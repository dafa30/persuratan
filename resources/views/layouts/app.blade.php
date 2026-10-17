<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SITEMAN-SUCA</title>
  <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">

  {{-- Bootstrap CSS (5.3.3) --}}
  <link href="{{ asset('library/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Bootstrap Icons (wajib agar <i class="bi ..."></i> muncul) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
  />

  <style>
    :root {
      --admin-ink: #151515;
      --admin-gold: #d5aa45;
      --admin-paper: #f5f6f7;
      --admin-muted: #69717a;
      --admin-line: #e7e9ec;
    }

    body {
      background: var(--admin-paper);
    }

    /* ====== Dropdown smooth animation (tanpa JS) ====== */
    .navbar .dropdown-menu {
      display: block;              /* biar bisa transisi saat hide */
      margin: 0;
      opacity: 0;
      transform: translateY(-8px) scale(.98);
      visibility: hidden;
      pointer-events: none;
      will-change: transform, opacity;
      transition: transform .18s ease, opacity .18s ease, visibility 0s linear .18s;
      transform-origin: top right; /* enak buat menu yang align kanan */
    }
    .navbar .dropdown-menu.show {
      opacity: 1;
      transform: translateY(0) scale(1);
      visibility: visible;
      pointer-events: auto;
      transition: transform .18s ease, opacity .18s ease, visibility 0s;
    }
    @media (prefers-reduced-motion: reduce) {
      .navbar .dropdown-menu { transition: none !important; }
    }

    /* ====== Flash alert: pojok kanan atas ====== */
    .flash-stack{
      position: fixed;
      right: 16px;
      top: 142px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      z-index: 2000;              /* di atas navbar & konten */
    }
    .flash-stack .alert{
      max-width: 340px;           /* tidak kepanjangan */
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 8px;
      box-shadow: 0 6px 18px rgba(0,0,0,.1);
      margin: 0;                  /* hilangkan margin default alert */
    }
    .flash-stack-standard {
      top: 72px;
    }
    .flash-stack-bare {
      top: 16px;
    }
    @media (max-width: 767.98px) {
      .flash-stack.flash-stack-bare { top: 10px; }
    }

    .admin-header {
      position: sticky;
      top: 0;
      z-index: 1030;
      background: #fff;
      box-shadow: 0 4px 18px rgba(15, 23, 42, .08);
    }
    .admin-brandbar {
      min-height: 76px;
      background: #fff;
    }
    .admin-brandbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 76px;
      gap: 20px;
    }
    .admin-brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
      color: var(--admin-ink);
      text-decoration: none;
    }
    .admin-brand img {
      width: 48px;
      height: 48px;
      object-fit: contain;
      flex: 0 0 auto;
    }
    .admin-brand-copy {
      display: grid;
      gap: 2px;
      line-height: 1.2;
    }
    .admin-brand-copy strong {
      font-size: 14px;
      font-weight: 700;
    }
    .admin-system-label {
      color: var(--admin-muted);
      font-size: 13px;
      font-weight: 600;
      text-transform: uppercase;
      white-space: nowrap;
    }
    .admin-nav {
      min-height: 52px;
      padding: 0;
      background: var(--admin-ink) !important;
    }
    .admin-nav .navbar-nav {
      align-items: center;
      gap: 6px;
    }
    .admin-nav .nav-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      min-height: 52px;
      padding: 0 14px !important;
      color: rgba(255, 255, 255, .78) !important;
      font-size: 14px;
      transition: color .18s ease, background-color .18s ease;
    }
    .admin-nav .nav-link:hover,
    .admin-nav .nav-link.active {
      color: #fff !important;
      background: rgba(255, 255, 255, .09);
    }
    .admin-nav .nav-link.active {
      box-shadow: inset 0 -3px var(--admin-gold);
    }
    .admin-nav .nav-link i {
      font-size: 16px;
    }
    .admin-nav .admin-user-toggle {
      gap: 9px;
      min-height: 40px;
      margin: 6px 0;
      padding: 0 12px !important;
      border: 1px solid rgba(255, 255, 255, .24);
      border-radius: 4px;
      color: #fff !important;
      background: rgba(255, 255, 255, .08);
    }
    .admin-user-toggle .bi-person-fill {
      color: var(--admin-gold);
      font-size: 18px;
    }
    .admin-user-name {
      max-width: 220px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .admin-nav .dropdown-menu {
      min-width: 190px;
      padding: 6px;
      border: 1px solid var(--admin-line);
      border-radius: 4px;
      box-shadow: 0 12px 28px rgba(15, 23, 42, .14);
    }
    .admin-nav .dropdown-item {
      border-radius: 3px;
      padding: 9px 10px;
    }
    .admin-nav .navbar-toggler {
      margin: 8px 0;
      border-color: rgba(255, 255, 255, .4);
    }
    .admin-nav .navbar-toggler-icon {
      filter: invert(1);
    }
    .admin-main {
      padding-top: 24px;
      padding-bottom: 40px;
    }
    .admin-sidebar {
      --bs-offcanvas-width: 268px;
      position: fixed;
      inset: 0 auto 0 0;
      z-index: 1045;
      display: flex;
      flex-direction: column;
      width: 268px;
      height: 100vh;
      border-right: 1px solid #e5e7eb;
      background: #fff;
      box-shadow: 5px 0 24px rgba(20, 28, 38, .045);
    }
    .admin-sidebar .offcanvas-body {
      display: flex;
      flex-direction: column;
      padding: 0 14px 16px;
    }
    .admin-sidebar-brand {
      display: flex;
      align-items: center;
      gap: 11px;
      min-height: 88px;
      padding: 16px 20px;
      border-bottom: 1px solid #edf0f2;
      color: var(--admin-ink);
      text-decoration: none;
    }
    .admin-sidebar-brand img { width: 42px; height: 42px; object-fit: contain; }
    .admin-sidebar-brand strong { display: block; font-size: 12px; line-height: 1.35; }
    .admin-sidebar-brand small { display: block; margin-top: 3px; color: var(--admin-muted); font-size: 11px; }
    .admin-sidebar-section-label {
      margin: 23px 10px 8px;
      color: #8a929b;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }
    .admin-sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      min-height: 44px;
      margin: 2px 0;
      padding: 0 12px;
      border-left: 3px solid transparent;
      border-radius: 3px;
      color: #4f5964;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      transition: color .15s ease, background-color .15s ease;
    }
    .admin-sidebar-link i { width: 18px; color: #818a94; font-size: 16px; text-align: center; }
    .admin-sidebar-link:hover,
    .admin-sidebar-link.active {
      border-left-color: var(--admin-gold);
      color: #6e5113;
      background: #f8f4e9;
    }
    .admin-sidebar-link.active i { color: #a17a24; }
    .admin-sidebar-bottom { margin-top: auto; padding-top: 16px; border-top: 1px solid #edf0f2; }
    .admin-sidebar-user { overflow: hidden; padding: 7px 10px 12px; color: #68717b; font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
    .admin-sidebar-user i { margin-right: 7px; color: #a17a24; }
    .admin-sidebar-logout { width: 100%; padding: 9px 12px; border: 0; border-radius: 3px; color: #8a3f3f; background: #fbf1f0; font-size: 13px; text-align: left; }
    .admin-sidebar-logout:hover { color: #742d2d; background: #f6e5e3; }
    .admin-mobile-header { position: sticky; top: 0; z-index: 1030; display: flex; align-items: center; justify-content: space-between; min-height: 62px; padding: 0 16px; border-bottom: 1px solid var(--admin-line); background: #fff; }
    .admin-mobile-header .admin-sidebar-brand { min-height: 0; padding: 0; border: 0; }
    .admin-mobile-header .admin-sidebar-brand img { width: 36px; height: 36px; }
    .admin-mobile-trigger { display: inline-grid; place-items: center; width: 40px; height: 40px; border: 1px solid var(--admin-line); border-radius: 4px; color: #303740; background: #fff; font-size: 19px; }
    .admin-main-sidebar { width: calc(100% - 268px); min-height: 100vh; margin-left: 268px; padding: 28px 30px 44px; }
    @media (min-width: 992px) {
      .admin-sidebar.offcanvas-lg { visibility: visible !important; transform: none !important; }
    }
    @media (max-width: 991.98px) {
      .admin-nav .navbar-collapse {
        padding-bottom: 8px;
      }
      .admin-nav .navbar-nav {
        align-items: stretch;
      }
      .admin-nav .nav-link {
        min-height: 44px;
      }
      .admin-nav .admin-user-toggle {
        width: fit-content;
      }
      .admin-nav .dropdown-menu {
        width: max-content;
        min-width: 0;
        max-width: calc(100vw - 24px);
      }
      .admin-nav .dropdown-menu form,
      .admin-nav .dropdown-item {
        width: max-content;
      }
    }
    @media (max-width: 575.98px) {
      .admin-brandbar,
      .admin-brandbar-inner {
        min-height: 64px;
      }
      .admin-brand img {
        width: 40px;
        height: 40px;
      }
      .admin-brand-copy strong {
        font-size: 11px;
      }
      .admin-brand-copy small {
        font-size: 9px;
      }
      .admin-system-label {
        display: none;
      }
      .admin-main {
        padding-top: 18px;
      }
      .admin-main-sidebar { width: 100%; min-height: calc(100vh - 62px); margin-left: 0; padding: 20px 16px 36px; }
      .admin-sidebar { border: 0; box-shadow: 12px 0 30px rgba(15, 23, 42, .16); }
      .flash-stack {
        top: 126px;
        right: 10px;
        left: 10px;
      }
      .flash-stack .alert {
        max-width: none;
      }
    }

    /* ====== Topbar & sidebar collapsible (dashboard admin) ====== */
    .admin-sidebar {
      overflow-x: hidden;
      transition: width .3s cubic-bezier(.4, 0, .2, 1), transform .3s ease-in-out;
    }
    .admin-sidebar .offcanvas-body { overflow-x: hidden; overflow-y: auto; }
    .admin-brand-text, .admin-link-text {
      overflow: hidden;
      white-space: nowrap;
      max-width: 220px;
      transition: opacity .2s ease, max-width .3s cubic-bezier(.4, 0, .2, 1);
    }
    .admin-sidebar-brand img { flex: 0 0 auto; }
    .admin-sidebar-section-label { white-space: nowrap; overflow: hidden; transition: all .3s ease; }

    .admin-topbar {
      position: sticky;
      top: 0;
      z-index: 1030;
      display: flex;
      align-items: center;
      gap: 12px;
      height: 64px;
      margin-left: 268px;
      padding: 0 24px;
      border-bottom: 1px solid var(--admin-line);
      background: #fff;
      transition: margin-left .3s cubic-bezier(.4, 0, .2, 1);
    }
    .admin-sidebar-toggle {
      display: inline-grid;
      place-items: center;
      width: 40px;
      height: 40px;
      border: 0;
      border-radius: 8px;
      color: #4f5964;
      background: transparent;
      font-size: 24px;
      transition: background-color .15s ease;
    }
    .admin-sidebar-toggle:hover { background: #f1f3f5; }
    .admin-topbar-brand { display: inline-flex; align-items: center; gap: 8px; color: var(--admin-ink); text-decoration: none; }
    .admin-topbar-brand img { width: 34px; height: 34px; object-fit: contain; }
    .admin-topbar-brand strong { font-size: 13px; }
    .admin-user-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      max-width: 100%;
      padding: 5px 14px 5px 6px;
      border: 1px solid var(--admin-line);
      border-radius: 999px;
      color: #3b434c;
      background: #fff;
      font-size: 14px;
      transition: box-shadow .15s ease, background-color .15s ease;
    }
    .admin-user-btn:hover, .admin-user-btn[aria-expanded="true"] { background: #f8f9fa; box-shadow: 0 2px 8px rgba(15, 23, 42, .08); }
    .admin-user-avatar { color: var(--admin-gold); font-size: 30px; line-height: 1; }
    .admin-user-photo { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; object-position: top; border: 2px solid var(--admin-gold); flex-shrink: 0; }
    .admin-user-name { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .admin-user-chevron { font-size: 12px; transition: transform .25s ease; }
    .admin-user-btn[aria-expanded="true"] .admin-user-chevron { transform: rotate(180deg); }
    .admin-user { position: relative; }
    .admin-user-menu {
      display: block;
      top: 100%;
      right: 0;
      left: auto;
      min-width: 100%;
      margin-top: 8px;
      padding: 6px;
      border: 1px solid var(--admin-line);
      border-radius: 12px;
      box-shadow: 0 12px 28px rgba(15, 23, 42, .14);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transform: translateY(-8px) scale(.98);
      transform-origin: top right;
      transition: transform .18s ease, opacity .18s ease, visibility 0s linear .18s;
    }
    .admin-user-menu.show {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: none;
      transition: transform .18s ease, opacity .18s ease, visibility 0s;
    }
    .admin-user-menu .dropdown-item { border-radius: 8px; padding: 9px 12px; color: #8a3f3f; font-weight: 600; }
    .admin-user-menu .dropdown-item:hover { color: #742d2d; background: #fbf1f0; }
    .flash-stack-admin { top: 76px; }
    .admin-no-anim *, .admin-no-anim *::before, .admin-no-anim *::after { transition: none !important; }

    @media (min-width: 992px) {
      .admin-main-sidebar { transition: margin-left .3s cubic-bezier(.4, 0, .2, 1), width .3s cubic-bezier(.4, 0, .2, 1); }
      body.sidebar-collapsed .admin-sidebar { width: 76px; }
      body.sidebar-collapsed .admin-topbar { margin-left: 76px; }
      body.sidebar-collapsed .admin-main-sidebar { width: calc(100% - 76px); margin-left: 76px; }
      body.sidebar-collapsed .admin-sidebar-brand { justify-content: center; gap: 0; padding-left: 0; padding-right: 0; }
      body.sidebar-collapsed .admin-brand-text,
      body.sidebar-collapsed .admin-link-text { max-width: 0; opacity: 0; }
      body.sidebar-collapsed .admin-sidebar .offcanvas-body { padding-left: 10px; padding-right: 10px; }
      body.sidebar-collapsed .admin-sidebar-section-label {
        height: 1px;
        margin: 16px 12px;
        padding: 0;
        font-size: 0;
        background: #edf0f2;
      }
      body.sidebar-collapsed .admin-sidebar-link { justify-content: center; gap: 0; padding: 0; }
    }
    @media (max-width: 991.98px) {
      .admin-topbar { margin-left: 0; padding: 0 14px; }
      .admin-main-sidebar { margin-left: 0; width: 100%; }
      .admin-user-name { max-width: 110px; }
    }

    /* ====== Style lain (tetap dari versi sebelumnya) ====== */
    .timeline { list-style:none; padding:0; position:relative; }
    .timeline:before { content:''; position:absolute; top:0; bottom:0; width:4px; background:#0d6efd; left:20px; margin-left:-2px; }
    .timeline-item { margin-bottom:20px; padding-left:50px; position:relative; }
    .timeline-badge { color:#fff; width:40px; height:40px; line-height:40px; font-size:1.2em; text-align:center; position:absolute; top:0; left:0; background:#0d6efd; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .timeline-badge.success { background:#198754; }
    .timeline-badge.pending { background:#ffc107; }
    .timeline-panel { padding:15px; border:1px solid #ddd; border-radius:.25rem; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .timeline-title { margin-top:0; font-weight:bold; }
    .timeline-body p, .timeline-body ul { margin-bottom:0; }
    .timeline-body p + p { margin-top:5px; }
    .timeline-date { font-size:.85em; color:#777; }
    .pdf-viewer { width:100%; height:70vh; border:1px solid #ddd; }

    .admin-list-page { max-width: 1600px; margin: 0 auto; color: #20252b; }
    .admin-list-heading { margin-bottom: 18px; }
    .admin-list-heading h1 { margin: 0; font-size: 24px; font-weight: 700; }
    .admin-list-heading p { margin: 5px 0 0; color: var(--admin-muted); font-size: 13px; }
    .admin-data-table { min-width: 1240px; margin: 0; border-color: #edf0f2; font-size: 13px; }
    .admin-data-table thead th { padding: 11px 10px; border-bottom: 1px solid #e3e6e9; color: #606873; background: #f7f8f9; font-size: 10px; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .admin-data-table tbody td { padding: 12px 10px; vertical-align: middle; }
    .admin-data-table tbody tr { transition: background-color .15s ease; }
    .admin-data-table tbody tr:hover { background: #faf8f2; }
    .admin-data-table .badge { font-size: 11px; font-weight: 600; }

    /* Grup aksi: lebih rapat */
        .action-group{ display:inline-flex; gap:.35rem; }

        /* Tombol: ukuran FIXED lebih kecil agar kolom hemat ruang */
        .action-btn{
        position:relative;
        width:60px;                 /* ↓ dari 88px */
        height:36px;                /* ↓ dari 44px */
        border:0; border-radius:.65rem;
        padding:0; overflow:hidden;
        display:inline-flex; align-items:center; justify-content:center;
        box-shadow:0 2px 5px rgba(0,0,0,.05);
        transition:opacity .22s ease, filter .22s ease, transform .22s ease;
        }

        /* Layers (ikon & label) cross-fade */
        .action-btn .btn-layer{
        position:absolute; inset:0;
        display:flex; align-items:center; justify-content:center;
        transition:opacity .24s ease, transform .24s ease;
        }

        /* Ikon & label dibuat sedikit lebih kecil */
        .action-btn .icon i{ font-size:1rem; line-height:1; }      /* ↓ dari 1.1rem */
        .action-btn .label{
        font-size:.72rem;                                        /* ↓ dari .78rem */
        line-height:1; white-space:nowrap;
        opacity:0; transform:translateY(6px);
        }

        /* Hover/focus: cross-fade tanpa ubah ukuran tombol */
        .action-btn:hover .icon,
        .action-btn:focus-visible .icon{ opacity:0; transform:translateY(-6px) scale(.92); }
        .action-btn:hover .label,
        .action-btn:focus-visible .label{ opacity:1; transform:translateY(0); }

        /* Redupkan tombol lain (opsional) */
        .action-group:hover .action-btn:not(:hover){
        opacity:.5; filter:saturate(.9); transform:scale(.985);
        }

        /* Warna tetap */
        .btn-download{ background:#e9f2ff; color:#1d4ed8; }
        .btn-view    { background:#e7f6ef; color:#047857; }
        .btn-edit    { background:#fff6e5; color:#b45309; }
        .btn-delete  { background:#fdecef; color:#b91c1c; }

        .btn-download:hover{ background:#dbeafe; }
        .btn-view:hover    { background:#d1fae5; }
        .btn-edit:hover    { background:#ffedd5; }
        .btn-delete:hover  { background:#fee2e2; }

        /* Fokus ring */
        .action-btn:focus-visible{ outline:3px solid rgba(59,130,246,.45); outline-offset:2px; }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce){
        .action-btn, .action-btn .btn-layer{ transition:none; }
        }

  </style>

  @stack('styles')
</head>
<body>

  @php
    $onAdminIndex = request()->routeIs('surat.index');
    $onIncoming = request()->routeIs('surat.masuk');
    $onOutgoing = request()->routeIs('surat.keluar');
    $onSettingRole = request()->routeIs('setting.role.*');
    $showAdminNav = auth()->check()
      && auth()->user()->role_id === 1
      && request()->routeIs('surat.index', 'surat.masuk', 'surat.keluar', 'setting.role.*');
  @endphp
  @if($showAdminNav)
  <script>
    try {
      if (localStorage.getItem('adminSidebar') === 'collapsed' && window.innerWidth >= 992) {
        document.body.classList.add('sidebar-collapsed');
      }
    } catch (e) {}
    document.body.classList.add('admin-no-anim');
  </script>
  <aside class="offcanvas-lg offcanvas-start admin-sidebar" tabindex="-1" id="adminSidebar" aria-label="Navigasi dashboard">
    <div class="offcanvas-header d-lg-none border-bottom">
      <h2 class="offcanvas-title fs-6 mb-0">Navigasi</h2>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Tutup menu"></button>
    </div>
    <a class="admin-sidebar-brand d-none d-lg-flex" href="{{ route('surat.index') }}" aria-label="Dashboard Persuratan MPR RI">
      <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI">
      <span class="admin-brand-text"><strong>MAJELIS PERMUSYAWARATAN RAKYAT<br>REPUBLIK INDONESIA</strong><small>Sistem Persuratan</small></span>
    </a>
    <div class="offcanvas-body">
      <div class="admin-sidebar-section-label">Menu utama</div>
      <nav aria-label="Menu utama">
        <a class="admin-sidebar-link {{ $onAdminIndex ? 'active' : '' }}" href="{{ route('surat.index') }}" title="Dashboard"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span class="admin-link-text">Dashboard</span></a>
      </nav>
      <div class="admin-sidebar-section-label">Administrasi</div>
      <nav aria-label="Administrasi">
        <a class="admin-sidebar-link {{ $onIncoming ? 'active' : '' }}" href="{{ route('surat.masuk') }}" title="Surat Masuk"><i class="bi bi-inbox-fill" aria-hidden="true"></i><span class="admin-link-text">Surat Masuk</span></a>
        <a class="admin-sidebar-link {{ $onOutgoing ? 'active' : '' }}" href="{{ route('surat.keluar') }}" title="Surat Keluar"><i class="bi bi-send-fill" aria-hidden="true"></i><span class="admin-link-text">Surat Keluar</span></a>
        <a class="admin-sidebar-link" href="{{ route('surat.create') }}" title="Tambah Surat"><i class="bi bi-plus-square" aria-hidden="true"></i><span class="admin-link-text">Tambah Surat</span></a>
      </nav>
      <div class="admin-sidebar-section-label">User</div>
      <nav aria-label="User">
        <a class="admin-sidebar-link {{ $onSettingRole ? 'active' : '' }}" href="{{ route('setting.role.index') }}" title="Pengaturan Pengguna"><i class="bi bi-people-fill" aria-hidden="true"></i><span class="admin-link-text">Pengaturan Pengguna</span></a>
      </nav>
    </div>
  </aside>

  <header class="admin-topbar">
    <button class="admin-sidebar-toggle" type="button" id="adminSidebarToggle" aria-controls="adminSidebar" aria-label="Buka atau tutup sidebar">
      <i class="bi bi-list" aria-hidden="true"></i>
    </button>
    <a class="admin-topbar-brand d-lg-none" href="{{ route('surat.index') }}" aria-label="Dashboard Persuratan MPR RI">
      <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI">
      <strong>SITEMAN-SUCA</strong>
    </a>
    <div class="dropdown admin-user ms-auto">
      <button class="admin-user-btn" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" aria-label="Menu pengguna">
        @if (auth()->user()->foto_url)
          <img src="{{ auth()->user()->foto_url }}" alt="" class="admin-user-photo">
        @else
          <i class="bi bi-person-circle admin-user-avatar" aria-hidden="true"></i>
        @endif
        <span class="admin-user-name">{{ auth()->user()->name }}</span>
        <i class="bi bi-chevron-down admin-user-chevron" aria-hidden="true"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end admin-user-menu">
        <li>
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar</button>
          </form>
        </li>
      </ul>
    </div>
  </header>

  @push('scripts')
  <script>
    (function () {
      const toggle = document.getElementById('adminSidebarToggle');
      const sidebar = document.getElementById('adminSidebar');
      if (!toggle || !sidebar) return;

      toggle.setAttribute('aria-expanded', String(!document.body.classList.contains('sidebar-collapsed')));

      toggle.addEventListener('click', () => {
        if (window.innerWidth >= 992) {
          const collapsed = document.body.classList.toggle('sidebar-collapsed');
          toggle.setAttribute('aria-expanded', String(!collapsed));
          try { localStorage.setItem('adminSidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
        } else {
          bootstrap.Offcanvas.getOrCreateInstance(sidebar).toggle();
        }
      });

      requestAnimationFrame(() => requestAnimationFrame(() => document.body.classList.remove('admin-no-anim')));
    })();
  </script>
  @endpush
  @elseif(!View::hasSection('no_navbar'))
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="{{ route('surat.index') }}">Sistem Persuratan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Buka navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          @auth
            @if($onSettingRole && auth()->user()->role_id === 1)
              <li class="nav-item">
                <a class="nav-link active" href="{{ route('setting.role.index') }}">Pengaturan Pengguna</a>
              </li>
            @endif
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ auth()->user()->name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                  <form action="{{ route('logout') }}" method="POST" class="px-3 py-1">
                    @csrf
                    <button type="submit" class="dropdown-item px-0">Log Out</button>
                  </form>
                </li>
              </ul>
            </li>
          @else
            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>
  @endif

  {{-- Flash alert stack (pojok kanan atas) --}}
  <div class="flash-stack {{ $showAdminNav ? 'flash-stack-admin' : (View::hasSection('no_navbar') ? 'flash-stack-bare' : 'flash-stack-standard') }}">
    @php($flashSuccess = session()->pull('success'))
    @if($flashSuccess)
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $flashSuccess }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Periksa kembali input Anda.</strong>
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
  </div>

  {{-- Main --}}
  @if($showAdminNav)
  <main class="container-fluid admin-main admin-main-sidebar">
  @elseif(View::hasSection('no_navbar'))
  <main class="container py-4 py-md-5 page-no-navbar">
  @else
  <main class="container mt-5 pt-3">
  @endif
    @yield('content')
  </main>

  {{-- Modal batas ukuran file --}}
  <div class="modal fade" id="fileSizeModal" tabindex="-1" aria-labelledby="fileSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-warning">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="fileSizeModalLabel">Peringatan Ukuran File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          Ukuran file melebihi <strong>2 MB</strong>. Silakan pilih file yang lebih kecil.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal tipe file --}}
  <div class="modal fade" id="fileTypeModal" tabindex="-1" aria-labelledby="fileTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-warning">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="fileTypeModalLabel">Peringatan Format File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          Format file tidak didukung. Hanya PDF, Word (.doc/.docx) atau Excel (.xls/.xlsx).
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Bootstrap JS Bundle --}}
   <script src="{{ asset('library/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  {{-- Validasi file global (opsional) --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const input = document.getElementById('file_surat');
      const form  = document.getElementById('formSurat');
      if (!input || !form) return;

      const SIZE_LIMIT = 2 * 1024 * 1024;
      const ALLOWED_EXT = ['pdf','doc','docx','xls','xlsx'];

      input.addEventListener('change', () => {
        const f = input.files?.[0];
        if (f && f.size > SIZE_LIMIT) {
          input.value = '';
          new bootstrap.Modal(document.getElementById('fileSizeModal')).show();
        }
      });

      form.addEventListener('submit', (e) => {
        const f = input.files?.[0];
        if (!f) return;
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        if (!ALLOWED_EXT.includes(ext)) {
          e.preventDefault();
          input.value = '';
          new bootstrap.Modal(document.getElementById('fileTypeModal')).show();
        }
      });
    });
  </script>

  @stack('scripts')

  {{-- Auto-reload Back/Forward + bersihkan ?refresh --}}
  <script>
    window.addEventListener('pageshow', function (e) {
      const navEntries = performance.getEntriesByType('navigation');
      const navType = navEntries.length ? navEntries[0].type : 'navigate';
      if (e.persisted || navType === 'back_forward') window.location.reload();

      const sp = new URLSearchParams(location.search);
      if (sp.has('refresh')) {
        const url = new URL(location.href);
        url.searchParams.delete('refresh');
        history.replaceState({}, '', url);
      }
    });
  </script>

  @include('partials.confirm-delete')

  {{-- Overlay loading elegan saat surat diproses --}}
  <style>
    #suratLoading{position:fixed;inset:0;z-index:2000;display:flex;align-items:center;justify-content:center;
      background:rgba(15,30,60,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
      opacity:0;visibility:hidden;transition:opacity .35s ease,visibility .35s ease}
    #suratLoading.show{opacity:1;visibility:visible}
    #suratLoading .sl-card{background:#fff;border-radius:16px;padding:2rem 2.5rem;text-align:center;min-width:280px;max-width:calc(100vw - 32px);
      box-shadow:0 20px 50px rgba(0,0,0,.3);transform:translateY(12px) scale(.97);transition:transform .35s ease}
    #suratLoading.show .sl-card{transform:none}
    #suratLoading .sl-ring{position:relative;width:64px;height:64px;margin:0 auto 1.25rem}
    #suratLoading .sl-ring::before,#suratLoading .sl-ring::after{content:"";position:absolute;inset:0;border-radius:50%;border:3px solid transparent}
    #suratLoading .sl-ring::before{border-top-color:#1e3a8a;border-right-color:#1e3a8a;animation:slSpin 1.1s cubic-bezier(.5,.1,.5,.9) infinite}
    #suratLoading .sl-ring::after{inset:10px;border-bottom-color:#c9a227;border-left-color:#c9a227;animation:slSpin 1.6s linear infinite reverse}
    #suratLoading .sl-title{font-weight:600;color:#1e3a8a;margin-bottom:.25rem}
    #suratLoading .sl-sub{font-size:.85rem;color:#6b7280}
    #suratLoading .sl-dots span{display:inline-block;animation:slFade 1.4s infinite both}
    #suratLoading .sl-dots span:nth-child(2){animation-delay:.2s}
    #suratLoading .sl-dots span:nth-child(3){animation-delay:.4s}
    @keyframes slSpin{to{transform:rotate(360deg)}}
    @keyframes slFade{0%,80%,100%{opacity:.2}40%{opacity:1}}
    @media (prefers-reduced-motion:reduce){#suratLoading .sl-ring::before,#suratLoading .sl-ring::after{animation-duration:3s}}
  </style>
  <div id="suratLoading" role="status" aria-live="polite" aria-hidden="true">
    <div class="sl-card">
      <div class="sl-ring"></div>
      <div class="sl-title">Memproses Surat<span class="sl-dots"><span>.</span><span>.</span><span>.</span></span></div>
      <div class="sl-sub">Mohon tunggu, jangan menutup halaman ini.</div>
    </div>
  </div>
  <script>
    (function () {
      const ov = document.getElementById('suratLoading');
      document.addEventListener('submit', function (e) {
        const f = e.target;
        if (!f || f.id !== 'formSurat') return;
        setTimeout(() => {
          if (e.defaultPrevented) return;
          ov.classList.add('show');
          ov.setAttribute('aria-hidden', 'false');
          f.querySelectorAll('button[type=submit]').forEach(b => b.disabled = true);
        }, 0);
      });
      window.addEventListener('pageshow', () => ov.classList.remove('show'));
    })();
  </script>

  {{-- (Opsional) Auto-dismiss semua alert setelah 3 detik --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.flash-stack .alert').forEach(el => {
        setTimeout(() => bootstrap.Alert.getOrCreateInstance(el).close(), 3000);
      });
    });
  </script>

</body>
</html>
