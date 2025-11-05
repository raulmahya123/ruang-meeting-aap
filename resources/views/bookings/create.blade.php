{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Jadwal')

@section('content')
@php
  use Illuminate\Support\Str;

  // THEME TOKENS
  $btnFilled   = 'px-4 py-2 rounded-xl bg-[color:var(--brand-blue)] text-white border border-[color:var(--brand-blue)] hover:brightness-[1.05]';
  $btnOutline  = 'px-3 py-2 rounded-xl border border-[color:var(--brand-blue)] text-[color:var(--brand-blue)] bg-white hover:bg-blue-50';
  $chipMaroon  = 'inline-flex h-6 w-6 rounded-lg bg-[color:var(--brand-maroon)] text-white items-center justify-center text-[11px]';
  $labelBase   = 'block text-sm font-medium text-gray-700 mb-1';
  $inputBase   = 'w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]';
  $selectBase  = 'rounded-xl border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--brand-blue)] focus:border-[color:var(--brand-blue)]';
  $cardWrap    = 'bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden';
  $tz          = 'Asia/Jakarta';

  // Divisi (SESUAI ENUM MIGRASI)
  $divOptions = [
    'HR'  => 'Human Resources',
    'SCM' => 'Supply Chain',
    'ENG' => 'Engineering',
    'HSE' => 'Health, Safety & Environment',
    'OPS' => 'Operations',
    'FIN' => 'Finance',
    'IT'  => 'Information Technology',
    'PLT' => 'Plantat',
    'MGN' => 'Management',
  ];
  $selectedDiv = old('division', request('division'));

  // Prefill dari query/old: YYYY-MM-DDTHH:MM
  $startAtRaw = (string) old('start_at', request('start_at'));
  $endAtRaw   = (string) old('end_at',   request('end_at'));

  $startDatePref = $startAtRaw ? Str::of($startAtRaw)->substr(0,10) : '';
  $startHourPref = $startAtRaw ? Str::of($startAtRaw)->substr(11,2) : '';
  $startMinPref  = $startAtRaw ? Str::of($startAtRaw)->substr(14,2) : '';

  $endDatePref = $endAtRaw ? Str::of($endAtRaw)->substr(0,10) : '';
  $endHourPref = $endAtRaw ? Str::of($endAtRaw)->substr(11,2) : '';
  $endMinPref  = $endAtRaw ? Str::of($endAtRaw)->substr(14,2) : '';

  // List jam & menit untuk select (00..23, 00..59)
  $hours  = array_map(fn($n)=> str_pad((string)$n,2,'0',STR_PAD_LEFT), range(0,23));
  $minutes= array_map(fn($n)=> str_pad((string)$n,2,'0',STR_PAD_LEFT), range(0,59));
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
          <h1 class="text-xl sm:text-2xl font-semibold text-[color:var(--brand-maroon)]">Buat Jadwal</h1>
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

  <form method="post" action="{{ route('bookings.store') }}" class="space-y-6" id="booking-form" novalidate>
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

        {{-- Mulai: tanggal + (HH, MM) --}}
        <div>
          <label class="{{ $labelBase }}">Mulai ({{ $tz }})</label>
          <div class="grid grid-cols-5 gap-2">
            <input id="start_date" type="date" class="{{ $inputBase }} col-span-3"
                   value="{{ old('start_date') ?: $startDatePref }}" required>

            <select id="start_hour" class="{{ $selectBase }} col-span-1" required>
              <option value="">HH</option>
              @foreach($hours as $h)
                <option value="{{ $h }}" @selected(($startHourPref ?: old('start_hour')) === $h)>{{ $h }}</option>
              @endforeach
            </select>

            <select id="start_min" class="{{ $selectBase }} col-span-1" required>
              <option value="">MM</option>
              @foreach($minutes as $m)
                <option value="{{ $m }}" @selected(($startMinPref ?: old('start_min')) === $m)>{{ $m }}</option>
              @endforeach
            </select>
          </div>
          <p class="text-[11px] text-gray-500 mt-1">Format 24-jam, contoh <span class="font-mono">14:30</span>.</p>
          @error('start_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Selesai: tanggal + (HH, MM) --}}
        <div>
          <label class="{{ $labelBase }}">Selesai ({{ $tz }})</label>
          <div class="grid grid-cols-5 gap-2">
            <input id="end_date" type="date" class="{{ $inputBase }} col-span-3"
                   value="{{ old('end_date') ?: $endDatePref }}" required>

            <select id="end_hour" class="{{ $selectBase }} col-span-1" required>
              <option value="">HH</option>
              @foreach($hours as $h)
                <option value="{{ $h }}" @selected(($endHourPref ?: old('end_hour')) === $h)>{{ $h }}</option>
              @endforeach
            </select>

            <select id="end_min" class="{{ $selectBase }} col-span-1" required>
              <option value="">MM</option>
              @foreach($minutes as $m)
                <option value="{{ $m }}" @selected(($endMinPref ?: old('end_min')) === $m)>{{ $m }}</option>
              @endforeach
            </select>
          </div>
          <p class="text-[11px] text-gray-500 mt-1">Jika kosong, akan otomatis +60 menit dari waktu mulai.</p>
          @error('end_at') <p class="text-xs text-red-700 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Hidden untuk server --}}
        <input type="hidden" name="start_at" id="start_at" value="{{ old('start_at', request('start_at')) }}">
        <input type="hidden" name="end_at" id="end_at" value="{{ old('end_at', request('end_at')) }}">
      </div>
    </div>

    {{-- Card: Pemesan --}}
    <div class="{{ $cardWrap }}">
      <div class="px-4 sm:px-6 py-4 border-b flex items-center gap-2">
        <span class="{{ $chipMaroon }}">2</span>
        <h2 class="font-semibold text-gray-900">Data Pengguna</h2>
      </div>

      <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div>
          <label class="{{ $labelBase }}">Nama Pengguna</label>
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
        <button class="{{ $btnFilled }}">Simpan Jadwal</button>
      </div>
    </div>
  </form>
</div>

{{-- JS: gabung ke hidden; auto +60m; validasi end>=start; Ctrl/Cmd+Enter submit --}}
<script>
  const pad = n => String(n).padStart(2,'0');
  const pick = id => document.getElementById(id)?.value || '';

  function getDT(prefix){
    const d = pick(prefix + '_date');
    const h = pick(prefix + '_hour');
    const m = pick(prefix + '_min');
    if (!d || !h || !m) return null;
    // Build Date in local time
    return new Date(`${d}T${h}:${m}:00`);
  }

  function setDT(prefix, dateObj){
    const d = document.getElementById(prefix + '_date');
    const h = document.getElementById(prefix + '_hour');
    const m = document.getElementById(prefix + '_min');
    if (!dateObj) return;
    d.value = `${dateObj.getFullYear()}-${pad(dateObj.getMonth()+1)}-${pad(dateObj.getDate())}`;
    h.value = pad(dateObj.getHours());
    m.value = pad(dateObj.getMinutes());
  }

  function syncHidden(){
    const s = getDT('start');
    const e = getDT('end');
    if (s) document.getElementById('start_at').value =
      `${s.getFullYear()}-${pad(s.getMonth()+1)}-${pad(s.getDate())}T${pad(s.getHours())}:${pad(s.getMinutes())}`;
    if (e) document.getElementById('end_at').value =
      `${e.getFullYear()}-${pad(e.getMonth()+1)}-${pad(e.getDate())}T${pad(e.getHours())}:${pad(e.getMinutes())}`;
  }

  function autoEndPlus60IfEmpty(){
    const s = getDT('start'); if (!s) return;
    const hasEnd = !!(pick('end_date') && pick('end_hour') && pick('end_min'));
    if (!hasEnd){
      const e = new Date(s.getTime() + 60*60000);
      setDT('end', e);
    }
  }

  function ensureEndAfterStart(){
    const s = getDT('start'); const e = getDT('end');
    if (!s || !e) return;
    if (e <= s){
      const adj = new Date(s.getTime() + 60*60000);
      setDT('end', adj);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('booking-form');
    ['start_date','start_hour','start_min','end_date','end_hour','end_min'].forEach(id => {
      document.getElementById(id)?.addEventListener('change', () => {
        if (id.startsWith('start')) autoEndPlus60IfEmpty();
        ensureEndAfterStart();
        syncHidden();
      });
    });

    // Prefill awal (dari hidden old/request), tetap sinkron
    autoEndPlus60IfEmpty();
    ensureEndAfterStart();
    syncHidden();

    // Ctrl/Cmd + Enter = submit
    form.addEventListener('keydown', (ev) => {
      if ((ev.ctrlKey || ev.metaKey) && ev.key === 'Enter') {
        ev.preventDefault();
        syncHidden();
        form.submit();
      }
    });

    form.addEventListener('submit', () => syncHidden());
  });
</script>
@endsection
