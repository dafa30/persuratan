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
        <img src="{{ asset('anggota_mpr/ahmad_muzani.jpg') }}" alt="H. Ahmad Muzani">
        <div class="card-body">
          <h5 class="fw-bold card-title">Bagian Set. Ketua MPR</h5>
          <p class="text-muted small card-subtitle">H. Ahmad Muzani</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="calendar-section">
    <div class="container">
        <div class="month-section">
            <div class="letter-card">
                @if(Auth::check())
                    @php
                        $userRole = Auth::user()->role->nama_role;
                    @endphp

                    @if($userRole == 'sekretariat' || $userRole == 'admin')
                        <div class="letter-item">
                            <a href="{{ route('surat.ketuamprbiasa') }}">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Biasa</p>
                        </div>

                        <div class="letter-item">
                            <a href="{{ route('surat.ketuamprrahasia') }}">
                                <i class="bi bi-envelope" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Rahasia</p>
                        </div>

                        <div class="letter-item">
                            <a href="{{ route('surat.ketuamprsangatrahasia') }}">
                                <i class="bi bi-envelope-exclamation" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Sangat Rahasia</p>
                        </div>

                    @elseif($userRole == 'user')
                        <div class="letter-item">
                            <a href="{{ route('surat.ketuamprbiasa') }}">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Biasa</p>
                        </div>

                    @else
                        <div class="letter-item">
                            <p>Tidak ada menu surat yang tersedia untuk Anda.</p>
                        </div>
                    @endif

                @else
                    <div class="letter-item text-center">
                         <p>Silakan Login Untuk Melihat Surat</p>
                         <a href="{{ route('login') }}" class="btn btn-primary mt-2">Login</a>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

@endsection