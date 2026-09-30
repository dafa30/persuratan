@extends('layouts.app')

@section('content')

{{-- Notifikasi error --}}
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Tombol Tambah Surat --}}
<a href="{{ route('surat.create') }}" class="btn btn-primary mb-3">Tambah Surat</a>

{{-- ===================== SURAT MASUK ===================== --}}
<div class="surat-masuk-section">
    <h2>Surat Masuk</h2>
    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width:56px">No</th>
                <th>Judul Surat</th>
                <th>Nomor Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Perihal</th>
                <th>Kategori</th>
                <th>Status</th>
                <th style="width:280px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratMasuk as $index => $surat)
                @php
                    // Badge jenis
                    $jenis = strtolower((string)($surat->jenis_surat ?? ''));
                    $jenisBadgeClass = $jenis === 'masuk'
                        ? 'badge rounded-pill text-bg-success px-3 py-2'
                        : 'badge rounded-pill text-bg-info px-3 py-2';

                    // Badge status
                    $status = strtolower((string)($surat->status ?? ''));
                    $statusClass = match ($status) {
                        'dikirim'  => 'badge bg-warning text-dark',
                        'dibaca'   => 'badge bg-primary',
                        'diterima' => 'badge bg-success',
                        'ditolak'  => 'badge bg-danger',
                        default    => 'badge bg-secondary'
                    };

                    // Alur Caraka?
                    $isSuratCaraka = (strtolower($surat->pengirim_nama ?? '') === 'caraka') || !empty($surat->penerima_eksternal);
                    $editUrl = $isSuratCaraka
                        ? route('surat.edit.caraka', $surat)
                        : route('surat.edit', $surat->id_surats);
                @endphp
                <tr class="text-center">
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $surat->judul_surat }}</td>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td><span class="{{ $jenisBadgeClass }}">{{ ucfirst($surat->jenis_surat) }}</span></td>
                    <td>{{ optional($surat->created_at)->isoFormat('D MMMM Y') }}</td>
                    <td class="text-start">{{ $surat->pengirim_display }}</td>
                    <td class="text-start">{{ $surat->penerima_display }}</td>
                    <td class="text-start">{{ $surat->perihal }}</td>
                    <td>{{ ucfirst($surat->kategori) }}</td>
                    <td><span class="{{ $statusClass }}">{{ ucfirst($surat->status ?? '—') }}</span></td>
                    <td class="text-nowrap">
                        <div class="action-group">
                            @if(!empty($surat->file_surat))
                                {{-- Unduh --}}
                                <a href="{{ asset($surat->file_surat) }}"
                                   class="action-btn btn-download"
                                   target="_blank" download aria-label="Unduh surat">
                                    <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                    <span class="btn-layer label">Unduh</span>
                                </a>

                                {{-- Lihat --}}
                                <a href="{{ route('surat.detailsurat', ['kategori' => $surat->jenis_surat, 'id_surats' => $surat->id_surats]) }}"
                                   class="action-btn btn-view" aria-label="Lihat surat">
                                    <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                    <span class="btn-layer label">Lihat</span>
                                </a>
                            @else
                                <span class="text-muted me-2">Tidak ada file</span>
                            @endif

                            {{-- Edit / Unggah (jika caraka, label "Unggah") --}}
                            <a href="{{ $editUrl }}" class="action-btn btn-edit" aria-label="{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}">
                                <span class="btn-layer icon">
                                    <i class="bi {{ $isSuratCaraka ? 'bi-upload' : 'bi-pencil-square' }}"></i>
                                </span>
                                <span class="btn-layer label">{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}</span>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('surat.destroy', $surat->id_surats) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
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
                    <td colspan="11" class="text-center">Tidak ada surat masuk tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===================== SURAT KELUAR ===================== --}}
<div class="surat-keluar-section mt-5">
    <h2>Surat Keluar</h2>
    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width:56px">No</th>
                <th>Judul Surat</th>
                <th>Nomor Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Perihal</th>
                <th>Kategori</th>
                <th>Status</th>
                <th style="width:280px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratKeluar as $index => $surat)
                @php
                    $jenis = strtolower((string)($surat->jenis_surat ?? ''));
                    $jenisBadgeClass = $jenis === 'masuk'
                        ? 'badge rounded-pill text-bg-success px-3 py-2'
                        : 'badge rounded-pill text-bg-danger px-3 py-2';

                    $status = strtolower((string)($surat->status ?? ''));
                    $statusClass = match ($status) {
                        'dikirim'  => 'badge bg-warning text-dark',
                        'dibaca'   => 'badge bg-primary',
                        'diterima' => 'badge bg-success',
                        'ditolak'  => 'badge bg-danger',
                        default    => 'badge bg-secondary'
                    };

                    $isSuratCaraka = (strtolower($surat->pengirim_nama ?? '') === 'caraka') || !empty($surat->penerima_eksternal);
                    $editUrl = $isSuratCaraka
                        ? route('surat.edit.caraka', $surat)
                        : route('surat.edit', $surat->id_surats);
                @endphp
                <tr class="text-center">
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $surat->judul_surat }}</td>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td><span class="{{ $jenisBadgeClass }}">{{ ucfirst($surat->jenis_surat) }}</span></td>
                    <td>{{ optional($surat->created_at)->isoFormat('D MMMM Y') }}</td>
                    <td class="text-start">{{ $surat->pengirim_display }}</td>
                    <td class="text-start">{{ $surat->penerima_display }}</td>
                    <td class="text-start">{{ $surat->perihal }}</td>
                    <td>{{ ucfirst($surat->kategori) }}</td>
                    <td><span class="{{ $statusClass }}">{{ ucfirst($surat->status ?? '—') }}</span></td>
                    <td class="text-nowrap">
                        <div class="action-group">
                            @if(!empty($surat->file_surat))
                                {{-- Unduh --}}
                                <a href="{{ asset($surat->file_surat) }}"
                                   class="action-btn btn-download"
                                   target="_blank" download aria-label="Unduh surat">
                                    <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                    <span class="btn-layer label">Unduh</span>
                                </a>

                                {{-- Lihat --}}
                                <a href="{{ route('surat.detailsurat', ['kategori' => $surat->jenis_surat, 'id_surats' => $surat->id_surats]) }}"
                                   class="action-btn btn-view" aria-label="Lihat surat">
                                    <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                    <span class="btn-layer label">Lihat</span>
                                </a>
                            @else
                                <span class="text-muted me-2">Tidak ada file</span>
                            @endif

                            {{-- Edit / Unggah --}}
                            <a href="{{ $editUrl }}" class="action-btn btn-edit" aria-label="{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}">
                                <span class="btn-layer icon">
                                    <i class="bi {{ $isSuratCaraka ? 'bi-upload' : 'bi-pencil-square' }}"></i>
                                </span>
                                <span class="btn-layer label">{{ $isSuratCaraka ? 'Unggah' : 'Edit' }}</span>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('surat.destroy', $surat->id_surats) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
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
                    <td colspan="11" class="text-center">Tidak ada surat keluar tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
