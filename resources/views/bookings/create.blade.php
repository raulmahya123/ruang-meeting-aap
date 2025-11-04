{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Baru')

@section('content')
@php
  // THEME TOKENS
  $btnFilled   = 'px-4 py-2 rounded-xl bg-[color:var(--brand-blue)] text-white border border-[color:var(--brand-blue)] hover:brightness-[1.05]';
  $btnOutline  = 'px-3 py-2 rounded-xl border border-[color:var(--brand-blue)] text-[color:var(--brand-blue)] bg-white hover:bg-blue-50';
  $chipMaroon  = 'inline-flex h-6 w-6 rounded-lg bg-[color:var(--brand-maroon)] text-white items-center justify-center text-[11px]';
  $labelBase   = 'block text-sm font-medium text-gray-700 mb-1';
  $inputBase   = 'w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]';
  $cardWrap    = 'bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden';
  $tz = 'Asia/Jakarta';

  // Divisi (sugesti)
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
  {{-- Header --}}
  <div class="sticky top-0 z-10 -mx-2 sm:mx-0 mb-4 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/70">
    <div class="flex items-center justify-between py-3 px-2 sm:px-0">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-2xl bg-[color:var(--brand-maroon)] text-white grid place-items-center shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[color:var(--brand-white)]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v3H2V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1Z"/>
            <path d="M22 11v6a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3v-6h20Z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-xl sm:text-2xl font-semibold text-[color:var(--brand-maroon)]">Buat Booking</h1>
          <p class="text-xs text-gray-600">Zona waktu: {{ $tz }}.</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">Kembali ke Kalender</a>
      </div>
    </div>
    <div class="h-[2px] w-full bg-[color:var(--brand-maroon)]"></div>
  </div>

  {{-- Error global --}}
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

    {{-- Card: Detail Utama --}}
    <div class="{{ $cardWrap }}">
      <div class="px-4 sm:px-6 py-4 border-b flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="{{ $chipMaroon }}">1</span>
          <h2 class="font-semibold text-gray-900">Detail Utama</h2>
        </div>
        <div class="text-xs text-gray-500">Wajib diisi</div>
      </div>

      <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="sm:col-span-2">
          <label class="{{ $labelBase }}">Ruangan</label>
          <select name="room_id" class="{{ $inputBase }}" required>
            <option value="">— pilih ruangan —</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}" @selected(old('room_id', request('room_id'))==$r->id)>{{ $r->name }}</option>
            @endforeach
          </select>
          @error('room_id') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
          <label class="{{ $labelBase }}">Judul</label>
          <input type="text" name="title"
                 value="{{ old('title') }}"
                 maxlength="200"
                 placeholder="Contoh: Sprint Retro, Weekly Ops, Presentasi Client"
                 class="{{ $inputBase }}" required>
          @error('title') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Mulai & Selesai (satu field masing2, 24 jam, tanpa AM/PM) --}}
        <div>
          <label class="{{ $labelBase }}">Mulai ({{ $tz }})</label>
          <input id="start_at" type="datetime-local" name="start_at"
                 value="{{ old('start_at', request('start_at')) }}"
                 class="{{ $inputBase }}" required>
          @error('start_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Selesai ({{ $tz }})</label>
          <input id="end_at" type="datetime-local" name="end_at"
                 value="{{ old('end_at', request('end_at')) }}"
                 class="{{ $inputBase }}" required>
          @error('end_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
          <p class="text-[11px] text-gray-500 mt-1">Akan otomatis diisi +60 menit setelah “Mulai” diubah.</p>
        </div>
      </div>
    </div>

    {{-- Card: Pemesan --}}
    <div class="{{ $cardWrap }}">
      <div class="px-4 sm:px-6 py-4 border-b flex items-center gap-2">
        <span class="{{ $chipMaroon }}">2</span>
        <h2 class="font-semibold text-gray-900">Data Pembooking</h2>
      </div>

      <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div>
          <label class="{{ $labelBase }}">Nama Pembooking</label>
          <input type="text" name="booked_by_name"
                 value="{{ old('booked_by_name') }}"
                 maxlength="120"
                 placeholder="Nama lengkap"
                 class="{{ $inputBase }}" required>
          @error('booked_by_name') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="{{ $labelBase }}">Divisi</label>
          <input list="division-list"
                 name="division"
                 value="{{ $selectedDiv }}"
                 placeholder="Pilih atau ketik divisi…"
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
          <label class="{{ $labelBase }}">Catatan</label>
          <textarea name="notes" rows="4"
                    placeholder="Info tambahan (opsional)"
                    class="{{ $inputBase }}">{{ old('notes') }}</textarea>
          @error('notes') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Aksi --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <a href="{{ route('bookings.week') }}" class="{{ $btnOutline }}">Batal</a>
        <button class="{{ $btnFilled }}">Simpan Booking</button>
      </div>
    </div>
  </form>
</div>

{{-- Interaksi kecil: auto +60m & validasi sederhana --}}
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

  function autoEndPlus60(){
    const s = document.getElementById('start_at');
    const e = document.getElementById('end_at');
    const sd = parseLocal(s.value);
    if(!sd) return;
    const ed = new Date(sd.getTime() + 60*60000);
    if(!e.value || parseLocal(e.value) <= sd){
      e.value = toLocalInput(ed);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('booking-form');
    const s = document.getElementById('start_at');
    const e = document.getElementById('end_at');

    // Prefill: kalau start ada dan end kosong → +60m
    if(s.value && !e.value){ autoEndPlus60(); }

    s.addEventListener('change', autoEndPlus60);

    // Ctrl/Cmd + Enter = submit
    form.addEventListener('keydown', (ev) => {
      if((ev.ctrlKey || ev.metaKey) && ev.key === 'Enter'){
        ev.preventDefault();
        form.submit();
      }
    });
  });
</script>
@endsection
