@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Data Surat Keluar</h2>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-secondary text-center">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Nomor Surat</th>
                    <th>Jenis Surat</th>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Dikirim</th>
                    <th>Dibaca</th>
                    <th>File Surat</th>
                    <th style="width:180px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $item)
                    @php
                        // ✅ Deteksi alur Caraka (pengirim manual / eksternal)
                        $isSuratCaraka = (strtolower($item->pengirim_nama ?? '') === 'caraka') || !empty($item->penerima_eksternal);

                        // ✅ Tentukan URL edit otomatis
                        $editUrl = $isSuratCaraka
                            ? route('surat.edit.caraka', ['surat' => $item->id_surats])
                            : route('surat.edit', ['id' => $item->id_surats]);

                        // ✅ Warna badge kategori
                        $kategoriClass = match(strtolower($item->kategori ?? '')) {
                            'biasa' => 'badge bg-success',
                            'rahasia' => 'badge bg-warning text-dark',
                            'sangat_rahasia' => 'badge bg-danger',
                            default => 'badge bg-secondary'
                        };

                        // ✅ Warna badge status
                        $statusClass = match(strtolower($item->status ?? '')) {
                            'dikirim'  => 'badge bg-info text-dark',
                            'dibaca'   => 'badge bg-primary',
                            'diterima' => 'badge bg-success',
                            'ditolak'  => 'badge bg-danger',
                            default    => 'badge bg-secondary'
                        };
                    @endphp

                    <tr class="text-center">
                        <td>{{ $item->id_surats }}</td>
                        <td>{{ $item->nomor_surat ?? '-' }}</td>
                        <td>{{ ucfirst($item->jenis_surat ?? '-') }}</td>

                        {{-- ✅ Nama pengirim & penerima dengan fallback --}}
                        <td class="text-start">
                            {{ $item->pengirim_display ?? optional($item->user)->name ?? '-' }}
                        </td>
                        <td class="text-start">
                            {{ $item->penerima_display ?? optional($item->penerima)->name ?? $item->penerima_eksternal ?? '-' }}
                        </td>

                        <td><span class="{{ $kategoriClass }}">{{ ucfirst($item->kategori ?? '-') }}</span></td>
                        <td><span class="{{ $statusClass }}">{{ ucfirst($item->status ?? '-') }}</span></td>

                        <td>{{ $item->created_at ? $item->created_at->isoFormat('D MMM Y, HH:mm') : '-' }}</td>
                        <td>{{ $item->dibaca ? \Carbon\Carbon::parse($item->dibaca)->isoFormat('D MMM Y, HH:mm') : '-' }}</td>

                        {{-- ✅ File surat --}}
                        <td>
                            @if($item->file_surat)
                                <a href="{{ asset($item->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>

                        {{-- ✅ Tombol Aksi --}}
                        <td class="text-nowrap">
                            <a href="{{ $editUrl }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> {{ $isSuratCaraka ? 'Edit (Caraka)' : 'Edit' }}
                            </a>

                            @if($item->file_bukti_terima)
                                <a href="{{ asset($item->file_bukti_terima) }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="bi bi-image"></i> Bukti
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            Tidak ada data surat keluar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
