{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Ruang Andalan')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')

  {{-- Favicon (maroon box + titik biru) --}}
  <link rel="icon" href="data:image/svg+xml,
  %3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E
    %3Crect rx='14' width='64' height='64' fill='%237a1023'/%3E
    %3Ccircle cx='48' cy='16' r='8' fill='%231252cc'/%3E
  %3C/svg%3E" />

  <style>
    /* === Brand: solid, no gradient, high-contrast (light only) === */
    :root{
      --brand-maroon:#7A1023;
      --brand-maroon-ink:#5d0c1a;
      --brand-blue:#1252CC;
      --brand-blue-ink:#0d3e99;
      --brand-white:#ffffff;

      --ink:#0b1220;
      --ink-2:#344155;
      --paper:#ffffff;
      --line:#e6e8ee;

      --ring: color-mix(in oklab, var(--brand-blue) 35%, transparent);
    }

    .cta{
      transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease, color .12s ease, border-color .12s ease, opacity .12s ease;
      will-change: transform;
    }
    .cta:active{ transform: translateY(1px) scale(.986); }

    .cta-kbd{
      padding:.1rem .35rem;border-radius:.375rem;
      border:1px solid var(--line); background:#f8fafc;
      font-variant-numeric: tabular-nums;
    }

    .focus-ring:focus{ outline:3px solid var(--ring); outline-offset:2px; }

    /* Buttons */
    .btn-outline{
      border:1px solid var(--brand-blue);
      color:var(--brand-blue);
      background:var(--brand-white);
    }
    .btn-outline:hover{ background:#eef4ff; }

    .btn-filled{
      background:var(--brand-blue);
      color:var(--brand-white);
      border:1px solid var(--brand-blue);
    }
    .btn-filled:hover{ background: color-mix(in oklab, var(--brand-blue) 92%, black 8%); }

    .btn-ghost-white{
      color:var(--brand-white);
      background:transparent;
    }
    .btn-ghost-white:hover{ background: rgba(255,255,255,.08); }

    /* Active underline indicator (unik) */
    .nav-active{ position: relative; isolation: isolate; }
    .nav-active::after{
      content:""; position:absolute; inset:auto 14px -8px 14px;
      height:4px; border-radius:999px; background:var(--brand-blue); z-index:1;
    }
  </style>
</head>
<body class="min-h-screen bg-[var(--paper)] text-[var(--ink)] antialiased">

  <!-- Skip to content -->
  <a href="#main" class="sr-only focus:not-sr-only focus-ring inline-block px-3 py-2 m-2 rounded bg-white">
    Lewati ke konten
  </a>

  <!-- NAVBAR: maroon solid, ikon & tombol biru -->
  <header class="sticky top-0 z-40 border-b border-slate-200/20 bg-[color:var(--brand-maroon)] text-white">
    <div class="max-w-7xl mx-auto px-4">
      <div class="h-16 flex items-center justify-between gap-3">

        <!-- Brand -->
        <a href="{{ route('bookings.week') }}" class="focus-ring group flex items-center gap-3">
          <div class="h-9 w-9 rounded-2xl grid place-items-center shadow-sm ring-1 ring-black/5 bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[color:var(--brand-blue)]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M7 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a3 3 0 013 3v3H2V7a3 3 0 013-3h1V3a1 1 0 011-1Z"/>
              <path d="M22 11v6a3 3 0 01-3 3H5a3 3 0 01-3-3v-6h20Z"/>
            </svg>
          </div>
          <div class="leading-tight">
            <div class="font-semibold tracking-tight">Ruang Andalan</div>
          </div>
        </a>

        @php
          $isWeek   = request()->routeIs('bookings.week');
          $isIndex  = request()->routeIs('bookings.index');
          $isCreate = request()->routeIs('bookings.create');

          $base = 'inline-flex items-center gap-2 rounded-full px-4 py-2.5 cta focus-ring';
          $blueOutline = 'btn-outline';
          $blueFilled  = 'btn-filled';
          $icon  = 'h-4 w-4 text-[color:var(--brand-blue)]';
        @endphp

        <!-- Desktop nav -->
        <nav class="hidden md:flex items-center gap-1">
          <a href="{{ route('bookings.week') }}"
             class="{{ $base }} {{ $isWeek ? 'btn-ghost-white nav-active' : 'btn-ghost-white' }}"
             aria-current="{{ $isWeek ? 'page' : 'false' }}">
            <svg class="{{ $icon }} text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a3 3 0 013 3v3H2V7a3 3 0 013-3h1V3a1 1 0 011-1Z"/><path d="M22 11v6a3 3 0 01-3 3H5a3 3 0 01-3-3v-6h20Z"/></svg>
            Kalender
          </a>
          <a href="{{ route('bookings.index') }}"
             class="{{ $base }} {{ $isIndex ? 'btn-ghost-white nav-active' : 'btn-ghost-white' }}"
             aria-current="{{ $isIndex ? 'page' : 'false' }}">
            <svg class="{{ $icon }} text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5h18v2H3zM3 11h12v2H3zM3 17h18v2H3z"/></svg>
            Jadwal
          </a>
          <a href="{{ route('bookings.create') }}"
             class="{{ $base }} {{ $blueFilled }}"
             aria-current="{{ $isCreate ? 'page' : 'false' }}">
            <svg class="{{ $icon }} text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 4h2v6h6v2h-2v6h-2v-6H5v-2h6z"/></svg>
            Buat Jadwal
          </a>
        </nav>

        <!-- Actions kanan: AUTH -->
        <div class="flex items-center gap-2">
          @auth
            <div class="hidden md:flex items-center gap-2">
              <span class="text-sm text-white/90">Hi, <strong>{{ auth()->user()->name }}</strong></span>
              {{-- optional: link profile breeze --}}
              @if(Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="{{ $base }} btn-ghost-white text-sm">Profil</a>
              @endif>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="{{ $base }} btn-outline text-sm bg-white">Logout</button>
              </form>
            </div>
          @endauth

          @guest
            <div class="hidden md:flex items-center gap-2">
              @if(Route::has('login'))
                <a href="{{ route('login') }}" class="{{ $base }} btn-outline text-sm bg-white">Login</a>
              @endif
            </div>
          @endguest

          <!-- Hamburger -->
          <button type="button" class="md:hidden focus-ring inline-flex items-center justify-center h-10 w-10 rounded-full cta btn-outline"
                  aria-controls="mobile-menu" aria-expanded="false" onclick="toggleMobileMenu()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M3 6h14M3 10h14M3 14h14"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <div id="mobile-menu" class="md:hidden hidden pb-4">
        <div class="grid gap-2">
          <a href="{{ route('bookings.week') }}"   class="{{ $base }} {{ $isWeek ? 'btn-filled' : 'btn-outline' }}">Kalender</a>
          <a href="{{ route('bookings.index') }}"  class="{{ $base }} {{ $isIndex ? 'btn-filled' : 'btn-outline' }}">Jadwal</a>
          <a href="{{ route('bookings.create') }}" class="{{ $base }} btn-filled">Buat Jadwal</a>
        </div>

        <div class="mt-3 border-t border-white/20 pt-3 grid gap-2">
          @auth
            <div class="text-sm text-white/90">Hi, <strong>{{ auth()->user()->name }}</strong></div>
            @if(Route::has('profile.edit'))
              <a href="{{ route('profile.edit') }}" class="{{ $base }} btn-ghost-white">Profil</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="{{ $base }} btn-outline bg-white">Logout</button>
            </form>
          @endauth

          @guest
            @if(Route::has('login'))
              <a href="{{ route('login') }}" class="{{ $base }} btn-outline bg-white">Login</a>
            @endif
            @if(Route::has('register'))
              <a href="{{ route('register') }}" class="{{ $base }} btn-filled">Register</a>
            @endif
          @endguest
        </div>
      </div>
    </div>
  </header>

  <!-- Flash -->
  <div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('ok'))
      <div class="mb-4 p-3 rounded-xl border bg-green-50 border-green-200 text-green-800">
        {{ session('ok') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-4 p-3 rounded-xl border bg-red-50 border-red-200 text-red-800">
        <ul class="list-disc ps-5 space-y-1">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif
  </div>

  <!-- Main -->
  <main id="main" class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="mt-10 border-t border-[color:var(--line)] bg-[var(--paper)]">
    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
      <div class="text-sm text-[var(--ink-2)]">
        © {{ date('Y') }} • <strong>Ruang Andalan</strong>
      </div>
    </div>
  </footer>

  @stack('modals')
  @stack('scripts')

  <script>
    // Mobile menu
    function toggleMobileMenu(){
      const el = document.getElementById('mobile-menu');
      const shown = el.classList.toggle('hidden') ? false : true;
      const buttons = document.querySelectorAll('button[onclick^="toggleMobileMenu"]');
      buttons.forEach(b => b.setAttribute('aria-expanded', shown ? 'true' : 'false'));
    }

    // Ctrl+Enter → submit form terdekat
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const targetForm = document.querySelector('form[data-ctrl-enter="submit"], main form');
        if (targetForm) {
          const submit = targetForm.querySelector('button[type="submit"], [type="submit"]');
          submit ? submit.click() : targetForm.submit();
        }
      }
    });
  </script>
</body>
</html>
