@extends('layouts.app')

@section('content')
@php
    // Tentukan route "kembali ke daftar" berdasarkan kategori daftar Ketua MPR
    $backRoute = match ($kategori) {
        'biasa'          => route('surat.bagianTU_biasa'),
        default          => route('surat.index'),
    };

    // Waktu dalam zona WIB
    $createdAtWib = optional($surat->created_at)->timezone('Asia/Jakarta');
    $dibacaAtWib  = optional($surat->dibaca)->timezone('Asia/Jakarta');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Detail Surat: {{ e($kategori) }}</h2>
    {{-- Jangan pakai history.back(); gunakan route supaya halaman list benar-benar reload --}}
    <a href="{{ $backRoute }}" class="btn btn-secondary">Kembali ke Daftar</a>
</div>

<div class="row g-3">
    {{-- Kolom kiri: PDF viewer --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Dokumen PDF: {{ e($kategori) }}</div>
            <div class="card-body">
                @if(!empty($pdfPath))
                    <iframe
                        src="{{ $pdfPath }}#view=FitH"
                        style="width:100%; height:80vh; border:0;"
                        title="Dokumen PDF">
                    </iframe>
                @else
                    <div class="alert alert-warning mb-0">
                        File PDF tidak tersedia atau tidak ditemukan.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Status Tracking --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Status Tracking Surat</div>
            <div class="card-body">

                <style>
                    /* Timeline sederhana */
                    .tl {position: relative; margin-left: 18px;}
                    .tl::before {content:""; position:absolute; left:7px; top:0; bottom:0; width:4px; background:#1b6ec2; opacity:.25;}
                    .tl-item {position: relative; margin-bottom:22px;}
                    .tl-dot {position:absolute; left:-1px; top:7px; width:18px; height:18px; border-radius:50%; background:#1b6ec2;}
                    .tl-card {margin-left:20px;}
                </style>

                <div class="tl">

                    {{-- Step 1: Dikirim (selalu ada) --}}
                    <div class="tl-item">
                        <div class="tl-dot"></div>
                        <div class="tl-card card shadow-sm">
                            <div class="card-body py-3">
                                <h5 class="card-title mb-1">Surat Dikirim</h5>
                                <div class="text-muted small">
                                    {{ $createdAtWib?->translatedFormat('d F Y, H:i') }} WIB
                                </div>
                                <p class="mb-0 mt-2">
                                    Dokumen surat telah berhasil dikirim dan disimpan dalam sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Dibaca (muncul jika sudah ada timestamp dibaca) --}}
                    @if($dibacaAtWib)
                        <div class="tl-item">
                            <div class="tl-dot"></div>
                            <div class="tl-card card shadow-sm">
                                <div class="card-body py-3">
                                    <h5 class="card-title mb-1">Surat Dibaca</h5>
                                    <div class="text-muted small">
                                        {{ $dibacaAtWib->translatedFormat('d F Y, H:i') }} WIB
                                    </div>
                                    <p class="mb-0 mt-2">
                                        Dokumen surat telah dilihat atau dibuka.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="tl-item">
                            <div class="tl-dot"></div>
                            <div class="tl-card card border-secondary-subtle">
                                <div class="card-body py-3">
                                    <h5 class="card-title mb-1">Menunggu Dibaca</h5>
                                    <p class="mb-0 mt-2 text-muted">
                                        Surat belum dibuka oleh penerima.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
