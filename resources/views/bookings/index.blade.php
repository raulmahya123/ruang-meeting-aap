{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Bookings — Day')

@section('content')
@php
  use Illuminate\Support\Carbon;
  $tz = 'Asia/Jakarta';

  // BUTTON & UI TOKENS (pakai CSS variable dari layout -> solid, no gradient)
  $btnOutlineBlue = 'inline-flex items-center gap-2 rounded-full border border-[color:var(--brand-blue)] text-[color:var(--brand-blue)] px-4 py-2.5 hover:bg-blue-50 cta focus-ring';
  $btnFilledBlue  = 'inline-flex items-center gap-2 rounded-full bg-[color:var(--brand-blue)] text-white px-4 py-2.5 border border-[color:var(--brand-blue)] hover:brightness-[1.05] cta focus-ring';
  $btnFilledRed   = 'inline-flex items-center gap-2 rounded-full bg-[color:var(--brand-maroon)] text-white px-3 py-1.5 border border-[color:var(--brand-maroon)] hover:brightness-[1.05] cta focus-ring';
  $btnGhost       = 'inline-flex items-center gap-2 rounded-full border border-slate-300 text-slate-700 px-4 py-2.5 hover:bg-slate-50 cta focus-ring';

  $chip           = 'inline-flex items-center gap-1 rounded-full text-xs px-2 py-1 border';
  $card           = 'bg-white rounded-2xl border border-slate-200 shadow-sm';
  $label          = 'block text-sm font-medium text-slate-700 mb-1';
  $inputBase      = 'w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]';

  // Normalize $date (string → Carbon)
  $dateObj = ($date ?? null) instanceof Carbon
      ? $date->copy()
      : Carbon::parse($date ?? now($tz)->toDateString(), $tz);

  // Map label division (karena bookings.division = enum/string, bukan relasi)
  $divOptions = [
    'HR'  => 'Human Resources',
    'SCM' => 'Supply Chain',
    'ENG' => 'Engineering',
    'HSE' => 'Health, Safety & Environment',
    'OPS' => 'Operations',
    'FIN' => 'Finance',
    'IT'  => 'Information Technology',
    'PLT' => 'Plant',
    'MGN' => 'Management',
  ];
@endphp

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <div class="flex items-center gap-2">
    <a href="{{ route('bookings.index', ['date' => $dateObj->copy()->subDay()->toDateString(), 'room_id'=>$roomId ?? null]) }}"
       class="{{ $btnOutlineBlue }}">‹</a>
    <a href="{{ route('bookings.index', ['date' => now($tz)->toDateString(), 'room_id'=>$roomId ?? null]) }}"
       class="{{ $btnOutlineBlue }}">Today</a>
    <a href="{{ route('bookings.index', ['date' => $dateObj->copy()->addDay()->toDateString(), 'room_id'=>$roomId ?? null]) }}"
       class="{{ $btnOutlineBlue }}">›</a>

    <div class="ms-1 text-lg font-semibold text-slate-900">
      {{ $dateObj->translatedFormat('l, d M Y') }}
    </div>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('bookings.week', ['date'=>$dateObj->toDateString(),'room_id'=>$roomId ?? null]) }}" class="{{ $btnGhost }}">Week</a>
    <a href="{{ route('bookings.create') }}" class="{{ $btnFilledBlue }}">+ New</a>
  </div>
</div>

{{-- Filters --}}
<div class="{{ $card }} p-4 mb-4">
  <form method="get" action="{{ route('bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
      <label class="{{ $label }}">Tanggal</label>
      <input type="date" name="date" value="{{ $dateObj->toDateString() }}" class="{{ $inputBase }}">
    </div>
    <div>
      <label class="{{ $label }}">Ruangan</label>
      <select name="room_id" class="{{ $inputBase }}">
        <option value="">All rooms</option>
        @foreach($rooms as $r)
          <option value="{{ $r->id }}" @selected(($roomId ?? null)==$r->id)>{{ $r->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end">
      <button class="{{ $btnOutlineBlue }} w-full sm:w-auto">Apply</button>
    </div>
  </form>
</div>

@if(session('ok'))
  <div class="mb-4 p-3 rounded-xl border bg-green-50 border-green-200 text-green-800">
    {{ session('ok') }}
  </div>
@endif

{{-- List / Empty state --}}
@if($bookings->isEmpty())
  <div class="{{ $card }} p-6 text-center">
    <div class="text-2xl">🗓️</div>
    <p class="mt-2 text-slate-700 font-medium">No bookings for this day.</p>
    <p class="text-sm text-slate-500">Lock your slot before someone else does.</p>
    <div class="mt-4 flex items-center justify-center gap-2">
      <a href="{{ route('bookings.create', ['start_at'=>$dateObj->copy()->setTime(9,0)->format('Y-m-d\TH:i'), 'end_at'=>$dateObj->copy()->setTime(10,0)->format('Y-m-d\TH:i'), 'room_id'=>$roomId ?? null]) }}"
         class="{{ $btnFilledBlue }}">+ Create at 09:00</a>
      <a href="{{ route('bookings.week', ['date'=>$dateObj->toDateString(),'room_id'=>$roomId ?? null]) }}"
         class="{{ $btnGhost }}">Open Week</a>
    </div>
  </div>
@else
  <div class="{{ $card }}">
    <div class="p-3 border-b text-sm text-slate-600 flex items-center justify-between">
      <div>Found <span class="font-semibold text-slate-900">{{ $bookings->count() }}</span> booking(s)</div>
      <div class="hidden sm:flex items-center gap-2">
        @if($roomId)
          <span class="{{ $chip }} border-[color:var(--brand-blue)]/20 text-[color:var(--brand-blue)]">Room: {{ optional($rooms->firstWhere('id',$roomId))->name }}</span>
        @endif
        <span class="{{ $chip }} border-slate-200 text-slate-700">Date: {{ $dateObj->toDateString() }}</span>
      </div>
    </div>

    <div class="divide-y">
      @foreach($bookings as $b)
        @php
          $start = $b->start_at->timezone($tz);
          $end   = $b->end_at->timezone($tz);
          $token = $b->cancel_token ?? null;

          // Division label (fallback ke kode jika tidak terdaftar)
          $divisionCode  = $b->division ?? null;
          $divisionLabel = $divisionCode ? ($divOptions[$divisionCode] ?? $divisionCode) : null;
        @endphp
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-start gap-3 hover:bg-slate-50/60">
          <div class="sm:w-28 shrink-0 text-sm text-slate-600">
            <div class="font-semibold text-slate-900">{{ $start->format('H:i') }} — {{ $end->format('H:i') }}</div>
            <div class="text-xs text-slate-500">{{ $tz }}</div>
          </div>

          <div class="flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <div class="font-semibold text-slate-900">{{ $b->title }}</div>
                <div class="text-xs text-slate-600">
                  {{ $b->room?->name ?? '—' }} · by {{ $b->booked_by_name }}
                  @if($divisionLabel)
                    <span class="text-slate-500">(Division: {{ $divisionLabel }})</span>
                  @endif
                </div>
              </div>

              <div class="flex items-center gap-2">
                <a href="{{ route('bookings.index', ['date'=>$start->toDateString(), 'room_id'=>$roomId ?? null]) }}"
                   class="{{ $btnOutlineBlue }} text-sm">Open day</a>

                @if($token)
                  {{-- Cancel via token; confirm first --}}
                  <a href="{{ route('bookings.cancel', ['token'=>$token]) }}"
                     class="{{ $btnFilledRed }} text-sm"
                     onclick="return confirm('Batalkan booking \"{{ addslashes($b->title) }}\" pada {{ $start->format('d M Y H:i') }}?')">
                    Batal
                  </a>
                @endif
              </div>
            </div>

            @if($b->notes)
              <div class="text-sm mt-2 text-slate-700">{{ $b->notes }}</div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endif
@endsection
