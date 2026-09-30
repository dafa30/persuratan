<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SITEMAN-SURAT</title>

  {{-- Bootstrap CSS (5.3.3) --}}
  <link href="{{ asset('library/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Bootstrap Icons (wajib agar <i class="bi ..."></i> muncul) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
  />

  <style>
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
      top: 72px;                  /* navbar fixed-top: sesuaikan jika tinggi navbar beda */
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

  {{-- Navbar --}}
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="{{ url('surat/dashboard') }}">Dashboard Surat</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          @php
            $onAdminIndex  = request()->routeIs('surat.index');
            $onSettingRole = request()->routeIs('setting.role.*');
          @endphp

          @auth
            @if(($onAdminIndex || $onSettingRole) && auth()->user()->role_id === 1)
              <li class="nav-item">
                <a class="nav-link {{ $onSettingRole ? 'active' : '' }}" href="{{ route('setting.role.index') }}">
                  Pengaturan Pengguna
                </a>
              </li>
            @endif
          @endauth

          {{-- Dropdown user --}}
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                 data-bs-toggle="dropdown" aria-expanded="false">
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
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">Login</a>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  {{-- Flash alert stack (pojok kanan atas) --}}
  <div class="flash-stack">
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
  <main class="container mt-5 pt-3">
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
