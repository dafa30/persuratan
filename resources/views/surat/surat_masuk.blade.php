@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Data Surat Masuk</h2>
    @include('components.surat-date-filter', ['years' => $years])

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-secondary text-center">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Nomor Surat</th>
                    <th>Jenis Surat</th>
                    <th>Pengirim</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Dikirim</th>
                    <th>Dibaca</th>
                    <th>File Surat</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $item)
                    @php
                        // badge kategori
                        $kategoriClass = match(strtolower($item->kategori ?? '')) {
                            'biasa' => 'badge bg-success',
                            'rahasia' => 'badge bg-warning text-dark',
                            'sangat_rahasia' => 'badge bg-danger',
                            default => 'badge bg-secondary'
                        };

                        // badge status
                        $statusClass = match(strtolower($item->status ?? '')) {
                            'dikirim' => 'badge bg-info text-dark',
                            'dibaca'  => 'badge bg-primary',
                            'diterima' => 'badge bg-success',
                            'ditolak' => 'badge bg-danger',
                            default => 'badge bg-secondary'
                        };
                    @endphp

                    <tr class="text-center">
                        {{-- gunakan id_surats sebagai PK --}}
                        <td>{{ $item->id_surats }}</td>

                        <td>{{ $item->nomor_surat ?? '-' }}</td>
                        <td>{{ ucfirst($item->jenis_surat ?? '-') }}</td>

                        {{-- ✅ tampilkan nama pengirim (manual / relasi user) --}}
                        <td class="text-start">
                            {{ $item->pengirim_display ?? optional($item->user)->name ?? '-' }}
                        </td>

                        <td><span class="{{ $kategoriClass }}">{{ ucfirst($item->kategori ?? '-') }}</span></td>

                        <td><span class="{{ $statusClass }}">{{ ucfirst($item->status ?? '-') }}</span></td>

                        <td>{{ $item->created_at ? $item->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }}</td>
                        <td>{{ $item->updated_at ? $item->updated_at->isoFormat('D MMMM Y, HH:mm') : '-' }}</td>

                        <td>
                            @if(!empty($item->file_surat))
                                <a href="{{ route('surat.file', ['surat' => $item->id_surats]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Tidak ada data surat masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
