{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Jadwal')

@section('content')
@php
  $btnFilled   = 'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[color:var(--brand-blue)] text-white border border-[color:var(--brand-blue)] hover:brightness-[1.05] font-medium text-sm shadow-sm';
  $btnOutline  = 'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 font-medium text-sm';
  $labelBase   = 'block text-sm font-medium text-gray-600 mb-1.5';
  $inputBase   = 'w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)]/20 focus:border-[color:var(--brand-blue)] focus:bg-white transition-all';
  $cardWrap    = 'bg-white rounded-2xl border border-gray-200/80 shadow-sm';
  $selectBase  = 'w-full rounded-xl border border-gray-200 bg-gray-50/50 py-2.5 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)]/20 focus:border-[color:var(--brand-blue)] focus:bg-white transition-all cursor-pointer';
  $iconLeft    = 'pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400';
  $iconRight   = 'pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400';
  $tz          = 'Asia/Jakarta';

  $divOptions = [
    'HRGA-IT' => 'HRGA-IT',
    'SCM' => 'Supply Chain',
    'ENG' => 'Engineering',
    'HSE' => 'Health, Safety & Environment',
    'OPS' => 'Operations',
    'FIN' => 'Finance',
    'PLT' => 'Plant',
    'MGN' => 'Management',
    'AST' => 'Asset'
  ];
  $selectedDiv = old('division', request('division'));
@endphp

<div class="max-w-lg mx-auto">
  {{-- Header --}}
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-lg font-semibold text-gray-900">Buat Jadwal</h1>
      <p class="text-sm text-gray-500">Zona waktu {{ $tz }}</p>
    </div>
    <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Kembali
    </a>
  </div>

  {{-- Error --}}
  @if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
      <div class="flex items-center gap-2 text-red-800 text-sm font-medium mb-1">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Ada kesalahan
      </div>
      <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('bookings.store') }}" id="booking-form" novalidate>
    @csrf

    {{-- Detail Jadwal --}}
    <div class="{{ $cardWrap }}">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Detail Jadwal</h2>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="{{ $labelBase }}">Ruangan</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <select name="room_id" class="{{ $selectBase }} pl-10 pr-10" required>
              <option value="" disabled selected>Pilih ruangan</option>
              @foreach($rooms as $r)
                <option value="{{ $r->id }}" @selected(old('room_id', request('room_id'))==$r->id)>{{ $r->name }}</option>
              @endforeach
            </select>
            <svg class="{{ $iconRight }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          @error('room_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Judul Acara</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="text" name="title"
                   value="{{ old('title') }}"
                   maxlength="200"
                   placeholder="Cth: Sprint Retro, Weekly Ops"
                   class="{{ $inputBase }} pl-10" required>
          </div>
          @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Mulai</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <input id="start_at_input" type="datetime-local" name="start_at"
                   value="{{ old('start_at', request('start_at')) }}"
                   class="{{ $inputBase }} pl-10" required>
          </div>
          @error('start_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Selesai</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <input id="end_at_input" type="datetime-local" name="end_at"
                   value="{{ old('end_at', request('end_at')) }}"
                   class="{{ $inputBase }} pl-10" required>
          </div>
          @error('end_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Data Pemesan --}}
    <div class="{{ $cardWrap }} mt-4">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Data Pemesan</h2>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="{{ $labelBase }}">Nama Lengkap</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" name="booked_by_name"
                   value="{{ old('booked_by_name') }}"
                   maxlength="120"
                   placeholder="Nama lengkap"
                   class="{{ $inputBase }} pl-10" required>
          </div>
          @error('booked_by_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Divisi</label>
          <div class="relative">
            <svg class="{{ $iconLeft }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <select name="division" class="{{ $selectBase }} pl-10 pr-10" required>
              <option value="" disabled selected>Pilih divisi</option>
              @foreach($divOptions as $code => $label)
                <option value="{{ $code }}" @selected($selectedDiv === $code)>{{ $label }} ({{ $code }})</option>
              @endforeach
            </select>
            <svg class="{{ $iconRight }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          @error('division') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
          <div class="relative">
            <svg class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            <textarea name="notes" rows="3"
                      placeholder="Info tambahan"
                      class="{{ $inputBase }} pl-10">{{ old('notes') }}</textarea>
          </div>
          @error('notes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Aksi --}}
    <div class="mt-5 flex items-center gap-3">
      <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Batal
      </a>
      <button class="{{ $btnFilled }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Simpan
      </button>
    </div>
  </form>
</div>

{{-- JS --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('booking-form');
    const start = document.getElementById('start_at_input');
    const end = document.getElementById('end_at_input');

    function validate() {
      if (start.value && end.value && end.value <= start.value) {
        const s = new Date(start.value);
        s.setMinutes(s.getMinutes() + 60);
        end.value = s.toISOString().slice(0, 16);
      }
    }

    start.addEventListener('change', validate);
    end.addEventListener('change', validate);
  });
</script>
@endsection