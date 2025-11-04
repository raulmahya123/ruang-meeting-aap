{{-- resources/views/bookings/week.blade.php --}}
@extends('layouts.app')

@section('title','Calendar (Week)')

@section('content')
@php
  use Illuminate\Support\Carbon;
  $tz = 'Asia/Jakarta';

  // Helper: pastikan Carbon (kalau string → parse Jakarta)
  $asCarbon = function ($v) use ($tz) {
      if ($v instanceof Carbon) return $v->copy();
      if (is_string($v) && $v !== '') return Carbon::parse($v, $tz);
      return null;
  };

  // Ambil dari controller (versi baru/lama) lalu normalisasi
  $weekStartLocal = $asCarbon($weekStartLocal ?? $weekStart) ?: Carbon::now($tz)->startOfWeek(Carbon::MONDAY)->startOfDay();
  $weekEndLocal   = $asCarbon($weekEndLocal   ?? $weekEnd)   ?: $weekStartLocal->copy()->addDays(7)->endOfDay();
  $dateLocal      = $asCarbon($dateLocal      ?? $date)      ?: Carbon::now($tz);

  // Jam & tinggi baris
  $rowH      = isset($rowHeight) ? (int)$rowHeight : 48; // px per jam
  $hourStart = isset($hourStart) ? (int)$hourStart : 7;
  $hourEnd   = isset($hourEnd)   ? (int)$hourEnd   : 20;
  if ($hourEnd <= $hourStart) { $hourEnd = $hourStart + 1; }
  $totalHours = max(1, $hourEnd - $hourStart);

  // Prev / Next
  $prev = $weekStartLocal->copy()->subWeek()->toDateString();
  $next = $weekStartLocal->copy()->addWeek()->toDateString();

  // Brand tokens (pakai CSS var dari layout)
  $btnOutlineBlue = 'inline-flex items-center gap-2 rounded-full border border-[color:var(--brand-blue)] text-[color:var(--brand-blue)] px-4 py-2.5 hover:bg-blue-50 cta focus-ring';
  $btnFilledBlue  = 'inline-flex items-center gap-2 rounded-full bg-[color:var(--brand-blue)] text-white px-4 py-2.5 border border-[color:var(--brand-blue)] hover:brightness-[1.05] cta focus-ring';
  $btnOutlineRed  = 'inline-flex items-center gap-2 rounded border border-[color:var(--brand-maroon)] text-[color:var(--brand-maroon)] px-3 py-2 hover:bg-red-50 cta focus-ring';
  $btnFilledRed   = 'inline-flex items-center gap-2 rounded bg-[color:var(--brand-maroon)] text-white px-3 py-2 border border-[color:var(--brand-maroon)] hover:brightness-[1.05] cta focus-ring';

  // Map label division (enum/string di kolom bookings.division)
  $divOptions = [
    'HR'  => 'Human Resources',
    'SCM' => 'Supply Chain',
    'ENG' => 'Engineering',
    'HSE' => 'Health, Safety & Environment',
    'OPS' => 'Operations',
    'FIN' => 'Finance',
    'IT'  => 'Information Technology',
    'MIN' => 'Mining',
  ];
@endphp

{{-- Toolbar --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-3">
  <div class="flex items-center gap-2">
    <a href="{{ route('bookings.week',['date'=>$prev,'room_id'=>$roomId ?? null,'hour_start'=>$hourStart,'hour_end'=>$hourEnd]) }}" class="{{ $btnOutlineBlue }}">‹</a>
    <a href="{{ route('bookings.week',['date'=>now($tz)->toDateString(),'room_id'=>$roomId ?? null,'hour_start'=>$hourStart,'hour_end'=>$hourEnd]) }}" class="{{ $btnOutlineBlue }}">Today</a>
    <a href="{{ route('bookings.week',['date'=>$next,'room_id'=>$roomId ?? null,'hour_start'=>$hourStart,'hour_end'=>$hourEnd]) }}" class="{{ $btnOutlineBlue }}">›</a>

    <div class="ms-1 text-lg font-semibold text-slate-900">
      {{ $weekStartLocal->translatedFormat('d M') }} — {{ $weekEndLocal->copy()->subDay()->translatedFormat('d M Y') }}
    </div>
  </div>

  <div class="flex items-center gap-2">
    <form method="get" action="{{ route('bookings.week') }}" class="flex flex-wrap items-center gap-2">
      <input type="date" name="date" value="{{ $weekStartLocal->toDateString() }}" class="rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]">
      <select name="room_id" class="rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]">
        <option value="">All rooms</option>
        @foreach($rooms as $r)
          <option value="{{ $r->id }}" @selected(($roomId ?? null)==$r->id)>{{ $r->name }}</option>
        @endforeach
      </select>
      <select name="hour_start" class="rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]">
        @for($h=0;$h<=23;$h++)
          <option value="{{ $h }}" @selected($hourStart==$h)>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</option>
        @endfor
      </select>
      <select name="hour_end" class="rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]">
        @for($h=1;$h<=24;$h++)
          <option value="{{ $h }}" @selected($hourEnd==$h)>{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</option>
        @endfor
      </select>
      <button type="submit" class="{{ $btnOutlineBlue }}">Apply</button>
    </form>

    <a href="{{ route('bookings.index',['date'=>$dateLocal->toDateString(),'room_id'=>$roomId ?? null]) }}" class="{{ $btnOutlineBlue }}">Day</a>
    <a href="{{ route('bookings.create') }}" class="{{ $btnFilledBlue }}">+ New</a>
  </div>
</div>

@if(session('ok'))
  <div class="mb-3 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 p-3 text-sm">{{ session('ok') }}</div>
@endif

{{-- Header hari --}}
<div class="grid grid-cols-8 rounded-t-2xl overflow-hidden border border-slate-200">
  <div class="bg-slate-50/80 px-2 py-2 text-xs text-slate-600 border-r border-slate-200">Time</div>
  @foreach($days as $d)
    @php
      $isToday = $d['is_today'];
      $isWeekend = in_array(Carbon::parse($d['date'],$tz)->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
    @endphp
    <div class="p-2 text-center border-r border-slate-200 {{ $isWeekend ? 'bg-slate-50' : 'bg-white' }}">
      <div class="flex items-center justify-center gap-2">
        <div class="font-medium {{ $isToday ? 'text-[color:var(--brand-blue)]' : 'text-slate-800' }}">{{ $d['label'] }}</div>
        @if($isToday)
          <span class="inline-flex items-center h-5 rounded-full px-2 text-[11px] bg-[color:var(--brand-blue)] text-white">Today</span>
        @endif
      </div>
      <div class="text-xs text-slate-500">{{ $d['date'] }}</div>
    </div>
  @endforeach
</div>

{{-- Grid body --}}
<div class="grid grid-cols-8 border-x border-b border-slate-200 rounded-b-2xl overflow-hidden">
  {{-- Kolom jam (kiri) --}}
  <div class="border-r border-slate-200 bg-slate-50/80">
    @for($h=$hourStart; $h<$hourEnd; $h++)
      <div class="h-12 border-b border-slate-200 text-xs text-slate-600 flex items-start">
        <div class="px-2 pt-1 font-medium text-slate-700">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</div>
      </div>
    @endfor
  </div>

  {{-- 7 kolom hari --}}
  @foreach($days as $dayKey => $d)
    @php
      $isWeekend = in_array(Carbon::parse($d['date'],$tz)->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
    @endphp
    <div class="relative border-r border-slate-200 bg-white {{ $isWeekend ? 'bg-slate-50' : 'bg-white' }} cw-day"
         data-day="{{ $d['date'] }}"
         onclick="CalendarWeek.clickColumn(event, '{{ $d['date'] }}', {{ (int)$hourStart }}, {{ (int)$hourEnd }}, {{ (int)$rowH }}, '{{ $roomId ?? '' }}')">

      {{-- Garis jam tipis --}}
      @for($h=$hourStart; $h<$hourEnd; $h++)
        <div class="absolute left-0 right-0 border-b border-slate-100/90" style="top: {{ ($h - $hourStart) * $rowH }}px; height: 0;"></div>
      @endfor
      {{-- Ketinggian kolom (sekali) --}}
      <div style="height: {{ $totalHours * $rowH }}px"></div>

      {{-- Events --}}
      @php
        /** @var \Illuminate\Support\Collection $items */
        $items = $d['items']->sortBy('start_at');

        // lane sederhana untuk overlap
        $lanes = []; // index => endMin
        $laneWidthPct = 100;
      @endphp

      @foreach($items as $b)
        @php
          $bs = $b->start_at->timezone($tz);
          $be = $b->end_at->timezone($tz);

          // clamp ke hari ini
          $dayStart = Carbon::parse($d['date'].' 00:00:00', $tz);
          $dayEnd   = $dayStart->copy()->endOfDay();

          $start = $bs->greaterThan($dayStart) ? $bs : $dayStart;
          $end   = $be->lessThan($dayEnd) ? $be : $dayEnd;

          // clamp ke window jam terlihat
          $visStart = $dayStart->copy()->setTime($hourStart,0,0);
          $visEnd   = $dayStart->copy()->setTime($hourEnd,0,0);
          if ($end->lte($visStart) || $start->gte($visEnd)) continue;

          if ($start->lt($visStart)) $start = $visStart;
          if ($end->gt($visEnd))     $end   = $visEnd;

          // posisi (px)
          $startMin = ($start->hour*60 + $start->minute) - ($hourStart*60);
          $endMin   = ($end->hour*60 + $end->minute)     - ($hourStart*60);
          $duration = max(15, $endMin - $startMin); // min 15px
          $topPx    = ($startMin/60) * $rowH;
          $heightPx = ($duration/60) * $rowH;

          // lane: cari lane yang available
          $laneIdx = 0;
          for ($i=0; $i<count($lanes); $i++) {
            if ($lanes[$i] <= $startMin) { $laneIdx = $i; break; }
            $laneIdx = $i + 1;
          }
          $lanes[$laneIdx] = $endMin;
          $laneWidthPct = 100 / max(1, count($lanes));
          $leftPct = $laneIdx * $laneWidthPct;

          $roomName = $b->room?->name;
          // bookings.division adalah string enum → tampilkan label bila dikenal
          $divisionCode  = $b->division ?? null;
          $divisionName  = $divisionCode ? ($divOptions[$divisionCode] ?? $divisionCode) : null;

          $payload = [
            "id"             => $b->id,
            "title"          => $b->title,
            "room"           => $roomName,
            "booked_by_name" => $b->booked_by_name,
            "division_name"  => $divisionName,
            "notes"          => $b->notes,
            "start_at"       => $start->format("Y-m-d\TH:i"),
            "end_at"         => $end->format("Y-m-d\TH:i"),
            "day_url"        => route("bookings.index", ["date"=>$start->toDateString(), "room_id"=>$roomId ?? null]),
          ];
        @endphp

        <button
          type="button"
          class="absolute text-left text-xs p-2 rounded-xl border cta focus-ring overflow-hidden group"
          style="top: {{ $topPx }}px; height: {{ $heightPx }}px; left: {{ $leftPct }}%; width: {{ $laneWidthPct }}%;
                 background-color: #fff7f7; border-color: color-mix(in oklab, var(--brand-maroon) 35%, #ffffff 65%);"
          onclick='CalendarWeek.openItem(@json($payload)); event.stopPropagation();'
        >
          <div class="flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full bg-[color:var(--brand-maroon)]"></span>
            <div class="font-semibold text-slate-900 truncate">{{ $b->title }}</div>
          </div>
          <div class="mt-0.5 text-[11px] text-slate-600 truncate">
            <span class="text-[color:var(--brand-maroon)] font-medium">{{ $start->format('H:i') }}–{{ $end->format('H:i') }}</span>
            @if($roomName) · {{ $roomName }} @endif
            @if($divisionName) · {{ $divisionName }} @endif
          </div>

          {{-- Hover affordance (solid, no gradient) --}}
          <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity"
               style="box-shadow: inset 0 0 0 1px var(--brand-maroon), 0 0 0 2px color-mix(in oklab, var(--brand-maroon) 20%, transparent); border-radius: 0.75rem;"></div>
        </button>
      @endforeach

      {{-- garis waktu "sekarang" (klik = create now) --}}
      @if($d['is_today'])
        @php
          $now = now($tz);
          $nowMin = ($now->hour*60 + $now->minute) - ($hourStart*60);
          $nowTop = ($nowMin >= 0 && $nowMin <= ($totalHours*60)) ? ($nowMin/60)*$rowH : null;
        @endphp
        @if(!is_null($nowTop))
          <button type="button"
                  title="Create booking at current time"
                  onclick="CalendarWeek.createAt('{{ $d['date'] }}', {{ (int)$now->hour }}, {{ (int)$now->minute }}, '{{ $roomId ?? '' }}'); event.stopPropagation();"
                  class="absolute left-0 right-0 focus:outline-none"
                  style="top: {{ $nowTop }}px;">
            <div class="relative h-0.5 bg-[color:var(--brand-blue)]">
              <span class="absolute -top-1.5 left-2 h-3 w-3 rounded-full bg-[color:var(--brand-blue)]"></span>
            </div>
          </button>
        @endif
      @endif
    </div>
  @endforeach
</div>

{{-- MODAL DETAIL (vanilla JS) --}}
<div id="cw-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
  <div class="absolute inset-0 bg-black/40" onclick="CalendarWeek.close()"></div>
  <div class="relative w-full sm:max-w-lg bg-white rounded-2xl shadow-xl m-2 sm:m-4 overflow-hidden">
    <div class="px-4 py-3 border-b flex items-center justify-between">
      <h3 id="cw-title" class="font-semibold text-lg truncate text-[color:var(--brand-maroon)]">Booking</h3>
      <button type="button" class="p-1 rounded hover:bg-slate-100" onclick="CalendarWeek.close()">✕</button>
    </div>

    <div class="p-4 space-y-3 text-sm">
      <div class="flex items-center gap-2">
        <div class="w-24 text-slate-500">Waktu</div>
        <div>
          <span id="cw-start">-</span> – <span id="cw-end">-</span> ({{ $tz }})
        </div>
      </div>

      <div class="flex items-center gap-2">
        <div class="w-24 text-slate-500">Ruangan</div>
        <div id="cw-room">-</div>
      </div>

      <div class="flex items-start gap-2">
        <div class="w-24 text-slate-500">Pemesan</div>
        <div>
          <div id="cw-by-name">-</div>
          <div id="cw-division" class="text-slate-600"></div>
        </div>
      </div>

      <div class="flex items-start gap-2" id="cw-notes-wrap" style="display:none;">
        <div class="w-24 text-slate-500">Catatan</div>
        <div id="cw-notes" class="whitespace-pre-wrap"></div>
      </div>
    </div>

    <div class="px-4 py-3 border-t flex items-center justify-between">
      <div class="text-xs text-slate-500">
        ID: <span id="cw-id">-</span>
      </div>
      <div class="flex items-center gap-2">
        <a id="cw-day-url" href="#" class="{{ $btnOutlineBlue }}">Lihat di Day</a>
        <button type="button" class="{{ $btnFilledRed }}" onclick="CalendarWeek.close()">Tutup</button>
      </div>
    </div>
  </div>
</div>

<style>
  /* modal toggle */
  #cw-modal.show { display:flex; }
</style>

<script>
  // Util: format "YYYY-MM-DDTHH:mm" -> "HH:mm"
  function cwFormatTime(isoLike){
    if(!isoLike) return '-';
    try {
      const t = isoLike.split('T')[1] || '';
      const [hh,mm] = t.split(':');
      if(!hh || !mm) return isoLike;
      return `${hh}:${mm}`;
    } catch(e){ return isoLike; }
  }

  // Controller UI (tanpa framework)
  const CalendarWeek = {
    modal: null,
    els: {},
    openItem(payload){
      if(!this.modal){ this._init(); }
      const p = payload || {};
      this.els.title.textContent   = p.title || 'Booking';
      this.els.start.textContent   = cwFormatTime(p.start_at);
      this.els.end.textContent     = cwFormatTime(p.end_at);
      this.els.room.textContent    = p.room || '-';
      this.els.byName.textContent  = p.booked_by_name || '-';
      this.els.division.textContent = p.division_name ? `Divisi: ${p.division_name}` : '';
      this.els.id.textContent      = p.id ?? '-';

      if(p.notes && String(p.notes).trim() !== ''){
        this.els.notesWrap.style.display = '';
        this.els.notes.textContent = p.notes;
      } else {
        this.els.notesWrap.style.display = 'none';
        this.els.notes.textContent = '';
      }

      if(p.day_url){ this.els.dayUrl.href = p.day_url; }

      this.modal.classList.add('show');
      document.body.classList.add('overflow-hidden');
    },
    close(){
      if(!this.modal){ this._init(); }
      this.modal.classList.remove('show');
      document.body.classList.remove('overflow-hidden');
    },
    clickColumn(evt, dayISO, hourStart, hourEnd, rowH, roomId){
      try{
        // Hindari trigger saat klik event
        if (evt.target.closest('button')) return;

        const rect = evt.currentTarget.getBoundingClientRect();
        const y = evt.clientY - rect.top;

        const minutesFromStart = Math.max(0, Math.min((hourEnd-hourStart)*60, Math.round((y/rowH)*60)));
        const absMinutes = hourStart*60 + minutesFromStart;

        // bulatkan ke 30 menit
        const rounded = Math.round(absMinutes/30)*30;
        const hh = Math.floor(rounded/60);
        const mm = rounded%60;

        this.createAt(dayISO, hh, mm, roomId);
      }catch(e){ console.error(e); }
    },
    createAt(dayISO, hh, mm, roomId){
      // default durasi 60 menit
      const pad = n => String(n).padStart(2,'0');
      const start = `${dayISO}T${pad(hh)}:${pad(mm)}`;
      const endMinutes = (hh*60 + mm) + 60;
      const eh = Math.floor(endMinutes/60);
      const em = endMinutes%60;
      const end = `${dayISO}T${pad(eh)}:${pad(em)}`;

      const params = new URLSearchParams();
      if(roomId) params.set('room_id', roomId);
      params.set('start_at', start);
      params.set('end_at', end);

      window.location.href = `{{ route('bookings.create') }}?` + params.toString();
    },
    _init(){
      this.modal          = document.getElementById('cw-modal');
      this.els.title      = document.getElementById('cw-title');
      this.els.start      = document.getElementById('cw-start');
      this.els.end        = document.getElementById('cw-end');
      this.els.room       = document.getElementById('cw-room');
      this.els.byName     = document.getElementById('cw-by-name');
      this.els.division   = document.getElementById('cw-division');
      this.els.notesWrap  = document.getElementById('cw-notes-wrap');
      this.els.notes      = document.getElementById('cw-notes');
      this.els.id         = document.getElementById('cw-id');
      this.els.dayUrl     = document.getElementById('cw-day-url');

      window.addEventListener('keydown', (e) => {
        if(e.key === 'Escape'){ this.close(); }
      }, { passive: true });
    }
  };
</script>
@endsection
