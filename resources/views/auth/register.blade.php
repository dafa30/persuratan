@extends('layouts.auth')

@section('title', 'Register - SITEMAN-SUCA')

@section('content')

<div class="logo-circle">
    <img src="{{ asset('mpr5.png') }}" alt="Logo MPR RI">
</div>

<h1 class="login-title">Register SITEMAN-SUCA</h1>
<p class="login-subtitle">Majelis Permusyawaratan Rakyat Republik Indonesia</p>

{{-- Modal sukses registrasi --}}
@if(session('register_success'))
    <div class="modal fade" id="registerSuccessModal" tabindex="-1"
         aria-labelledby="registerSuccessLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="registerSuccessLabel">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Registrasi Berhasil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    {{ session('success') ?? 'Akun berhasil dibuat. Silakan login.' }}
                </div>
                <div class="modal-footer border-0">
                    {{-- Pakai <a> supaya pasti redirect tanpa JS event --}}
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        Oke, saya login
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var modalEl = document.getElementById('registerSuccessModal');
            if (modalEl && window.bootstrap) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        })();
    </script>
    @endpush
@endif

@if ($errors->any())
<div class="alert alert-danger text-start">
  <ul class="mb-0">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form id="registerForm" action="{{ route('register.post') }}" method="post">
  @csrf

  <!-- Nama -->
  <div class="mb-3 text-start">
    <label for="name" class="form-label">Nama Lengkap</label>
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-person"></i></span>
      <input type="text" id="name" name="name" required class="form-control" placeholder="Masukkan nama lengkap">
    </div>
  </div>

  <!-- Email -->
  <div class="mb-3 text-start">
    <label for="email" class="form-label">Email</label>
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
      <input type="email" id="email" name="email" required class="form-control" placeholder="Masukkan email">
    </div>
  </div>

  <!-- Password -->
  <div class="mb-3 text-start">
    <label for="password" class="form-label">Password</label>
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-lock"></i></span>
      <input type="password" id="password" name="password" required class="form-control" placeholder="Masukkan password">
    </div>
  </div>

  <!-- Register button -->
  <button type="submit" id="registerBtn" class="btn btn-success">
    <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
    <span class="btn-text">Daftar</span>
  </button>

  <p class="footer mt-3">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
  <p class="footer">&copy; {{ date('Y') }} Sekretariat Jendral MPR RI</p>
</form>
@endsection
