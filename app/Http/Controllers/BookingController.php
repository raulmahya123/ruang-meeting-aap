<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    /** List (harian) dengan filter room */
    public function index(Request $request)
    {
        $tz     = 'Asia/Jakarta';
        $dateQ  = $request->query('date', now($tz)->toDateString());
        $roomId = $request->query('room_id');

        // Window 1 hari (lokal) → konversi ke UTC untuk query overlap
        $dayStartLocal = Carbon::parse($dateQ, $tz)->startOfDay();
        $dayEndLocal   = $dayStartLocal->copy()->endOfDay();
        $dayStartUtc   = $dayStartLocal->copy()->timezone('UTC');
        $dayEndUtc     = $dayEndLocal->copy()->timezone('UTC');

        $rooms = Room::orderBy('name')->get();

        $bookings = Booking::with('room')
            ->when($roomId, fn($q) => $q->where('room_id', $roomId))
            // overlap: start < dayEnd AND end > dayStart
            ->where('start_at', '<', $dayEndUtc)
            ->where('end_at',   '>', $dayStartUtc)
            ->orderBy('start_at')
            ->get();

        // Kirim tanggal aslinya (string) untuk form/filter
        $date = $dateQ;

        return view('bookings.index', compact('rooms', 'bookings', 'date', 'roomId'));
    }

    /** Form create */
    public function create()
    {
        $rooms = Room::orderBy('name')->get();
        return view('bookings.create', compact('rooms'));
    }

    /** Simpan booking (input dianggap dari zona Asia/Jakarta) */
    public function store(Request $request)
    {
        $tz = 'Asia/Jakarta';

        $data = $request->validate([
            'room_id'         => ['required', 'exists:rooms,id'],
            'title'           => ['required', 'string', 'max:200'],
            'start_at'        => ['required', 'date'],
            'end_at'          => ['required', 'date', 'after:start_at'],
            'booked_by_name'  => ['required', 'string', 'max:120'],
            'booked_by_email' => ['nullable', 'email'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ]);

        // Parse input lokal → simpan UTC
        $startUtc = Carbon::parse($data['start_at'], $tz)->timezone('UTC');
        $endUtc   = Carbon::parse($data['end_at'],   $tz)->timezone('UTC');

        // Cek bentrok (di UTC)
        $overlap = Booking::where('room_id', $data['room_id'])
            ->where('start_at', '<', $endUtc)
            ->where('end_at',   '>', $startUtc)
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_at' => 'Jadwal bentrok dengan booking lain untuk ruangan ini.'
            ]);
        }

        $booking = Booking::create([
            'room_id'         => $data['room_id'],
            'title'           => $data['title'],
            'start_at'        => $startUtc,
            'end_at'          => $endUtc,
            'booked_by_name'  => $data['booked_by_name'],
            'booked_by_email' => $data['booked_by_email'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'cancel_token'    => Str::random(48),
        ]);

        return redirect()
            ->route('bookings.index', [
                'date'    => $booking->start_at->timezone($tz)->toDateString(),
                'room_id' => $booking->room_id,
            ])
            ->with('ok', 'Booking berhasil. Simpan token pembatalan: ' . $booking->cancel_token);
    }

    /** Cancel via token */
    public function cancelByToken(Request $request, string $token)
    {
        $tz = 'Asia/Jakarta';

        $booking = Booking::where('cancel_token', $token)->firstOrFail();
        $date    = $booking->start_at->timezone($tz)->toDateString();
        $roomId  = $booking->room_id;

        $booking->delete();

        return redirect()
            ->route('bookings.index', compact('date', 'roomId'))
            ->with('ok', 'Booking dibatalkan.');
    }

    /** Tampilan mingguan (grid) */
    public function week(Request $request)
    {
        $tz = 'Asia/Jakarta';

        // tanggal anchor (lokal)
        $dateLocal = Carbon::parse(
            $request->query('date', now($tz)->toDateString()),
            $tz
        );

        // range minggu (lokal)
        $weekStartLocal = $dateLocal->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEndLocal   = $weekStartLocal->copy()->addDays(7)->endOfDay();

        // untuk query ke DB (UTC)
        $weekStartUtc = $weekStartLocal->copy()->timezone('UTC');
        $weekEndUtc   = $weekEndLocal->copy()->timezone('UTC');

        $hourStart = (int) $request->query('hour_start', 7);
        $hourEnd   = (int) $request->query('hour_end', 20);
        if ($hourEnd <= $hourStart) $hourEnd = $hourStart + 1;

        $rooms  = Room::orderBy('name')->get();
        $roomId = $request->query('room_id');

        // Ambil booking yang overlap minggu ini (UTC)
        $bookings = Booking::with('room')
            ->when($roomId, fn($q) => $q->where('room_id', $roomId))
            ->where('start_at', '<', $weekEndUtc)
            ->where('end_at',   '>', $weekStartUtc)
            ->get();

        // Siapkan Senin–Minggu untuk grid (lokal)
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $weekStartLocal->copy()->addDays($i);
            $days[$d->toDateString()] = [
                'date'     => $d->toDateString(),
                'label'    => $d->translatedFormat('D, d M'),
                'is_today' => $d->isSameDay(now($tz)),
                'items'    => collect(),
            ];
        }

        // Tempel booking ke hari yang dilalui (render pakai lokal)
        foreach ($bookings as $b) {
            $bStartLocal = $b->start_at->copy()->timezone($tz);
            $bEndLocal   = $b->end_at->copy()->timezone($tz);

            $iter = $bStartLocal->copy()->startOfDay();
            $last = $bEndLocal->copy()->startOfDay();
            while ($iter->lte($last)) {
                $k = $iter->toDateString();
                if (isset($days[$k])) {
                    $days[$k]['items']->push($b);
                }
                $iter->addDay();
            }
        }

        // Jam baris grid. Biasanya sampai < $hourEnd biar jumlah baris pas
        $hours     = range($hourStart, $hourEnd - 1);
        $rowHeight = 48; // h-12 di Tailwind

        // Kompat: kirimkan juga key lama (weekStart/weekEnd/date) kalau Blade kamu masih pakai itu
        $weekStart = $weekStartLocal;
        $weekEnd   = $weekEndLocal;
        $date      = $dateLocal;

        return view('bookings.week', compact(
            // nama baru (lebih jelas)
            'dateLocal',
            'weekStartLocal',
            'weekEndLocal',
            // kompat lama
            'date',
            'weekStart',
            'weekEnd',
            // grid
            'days',
            'hours',
            'rowHeight',
            // filter
            'rooms',
            'roomId',
            'hourStart',
            'hourEnd'
        ));
    }
}
