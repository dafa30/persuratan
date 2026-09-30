@extends('UI_Frontend.index')

@section('content')
<div class="container mt-4">
    <h3>Hasil Pencarian: "{{ $q }}"</h3>

    @if($data_surat->isEmpty())
    <div class="alert alert-warning">Tidak ada surat ditemukan.</div>
    @else
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Judul</th>
                <th>Nomor</th>
                <th>Nama File</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_surat as $i => $s)
            <tr class="text-center">
                <td>{{ $i+1 }}</td>
                <td>{{ $s->judul_surat }}</td>
                <td>{{ $s->nomor_surat }}</td>
                @php
                    $fileExtension = $s->file_surat ? pathinfo($s->file_surat, PATHINFO_EXTENSION) : '';
                    $displayFileName = $s->nama_file_asli
                        ?: ($s->file_surat ? $s->judul_surat . ($fileExtension ? '.' . $fileExtension : '') : '-');
                @endphp
                <td>{{ $displayFileName }}</td>
                <td>{{ $s->kategori }}</td>
                <td>{{ $s->jenis_surat }}</td>
                <td>{{ optional($s->created_at)->format('d M Y') }}</td>
                <td>{{ $s->pengirim_display }}</td>
                <td>{{ $s->penerima_display }}</td>
                <td>
                    @if($s->file_surat)
                    <a href="{{ asset($s->file_surat) }}" class="btn btn-sm btn-info" target="_blank">Download</a>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada hasil.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif
</div>
@endsection