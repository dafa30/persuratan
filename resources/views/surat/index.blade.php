@extends('layouts.app')

@push('styles')
<style>
    .admin-dashboard {
        max-width: 1720px;
        margin: 0 auto;
        color: #20252b;
    }
    .dashboard-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }
    .dashboard-eyebrow {
        margin-bottom: 7px;
        color: #987223;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .dashboard-heading h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
    }
    .dashboard-heading p {
        margin: 6px 0 0;
        color: #69717a;
        font-size: 14px;
    }
    .dashboard-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid #151515;
        border-radius: 4px;
        color: #fff;
        background: #151515;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
    }
    .dashboard-add-button:hover {
        transform: translateY(-1px);
        border-color: #987223;
        color: #fff;
        background: #987223;
    }
    .dashboard-toolbar {
        display: flex;
        justify-content: flex-end;
        margin: 0 0 12px;
    }
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }
    .dashboard-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 98px;
        padding: 18px;
        border: 1px solid #e7e9ec;
        border-radius: 5px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(20, 28, 38, .035);
        animation: dashboard-stat-enter .35s ease both;
    }
    .dashboard-stat:nth-child(2) { animation-delay: .06s; }
    .dashboard-stat:nth-child(3) { animation-delay: .12s; }
    .dashboard-stat-icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 4px;
        font-size: 19px;
        flex: 0 0 auto;
    }
    .stat-incoming .dashboard-stat-icon { color: #24734e; background: #e8f3ed; }
    .stat-outgoing .dashboard-stat-icon { color: #315d8a; background: #eaf1f8; }
    .stat-total .dashboard-stat-icon { color: #987223; background: #f7f0df; }
    .dashboard-stat-label {
        display: block;
        margin-bottom: 3px;
        color: #69717a;
        font-size: 12px;
    }
    .dashboard-stat-value {
        display: block;
        color: #20252b;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.1;
    }
    .dashboard-section {
        margin-top: 22px;
        overflow: hidden;
        border: 1px solid #e7e9ec;
        border-radius: 5px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(20, 28, 38, .035);
    }
    .dashboard-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 17px 18px;
        border-bottom: 1px solid #e7e9ec;
    }
    .dashboard-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }
    .dashboard-section-title i { color: #987223; }
    .dashboard-section-count {
        display: inline-flex;
        align-items: center;
        min-height: 25px;
        padding: 0 9px;
        border-radius: 3px;
        color: #59616b;
        background: #f0f2f4;
        font-size: 12px;
        font-weight: 600;
    }
    .dashboard-table-wrap { overflow-x: auto; }
    .dashboard-table {
        min-width: 1180px;
        margin: 0;
        border-color: #edf0f2;
        font-size: 13px;
    }
    .dashboard-table thead th {
        padding: 11px 10px;
        border-bottom: 1px solid #e3e6e9;
        color: #606873;
        background: #f7f8f9;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .dashboard-table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
    }
    .dashboard-table tbody tr { transition: background-color .15s ease; }
    .dashboard-table tbody tr:hover { background: #faf8f2; }
    .dashboard-table .badge { font-size: 11px; font-weight: 600; }
    .dashboard-empty {
        padding: 30px !important;
        color: #69717a;
        text-align: center;
    }
    .dashboard-empty i {
        display: block;
        margin-bottom: 7px;
        color: #a0a6ad;
        font-size: 22px;
    }
    @keyframes dashboard-stat-enter {
        from { opacity: 0; transform: translateY(7px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (prefers-reduced-motion: reduce) {
        .dashboard-stat { animation: none; }
        .dashboard-add-button, .dashboard-table tbody tr { transition: none; }
    }
    @media (max-width: 767.98px) {
        .dashboard-heading { align-items: stretch; flex-direction: column; }
        .dashboard-heading h1 { font-size: 24px; }
        .dashboard-add-button { align-self: flex-start; }
        .dashboard-stats { grid-template-columns: 1fr; gap: 9px; margin-bottom: 20px; }
        .dashboard-stat { min-height: 78px; padding: 13px; }
        .dashboard-section-heading { padding: 14px; }
    }
</style>
@endpush

@section('content')

{{-- Notifikasi error --}}
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="admin-dashboard">
    <div class="dashboard-heading">
        <div>
            <div class="dashboard-eyebrow">Panel Administrasi</div>
            <h1>Dashboard Surat</h1>
            <p>Pantau dan kelola surat masuk maupun surat keluar.</p>
        </div>
    </div>

    <div class="dashboard-stats" aria-label="Ringkasan surat">
        <article class="dashboard-stat stat-incoming">
            <span class="dashboard-stat-icon"><i class="bi bi-inbox-fill" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Surat Masuk</span>
                <span class="dashboard-stat-value">{{ $suratMasuk->count() }}</span>
            </span>
        </article>
        <article class="dashboard-stat stat-outgoing">
            <span class="dashboard-stat-icon"><i class="bi bi-send-fill" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Surat Keluar</span>
                <span class="dashboard-stat-value">{{ $suratKeluar->count() }}</span>
            </span>
        </article>
        <article class="dashboard-stat stat-total">
            <span class="dashboard-stat-icon"><i class="bi bi-files" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Total Surat</span>
                <span class="dashboard-stat-value">{{ $suratMasuk->count() + $suratKeluar->count() }}</span>
            </span>
        </article>
    </div>
    @include('components.surat-date-filter', ['years' => $years])
    <div class="dashboard-toolbar">
        <a href="{{ route('surat.create') }}" class="dashboard-add-button">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Surat
        </a>
    </div>

    {{-- ===================== SURAT MASUK ===================== --}}
    <section class="dashboard-section surat-masuk-section">
        <div class="dashboard-section-heading">
            <h2 class="dashboard-section-title"><i class="bi bi-inbox" aria-hidden="true"></i> Surat Masuk</h2>
            <span class="dashboard-section-count">{{ $suratMasuk->count() }} surat</span>
        </div>
        <div class="dashboard-table-wrap">
    <table class="table align-middle dashboard-table">
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
                    <td>{{ ucwords(str_replace('_', ' ', $surat->kategori ?? '')) }}</td>
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
                    <td colspan="11" class="dashboard-empty"><i class="bi bi-inbox" aria-hidden="true"></i>Tidak ada surat masuk tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
        </div>
    </section>

    {{-- ===================== SURAT KELUAR ===================== --}}
    <section class="dashboard-section surat-keluar-section">
        <div class="dashboard-section-heading">
            <h2 class="dashboard-section-title"><i class="bi bi-send" aria-hidden="true"></i> Surat Keluar</h2>
            <span class="dashboard-section-count">{{ $suratKeluar->count() }} surat</span>
        </div>
        <div class="dashboard-table-wrap">
    <table class="table align-middle dashboard-table">
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
                    <td>{{ ucwords(str_replace('_', ' ', $surat->kategori ?? '')) }}</td>
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
                    <td colspan="11" class="dashboard-empty"><i class="bi bi-send" aria-hidden="true"></i>Tidak ada surat keluar tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
        </div>
    </section>
</div>
@endsection
