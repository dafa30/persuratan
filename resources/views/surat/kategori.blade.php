@extends('layouts.app')

@section('content')
@php
    // Waktu dalam zona WIB
    $createdAtWib = optional($surat->created_at)->timezone('Asia/Jakarta');
    $dibacaAtWib  = $surat->dibaca ? \Carbon\Carbon::parse($surat->dibaca)->timezone('Asia/Jakarta') : null;
    $sudahDibaca  = !is_null($dibacaAtWib);
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Detail Surat: {{ $surat->jenis_surat }}</h2>
        {{-- gunakan route daftar supaya benar2 reload --}}
        <a href="{{ route('surat.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
    </div>

    <div class="row g-3">
        {{-- Kolom untuk PDF Viewer --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Dokumen PDF: {{ $surat->jenis_surat }}
                </div>
                <div class="card-body p-0">
                    @if($pdfPath)
                        <style>
                            .pdf-viewer{width:100%;height:80vh;border:0}
                        </style>
                        <iframe src="{{ $pdfPath }}#view=FitH" class="pdf-viewer" title="Dokumen PDF"></iframe>
                    @else
                        <div class="alert alert-warning m-3" role="alert">
                            File PDF tidak ditemukan atau belum diunggah untuk surat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom untuk Status Tracking --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Status Tracking Surat</div>
                <div class="card-body">

                    {{-- === Timeline Styles (ringan & mandiri) === --}}
                    <style>
                        .tl{position:relative;margin-left:18px}
                        .tl::before{
                            content:"";position:absolute;left:7px;top:0;bottom:0;width:4px;
                            background:#adb5bd; /* abu-abu default */
                        }
                        .tl.tl-read::before{background:#1b6ec2} /* aktif biru kalau sudah dibaca */

                        .tl-item{position:relative;margin-bottom:22px}
                        .tl-dot{
                            position:absolute;left:-1px;top:7px;width:18px;height:18px;border-radius:50%;
                            background:#adb5bd; /* abu-abu */
                        }
                        .tl-dot.active{background:#1b6ec2} /* aktif biru */
                        .tl-card{margin-left:20px}
                    </style>

                    <div class="tl {{ $sudahDibaca ? 'tl-read' : '' }}">
                        {{-- Step 1: Dikirim (selalu aktif) --}}
                        <div class="tl-item">
                            <div class="tl-dot active"></div>
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

                        {{-- Step 2: Dibaca (aktif bila sudah ada timestamp; kalau belum, abu-abu) --}}
                        <div class="tl-item">
                            <div class="tl-dot {{ $sudahDibaca ? 'active' : '' }}"></div>
                            <div class="tl-card card {{ $sudahDibaca ? 'shadow-sm' : 'border-secondary-subtle' }}">
                                <div class="card-body py-3">
                                    <h5 class="card-title mb-1">
                                        {{ $sudahDibaca ? 'Surat Dibaca' : 'Menunggu Dibaca' }}
                                    </h5>

                                    @if($sudahDibaca)
                                        <div class="text-muted small">
                                            {{ $dibacaAtWib?->translatedFormat('d F Y, H:i') }} WIB
                                        </div>
                                        <p class="mb-0 mt-2">Dokumen surat telah dilihat atau dibuka.</p>
                                    @else
                                        <p class="mb-0 mt-2 text-muted">Surat belum dibuka oleh penerima.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- === End Timeline === --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
