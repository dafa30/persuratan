@extends('layouts.app')

@section('content')
<div class="admin-list-page">
    <div class="admin-list-heading">
        <h1>Surat Masuk</h1>
        <p>Daftar surat yang diterima.</p>
    </div>
    @include('components.surat-date-filter', ['years' => $years])

    <div class="table-responsive bg-white border rounded-1 shadow-sm">
        <table class="table align-middle admin-data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Surat</th>
                    <th>Nomor Surat</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal</th>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Perihal</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $index => $item)
                    @php
                        $statusClass = match(strtolower($item->status ?? '')) {
                            'dikirim' => 'badge bg-warning text-dark',
                            'dibaca' => 'badge bg-primary',
                            'diterima' => 'badge bg-success',
                            'ditolak' => 'badge bg-danger',
                            default => 'badge bg-secondary',
                        };
                        $isSuratCaraka = strtolower($item->pengirim_nama ?? '') === 'caraka' || !empty($item->penerima_eksternal);
                        $editUrl = $isSuratCaraka
                            ? route('surat.edit.caraka', ['surat' => $item->id_surats])
                            : route('surat.edit', ['id_surats' => $item->id_surats]);
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->judul_surat ?? '-' }}</td>
                        <td>{{ $item->nomor_surat ?? '-' }}</td>
                        <td><span class="badge rounded-pill text-bg-success">{{ ucfirst($item->jenis_surat ?? '-') }}</span></td>
                        <td>{{ $item->created_at ? $item->created_at->isoFormat('D MMMM Y') : '-' }}</td>
                        <td>{{ $item->pengirim_display ?? optional($item->user)->name ?? '-' }}</td>
                        <td>{{ $item->penerima_display ?? optional($item->penerima)->name ?? $item->penerima_eksternal ?? '-' }}</td>
                        <td>{{ $item->perihal ?? '-' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $item->kategori ?? '-')) }}</td>
                        <td><span class="{{ $statusClass }}">{{ ucfirst($item->status ?? '-') }}</span></td>
                        <td class="text-nowrap">
                            <div class="action-group">
                                @if($item->file_surat)
                                    <a href="{{ route('surat.file', ['surat' => $item->id_surats]) }}" class="action-btn btn-download" target="_blank" download aria-label="Unduh surat">
                                        <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                        <span class="btn-layer label">Unduh</span>
                                    </a>
                                    <a href="{{ route('surat.detailsurat', ['kategori' => $item->jenis_surat, 'id_surats' => $item->id_surats]) }}" class="action-btn btn-view" aria-label="Lihat tracking surat">
                                        <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                        <span class="btn-layer label">Lihat</span>
                                    </a>
                                @else
                                    <span class="text-muted me-2">Tidak ada file</span>
                                @endif
                                <a href="{{ $editUrl }}" class="action-btn btn-edit" aria-label="{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}">
                                    <span class="btn-layer icon"><i class="bi {{ $isSuratCaraka ? 'bi-upload' : 'bi-pencil-square' }}"></i></span>
                                    <span class="btn-layer label">{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}</span>
                                </a>
                                <form action="{{ route('surat.destroy', $item->id_surats) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" aria-label="Hapus surat">
                                        <span class="btn-layer icon"><i class="bi bi-trash"></i></span>
                                        <span class="btn-layer label">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Tidak ada data surat masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
