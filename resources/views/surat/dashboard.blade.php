@extends('layouts.app')

@push('styles')
<style>
    .admin-dashboard {
        max-width: 1500px;
        margin: 0 auto;
        color: #20252b;
    }
    .dashboard-heading {
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
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .dashboard-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 96px;
        padding: 18px;
        border: 1px solid #e7e9ec;
        border-radius: 5px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(20, 28, 38, .035);
    }
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
    @media (max-width: 767.98px) {
        .dashboard-heading h1 { font-size: 24px; }
        .dashboard-stats { grid-template-columns: 1fr; gap: 9px; }
        .dashboard-stat { min-height: 76px; padding: 13px; }
    }
</style>
@endpush

@section('content')
<div class="admin-dashboard">
    <div class="dashboard-heading">
        <div class="dashboard-eyebrow">Panel Administrasi</div>
        <h1>Dashboard Persuratan MPR RI</h1>
        <p>Sistem Informasi Temu-Kembali Pengiriman Surat Dengan Caraka</p>
    </div>

    <div class="dashboard-stats" aria-label="Ringkasan surat">
        <article class="dashboard-stat stat-incoming">
            <span class="dashboard-stat-icon"><i class="bi bi-inbox-fill" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Surat Masuk</span>
                <span class="dashboard-stat-value">{{ $suratMasukCount }}</span>
            </span>
        </article>
        <article class="dashboard-stat stat-outgoing">
            <span class="dashboard-stat-icon"><i class="bi bi-send-fill" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Surat Keluar</span>
                <span class="dashboard-stat-value">{{ $suratKeluarCount }}</span>
            </span>
        </article>
        <article class="dashboard-stat stat-total">
            <span class="dashboard-stat-icon"><i class="bi bi-files" aria-hidden="true"></i></span>
            <span>
                <span class="dashboard-stat-label">Total Surat</span>
                <span class="dashboard-stat-value">{{ $suratMasukCount + $suratKeluarCount }}</span>
            </span>
        </article>
    </div>

</div>
@endsection