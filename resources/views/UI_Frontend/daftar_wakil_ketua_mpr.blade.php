@extends('UI_Frontend.index')

@section('content')

<div class="hero-section">
    <div class="container">
        <h1>SITEMAN-SURAT</h1>
        <p class="lead">Sistem Informasi Temu-Kembali Pengiriman Surat Dengan Caraka</p>
    </div>
</div>

<div class="calendar-section">
    <div class="container">
        <div class="month-section">
            <div class="letter-card">

                {{-- Cek apakah pengguna sudah login --}}
                @auth
                    @php
                        // Ambil role pengguna, pastikan relasi 'role' dan kolom 'nama_role' ada
                        $userRole = Auth::user()->role->nama_role; 
                    @endphp

                    {{-- Tampilkan menu berdasarkan role 'sekretariat' --}}
                    @if($userRole == 'sekretariat')
                        <div class="letter-item">
                            <a href="{{ route('surat.wakilketuamprbiasa', ['nama_anggota_wakil' => $nama_anggota_wakil]) }}">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Biasa</p>
                        </div>
                        <div class="letter-item">
                            <a href="{{ route('surat.wakilketuamprrahasia', ['nama_anggota_wakil' => $nama_anggota_wakil]) }}">
                                <i class="bi bi-envelope" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Rahasia</p>
                        </div>
                        <div class="letter-item">
                            <a href="{{ route('surat.wakilketuamprsangatrahasia', ['nama_anggota_wakil' => $nama_anggota_wakil]) }}">
                                <i class="bi bi-envelope-exclamation" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Sangat Rahasia</p>
                        </div>

                    {{-- Tampilkan menu berdasarkan role 'user' atau 'caraka' --}}
                    @elseif($userRole == 'user' || $userRole == 'caraka')
                        <div class="letter-item">
                            <a href="{{ route('surat.wakilketuamprbiasa', ['nama_anggota_wakil' => $nama_anggota_wakil]) }}">
                                <i class="bi bi-envelope-paper" style="font-size:30px"></i>
                            </a>
                            <p class="letter-title">Surat Kategori Biasa</p>
                        </div>
                    
                    {{-- Pesan jika role tidak dikenali --}}
                    @else
                        <div class="letter-item">
                            <p>Tidak ada menu surat yang tersedia untuk Anda.</p>
                        </div>
                    @endif

                {{-- Jika pengguna belum login, tampilkan tombol login --}}
                @else
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