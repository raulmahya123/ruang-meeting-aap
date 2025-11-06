{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Masuk')

@push('head')
<style>
  /* fallback kalau token belum ada di layout */
  :root{
    --brand-maroon: var(--brand-maroon, #7a1023);
    --brand-blue:   var(--brand-blue,   #1252cc);
  }
</style>
@endpush

@section('content')
  <div class="min-h-[70vh] grid place-items-center py-8">
    <div class="w-full max-w-md">
      {{-- Status session (mis. "Password reset link sent") --}}
      @if (session('status'))
        <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
          {{ session('status') }}
        </div>
      @endif

      <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        {{-- Header Card --}}
        <div class="px-6 py-5 border-b bg-[color:var(--brand-maroon)] text-white">
          <div class="flex items-center gap-3">
            {{-- Logo kecil (optional) --}}
            <div class="shrink-0 w-10 h-10 rounded-xl bg-white/10 grid place-items-center ring-1 ring-white/15">
              {{-- titik biru pada maroon box, senada favicon --}}
              <svg viewBox="0 0 64 64" class="w-6 h-6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect rx="12" width="64" height="64" fill="rgba(255,255,255,0.25)"/>
                <circle cx="46" cy="18" r="8" fill="rgba(255,255,255,0.95)"/>
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-semibold leading-tight">Masuk ke Ruang Andalan</h1>
              <p class="text-white/80 text-sm">Atur jadwal meeting dengan lebih tertib & seragam.</p>
            </div>
          </div>
        </div>

        {{-- Body Form --}}
        <div class="px-6 py-6">
          <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ show: false }">
            @csrf

            {{-- Email --}}
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                inputmode="email"
                autocomplete="username"
                required
                value="{{ old('email') }}"
                class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[color:var(--brand-blue)] focus:ring-[color:var(--brand-blue)]"
                placeholder="nama@andalan.co.id"
              />
              @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Password --}}
            <div>
              <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-[color:var(--brand-blue)] hover:underline">
                  Lupa kata sandi?
                </a>
                @endif
              </div>
              <div class="mt-1 relative">
                <input
                  :type="show ? 'text' : 'password'"
                  id="password"
                  name="password"
                  required
                  autocomplete="current-password"
                  class="block w-full rounded-xl border-gray-300 pr-11 focus:border-[color:var(--brand-blue)] focus:ring-[color:var(--brand-blue)]"
                  placeholder="••••••••"
                />
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 pr-3 grid place-items-center text-gray-500 hover:text-gray-700"
                        aria-label="Toggle show password">
                  <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.21.07.434 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z" />
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c1.658 0 3.232-.356 4.646-1M21 21L3 3" />
                  </svg>
                </button>
              </div>
              @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center justify-between">
              <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox"
                  class="rounded border-gray-300 text-[color:var(--brand-blue)] shadow-sm focus:ring-[color:var(--brand-blue)]">
                <span class="text-sm text-gray-700">Ingat saya</span>
              </label>
              {{-- Optional link ke register jika perlu --}}
              @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 underline decoration-[color:var(--brand-blue)]/30">
                  Daftar akun
                </a>
              @endif
            </div>

            {{-- Submit --}}
            <div>
              <button type="submit"
                      class="w-full px-4 py-2.5 rounded-xl font-medium
                             bg-[color:var(--brand-blue)] text-white
                             hover:brightness-[1.05] focus-visible:outline-none
                             focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[color:var(--brand-blue)]">
                Masuk
              </button>
            </div>
          </form>

          {{-- Divider optional SSO --}}
          {{-- 
          <div class="my-6 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-xs text-gray-500">atau</span>
            <div class="h-px flex-1 bg-gray-200"></div>
          </div>
          <div class="grid gap-2">
            <a href="{{ route('oauth.redirect','google') }}"
               class="w-full px-4 py-2.5 rounded-xl border text-sm hover:bg-gray-50">
              Masuk dengan Google
            </a>
          </div>
          --}}
        </div>

        {{-- Footer kecil --}}
        <div class="px-6 py-4 border-t bg-gray-50 text-xs text-gray-500">
          Dengan masuk, Anda setuju pada ketentuan dan kebijakan privasi Andalan Group.
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
{{-- Alpine untuk toggle password; kalau sudah ada Alpine di app.js, ini tidak wajib --}}
<script>
  document.addEventListener('alpine:init', () => {});
</script>
@endpush
