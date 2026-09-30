@extends('layouts.auth')

@section('title', 'Login - Sistem Persuratan MPR RI')

@section('content')

<div class="logo-circle">
    <img src="{{ asset('mpr5.png') }}" alt="Logo MPR RI">
</div>

<h1 class="login-title">Login Siteman-Surat</h1>
<p class="login-subtitle">Majelis Permusyawaratan Rakyat Republik Indonesia</p>

  {{-- Notifikasi sukses (misal setelah registrasi) --}}
  @if (session('success'))
      <div class="alert alert-success text-start mb-3">
          <i class="bi bi-check-circle-fill me-2"></i>
          {{ session('success') }}
      </div>
  @endif

  {{-- Error validasi dari server --}}
  @if ($errors->any())
    <div class="alert alert-danger text-start">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="loginForm" action="{{ route('login.post') }}" method="post">
    @csrf

    {{-- Email --}}
    <div class="mb-3 text-start">
      <label for="email" class="form-label">Email</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          placeholder="Masukkan email"
          value="{{ old('email') }}"
          required
          autofocus
          autocomplete="email"
        >
      </div>
      @error('email')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3 text-start">
      <label for="password" class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input
          type="password"
          id="password"
          name="password"
          class="form-control"
          placeholder="Masukkan password"
          required
          autocomplete="current-password"
        >
      </div>
      @error('password')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Tombol Login --}}
    <button type="submit" id="loginBtn" class="btn btn-primary w-100">
      <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
      <span class="btn-text">Login</span>
    </button>

    <p class="footer mt-3">
      Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
    <p class="footer">&copy; {{ date('Y') }} Sekretariat Jenderal MPR RI</p>
  </form>
@endsection
