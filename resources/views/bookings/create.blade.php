{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'New Booking')

@section('content')
@php
  // THEME TOKENS (pakai CSS var dari layout)
  $btnFilled   = 'px-4 py-2 rounded-xl bg-[color:var(--brand-blue)] text-white border border-[color:var(--brand-blue)] hover:brightness-[1.05)]';
  $btnOutline  = 'px-3 py-2 rounded-xl border border-[color:var(--brand-blue)] text-[color:var(--brand-blue)] bg-white hover:bg-blue-50';
  $chipMaroon  = 'inline-flex h-6 w-6 rounded-lg bg-[color:var(--brand-maroon)] text-white items-center justify-center text-[11px]';
  $labelBase   = 'block text-sm font-medium text-gray-700 mb-1';
  $inputBase   = 'w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]';
  $cardWrap    = 'bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden';
  $tz = 'Asia/Jakarta';

  // daftar divisi (sugesti), user tetap bisa ketik bebas
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
  $selectedDiv = old('division', request('division'));
@endphp

<div class="max-w-4xl mx-auto">
  {{-- Header sticky (solid, no gradient) --}}
  <div class="sticky top-0 z-10 -mx-2 sm:mx-0 mb-4 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/70">
    <div class="flex items-center justify-between py-3 px-2 sm:px-0">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-2xl bg-[color:var(--brand-maroon)] text-white grid place-items-center shadow">
          {{-- calendar icon (biru) --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[color:var(--brand-white)]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v3H2V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1Z"/>
            <path d="M22 11v6a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3v-6h20Z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-xl sm:text-2xl font-semibold text-[color:var(--brand-maroon)]">Create Booking</h1>
          <p class="text-xs text-gray-600">Lock your room slot in {{ $tz }}.</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">Back to Calendar</a>
      </div>
    </div>
    <div class="h-[2px] w-full bg-[color:var(--brand-maroon)]"></div>
  </div>

  {{-- Global errors --}}
  @if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 text-red-900 p-3 text-sm">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('bookings.store') }}" class="space-y-6" id="booking-form">
    @csrf

    {{-- Card: Essentials --}}
    <div class="{{ $cardWrap }}">
      <div class="px-4 sm:px-6 py-4 border-b flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="{{ $chipMaroon }}">1</span>
          <h2 class="font-semibold text-gray-900">Essentials</h2>
        </div>
        <div class="text-xs text-gray-500">Required fields</div>
      </div>

      <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="sm:col-span-2">
          <label class="{{ $labelBase }}">Room</label>
          <select name="room_id" class="{{ $inputBase }}" required>
            <option value="">— choose room —</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}" @selected(old('room_id', request('room_id'))==$r->id)>{{ $r->name }}</option>
            @endforeach
          </select>
          @error('room_id') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
          <label class="{{ $labelBase }}">Title</label>
          <input type="text" name="title"
                 value="{{ old('title') }}"
                 maxlength="200"
                 placeholder="e.g., Sprint Retro, Weekly Ops, Client Pitch"
                 class="{{ $inputBase }}" required>
          @error('title') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Start ({{ $tz }})</label>
          <div class="flex gap-2">
            <input id="start_at" type="datetime-local" name="start_at"
                   value="{{ old('start_at', request('start_at')) }}"
                   class="{{ $inputBase }}" required>
            <div class="hidden sm:flex gap-1">
            </div>
          </div>
          @error('start_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">End ({{ $tz }})</label>
          <input id="end_at" type="datetime-local" name="end_at"
                 value="{{ old('end_at', request('end_at')) }}"
                 class="{{ $inputBase }}" required>
          @error('end_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
          <p class="text-[11px] text-gray-500 mt-1">Auto-set to +60m after you tweak Start.</p>
        </div>
      </div>
    </div>

    {{-- Card: Booker --}}
    <div class="{{ $cardWrap }}">
      <div class="px-4 sm:px-6 py-4 border-b flex items-center gap-2">
        <span class="{{ $chipMaroon }}">2</span>
        <h2 class="font-semibold text-gray-900">Booker</h2>
      </div>

      <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div>
          <label class="{{ $labelBase }}">Booked by (name)</label>
          <input type="text" name="booked_by_name"
                 value="{{ old('booked_by_name') }}"
                 maxlength="120"
                 placeholder="Your name"
                 class="{{ $inputBase }}" required>
          @error('booked_by_name') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Division: dropdown yang bisa ngetik (datalist) --}}
        <div>
          <label class="{{ $labelBase }}">Division</label>
          <input list="division-list"
                 name="division"
                 value="{{ $selectedDiv }}"
                 placeholder="Pilih atau ketik baru…"
                 class="{{ $inputBase }}"
                 required>
          <datalist id="division-list">
            @foreach($divOptions as $code => $label)
              <option value="{{ $code }}">{{ $label }}</option>
            @endforeach
          </datalist>
          @error('division') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
          <label class="{{ $labelBase }}">Notes</label>
          <textarea name="notes" rows="4"
                    placeholder="Anything the team should know? (optional)"
                    class="{{ $inputBase }}">{{ old('notes') }}</textarea>
          @error('notes') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <div class="text-[11px] text-gray-500">
        Tip: tekan
        <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border">Ctrl</kbd>
        +
        <kbd class="px-1.5 py-0.5 rounded bg-gray-100 border">Enter</kbd>
        untuk menyimpan.
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">Cancel</a>
        <button class="{{ $btnFilled }}">Save Booking</button>
      </div>
    </div>
  </form>
</div>

{{-- Micro-interactions (no external lib) --}}
<script>
  const pad = n => String(n).padStart(2,'0');

  function parseLocal(dtStr){
    if(!dtStr) return null;
    const [d,t] = dtStr.split('T'); if(!t) return null;
    const [y,m,day] = d.split('-').map(Number);
    const [hh,mm] = t.split(':').map(Number);
    return new Date(y, (m-1), day, hh, mm, 0, 0);
  }
  function toLocalInput(d){
    if(!d) return '';
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }

  function uiSyncEnd(minutes = 60){
    const s = document.getElementById('start_at');
    const e = document.getElementById('end_at');
    const sd = parseLocal(s.value);
    if(!sd) return;
    const ed = new Date(sd.getTime() + minutes*60000);
    e.value = toLocalInput(ed);
  }

  function uiSetNow(){
    const now = new Date();
    const m = Math.round(now.getMinutes()/5)*5;
    now.setMinutes(m, 0, 0);
    document.getElementById('start_at').value = toLocalInput(now);
    uiSyncEnd(60);
  }

  function uiAddStart(mins){
    const s = document.getElementById('start_at');
    const d = parseLocal(s.value) || new Date();
    d.setMinutes(d.getMinutes() + mins, 0, 0);
    s.value = toLocalInput(d);
    uiSyncEnd(60);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const s = document.getElementById('start_at');
    const e = document.getElementById('end_at');
    if(s.value && !e.value){ uiSyncEnd(60); }

    s.addEventListener('change', () => {
      const sd = parseLocal(s.value);
      const ed = parseLocal(e.value);
      if(!ed || (sd && ed && ed <= sd)){ uiSyncEnd(60); }
    });

    document.getElementById('booking-form').addEventListener('keydown', (ev) => {
      if((ev.ctrlKey || ev.metaKey) && ev.key === 'Enter'){
        ev.preventDefault();
        ev.currentTarget.submit();
      }
    });
  });
</script>
@endsection
