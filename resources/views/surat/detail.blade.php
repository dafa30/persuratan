@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Surat</h2>
    <p><strong>Nomor Surat:</strong> {{ $surat->nomor_surat }}</p>
    <p><strong>Jenis Surat:</strong> {{ ucfirst($surat->jenis_surat) }}</p>
    <p><strong>Kategori:</strong> {{ ucfirst($surat->kategori) }}</p>

    <h4>Status Pengiriman</h4>
    <ul class="list-group">
        <li class="list-group-item">
            <strong>Mulai:</strong> {{ $surat->mulai ? $surat->mulai->format('d M Y, H:i') : 'Belum dimulai' }}
        </li>
        <li class="list-group-item">
            <strong>Diproses:</strong> {{ $surat->diproses ? $surat->diproses->format('d M Y, H:i') : 'Belum diproses' }}
        </li>
        <li class="list-group-item">
            <strong>Selesai:</strong> {{ $surat->selesai ? $surat->selesai->format('d M Y, H:i') : 'Belum selesai' }}
        </li>
    </ul>

    <div class="mt-4">
        <h4>File Surat</h4>
        <a href="{{ url('/storage/surats/'.$surat->file_surat) }}" class="btn btn-primary" target="_blank">Unduh Surat</a>
    </div>
</div>
@endsection