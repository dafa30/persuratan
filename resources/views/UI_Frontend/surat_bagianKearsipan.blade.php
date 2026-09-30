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
            <div class="card-custom w-100 text-center">
                <img src="{{ asset('anggota_mpr/setjen.jpg') }}" alt="H. Ahmad Muzani">
                <div class="card-body">
                    <h5 class="fw-bold card-title">Bagian Kearsipan Dan Persuratan</h5>
                    <p class="text-muted small card-subtitle">Kearsipan dan Persuratan</p>
                </div>
            </div>
        </div>
    </div>
</div>

@php
// Mapping kategori ke label dan nama route
$categories = [
'biasa' => ['label' => 'Surat Kategori Biasa', 'route' => 'surat.bagianKearsipan_biasa', 'icon' => 'bi-envelope-paper'],
'rahasia' => ['label' => 'Surat Kategori Rahasia', 'route' => 'surat.bagianKearsipan_rahasia', 'icon' => 'bi-envelope'],
'sangat_rahasia' => ['label' => 'Surat Kategori Sangat Rahasia', 'route' => 'surat.bagianKearsipan_sangat_rahasia',
'icon' => 'bi-envelope-exclamation'],
];
@endphp

<div class="letter-card d-flex flex-wrap gap-4">
    {{-- Looping untuk setiap kategori surat --}}
    @foreach($categories as $categoryKey => $categoryConfig)

    <div class="letter-item text-center">
        @auth
        {{-- Jika user sudah login, cek rolenya --}}
        @php
        $userRole = Auth::user()->role->nama_role;
        $hasAccess = false; // Default-nya tidak punya akses

        // Logika pembagian role
        if ($categoryKey === 'biasa') {
        $hasAccess = in_array($userRole, ['admin', 'sekretariat', 'kearsipan']);
        } 
        elseif (in_array($categoryKey, ['rahasia', 'sangat_rahasia'])) {
        $hasAccess = in_array($userRole, ['admin', 'sekretariat', 'kearsipan']);
        }
        @endphp

        @if($hasAccess)
        {{-- Jika punya akses, link aktif --}}
        <a href="{{ route($categoryConfig['route']) }}">
            <i class="bi {{ $categoryConfig['icon'] }}" style="font-size: 2rem;"></i>
        </a>
        <p class="letter-title mt-2">{{ $categoryConfig['label'] }}</p>
        @else
        {{-- Jika tidak punya akses, link mati dan berwarna abu-abu --}}
        <a href="#" onclick="return false;" style="cursor: not-allowed; color: #aaa;">
            <i class="bi {{ $categoryConfig['icon'] }}" style="font-size: 2rem;"></i>
        </a>
        <p class="letter-title mt-2" style="color: #aaa;">{{ $categoryConfig['label'] }}</p>
        @endif

        @else
        {{-- Jika user belum login, semua link mengarah ke halaman login --}}
        <a href="{{ route('login') }}">
            <i class="bi {{ $categoryConfig['icon'] }}" style="font-size: 2rem;"></i>
        </a>
        <p class="letter-title mt-2">{{ $categoryConfig['label'] }}</p>
        @endauth
    </div>

    @endforeach
</div>
</div>
@endsection