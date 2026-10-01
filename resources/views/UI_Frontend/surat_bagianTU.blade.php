@extends('UI_Frontend.index')
@section('content')

<div class="hero-section">
    <div class="container">
        <h1>SITEMAN-SUCA</h1>
        <p class="lead">Sistem Informasi Temu-Kembali Pengiriman Surat Dengan Caraka</p>
    </div>
</div>

<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-md-4 d-flex">
    <div class="card-custom w-100 text-center" data-aos="zoom-in">
        <img src="{{ asset('anggota_mpr/setjen.jpg') }}" alt="H. Ahmad Muzani">
        <div class="card-body">
          <h5 class="fw-bold card-title">Bagian TU Pimpinan Setjen</h5>
          <p class="text-muted small card-subtitle">Bagian Tata Usaha</p>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="calendar-section">
    <div class="container">
        <div class="month-section">
            <div class="letter-card">

                {{-- Mengecek jika pengguna sudah login --}}
                @auth
                    @php
                        // Ambil nama role pengguna saat ini
                        $userRole = Auth::user()->role->nama_role;
                        // Tentukan role apa saja yang diizinkan
                        $allowedRoles = ['user', 'caraka', 'sekretariat', 'admin', 'bagian_tu'];
                    @endphp

                    {{-- Jika role pengguna ada di dalam daftar yang diizinkan --}}
                    @if(in_array($userRole, $allowedRoles))
                        <div class="letter-item">
                            <a href="{{ route('surat.bagianTU_biasa') }}">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Biasa</p>
                        </div>
                    @else
                        {{-- Jika role pengguna tidak diizinkan, buat link tidak bisa diklik --}}
                        <div class="letter-item">
                            <a href="#" onclick="return false;" style="cursor: not-allowed; color: grey;">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title" style="color: grey;">Surat Kategori Biasa</p>
                        </div>
                    @endif

                @else {{-- Jika pengguna belum login (guest) --}}
                    <div class="letter-item text-center">
                        <p>Silakan Login Untuk Melihat Surat</p>
                        <a href="{{ route('login') }}" class="btn btn-primary mt-2">Login</a>
                    </div>
                @endauth

            </div>
        </div>
    </div>
</div>

@endsection 