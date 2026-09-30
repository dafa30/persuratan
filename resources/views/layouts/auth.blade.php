<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Sistem Persuratan MPR RI')</title>

  <!-- Bootstrap CSS -->
  <link href="{{ asset('library/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #0052CC;
      --primary-dark: #003E99;
      --accent: #F0B323;
      --border-soft: #E4E7EC;
      --text-muted: #6B7280;

      /* ✅ warna hijau untuk tombol Daftar */
      --success: #16A34A;
      --success-dark: #15803D;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Open Sans', sans-serif;
      color: #0F172A;
      background: url("{{ asset('mpr3.jpg') }}") center/cover no-repeat fixed;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative; /* untuk overlay */
      overflow-x: hidden;
    }

    /* Overlay gelap + gradasi supaya lebih elegan */
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background:
        linear-gradient(135deg, rgba(15, 23, 42, 0.94), rgba(0, 82, 204, 0.72));
      mix-blend-mode: multiply;
      z-index: 0;
    }

    .login-wrapper {
      position: relative;
      width: 100%;
      max-width: 520px;   /* ✅ lebih lebar */
      padding: 1.5rem;
    }

    .login-card {
      position: relative;
      z-index: 1060; /* > z-index backdrop bootstrap (1050) */
      background-color: rgba(255, 255, 255, 0.97);
      padding: 2rem 2.2rem 1.9rem;
      border-radius: 1.5rem;
      border: 1px solid rgba(148, 163, 184, 0.25);
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.65);
      width: 100%;
      text-align: center;
      backdrop-filter: blur(14px);
      opacity: 0;
      transform: translateY(18px);
      animation: fadeInUp 0.8s ease forwards;
    }


    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Elemen header di dalam @yield('content') bisa pakai class ini */
    .login-title {
      font-weight: 700;
      font-size: 1.35rem;
      letter-spacing: .02em;
      margin-bottom: .25rem;
      color: #0F172A;
    }

    .login-subtitle {
      font-size: .85rem;
      color: var(--text-muted);
      margin-bottom: 1.2rem;
    }

    .logo-circle {
      width: 60px;
      height: 60px;
      margin-bottom: 0.6rem;
      border-radius: 999px;
      border: 2px solid rgba(15, 23, 42, 0.06);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.85rem;
      background: #FFFFFF;
      overflow: hidden;
    }

    .logo-circle img {
      width: 52px;
      height: auto;
      object-fit: contain;
    }

    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: .6rem;
      padding: .25rem .9rem;
      border-radius: 999px;
      background: rgba(248, 250, 252, 0.8);
      border: 1px solid rgba(148, 163, 184, 0.35);
      font-size: .75rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #4B5563;
      margin-bottom: .8rem;
    }

    .brand-dot {
      width: .5rem;
      height: .5rem;
      border-radius: 999px;
      background: var(--accent);
      box-shadow: 0 0 0 3px rgba(240, 179, 35, 0.3);
    }

    /* Styling form agar terasa premium */
    .form-label {
      font-size: .85rem;
      color: #4B5563;
      font-weight: 600;
      margin-bottom: .35rem;
      text-align: left;
    }

    .input-group-text {
      border-radius: 999px 0 0 999px;
      border-color: var(--border-soft);
      background: #F9FAFB;
      color: #6B7280;
      padding-inline: .9rem;
      font-size: .9rem;
    }

    .form-control {
      border-radius: 0 999px 999px 0;
      border-color: var(--border-soft);
      padding-inline: .95rem 1rem;
      font-size: .9rem;
      height: 2.9rem;
      background-color: #FFFFFF;
    }

    .form-control:focus,
    .input-group-text:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 1px rgba(0, 82, 204, 0.25);
    }

    .form-check-label {
      font-size: .82rem;
      color: var(--text-muted);
    }

    /* Tombol login dibuat kapsul + gradasi */
    .btn-primary {
      width: 100%;
      height: 2.6rem;
      border-radius: 999px;
      border: none;
      font-weight: 600;
      font-size: .9rem;
      letter-spacing: .03em;
      text-transform: uppercase;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      box-shadow: 0 14px 35px rgba(0, 82, 204, 0.55);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .btn-primary:hover {
      filter: brightness(1.03);
      transform: translateY(-1px);
      box-shadow: 0 18px 45px rgba(0, 82, 204, 0.6);
    }

    .btn-primary:active {
      transform: translateY(0);
      box-shadow: 0 8px 25px rgba(0, 82, 204, 0.4);
    }

    .btn-primary .spinner-border {
      width: 1rem;
      height: 1rem;
      border-width: 2px;
    }

    .btn-success {
      width: 100%;
      height: 2.6rem;
      border-radius: 999px;
      border: none;
      font-weight: 600;
      font-size: .9rem;
      letter-spacing: .03em;
      text-transform: uppercase;
      background: linear-gradient(135deg, var(--success), var(--success-dark));
      box-shadow: 0 14px 35px rgba(22, 163, 74, 0.55);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .btn-success:hover {
      filter: brightness(1.03);
      transform: translateY(-1px);
      box-shadow: 0 18px 45px rgba(22, 163, 74, 0.6);
    }

    .btn-success:active {
      transform: translateY(0);
      box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
    }

    .login-meta {
      margin-top: 1rem;
      font-size: .85rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: var(--text-muted);
    }

    .login-meta a {
      text-decoration: none;
      font-weight: 600;
      color: var(--primary);
    }

    .login-meta a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .footer {
      margin-top: 1.4rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(226, 232, 240, 0.9);
      font-size: 0.78rem;
      color: #9CA3AF;
    }

    /* Toast positioning */
    .toast-container {
      position: fixed;
      top: 1.1rem;
      right: 1.1rem;
      z-index: 9999;
    }

    @media (max-width: 576px) {
      .login-wrapper {
        padding: 1rem;
      }
      .login-card {
        padding: 2.1rem 1.7rem 1.9rem;
        border-radius: 1.25rem;
      }
    }
  </style>

  @stack('styles')
</head>
<body>
  <!-- Toast Notification -->
  <div class="toast-container">
    @if(session('success') && !session('register_success'))
      <div id="loginToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            {{ session('success') }}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    @endif
  </div>

  <div class="login-wrapper">
    <div class="login-card">
      @yield('content')
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="{{ asset('library/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- Spinner & Toast Script -->
  <script>
    // Spinner saat submit form login
    document.getElementById('loginForm')?.addEventListener('submit', function () {
      const btn = document.getElementById('loginBtn');
      if (!btn) return;
      btn.disabled = true;
      btn.querySelector('.spinner-border')?.classList.remove('d-none');
      btn.querySelector('.btn-text')?.textContent = 'Memproses...';
    });

    // (Opsional) Tampilkan/ sembunyikan password jika kamu punya checkbox #showPasswordCheck
    document.getElementById('showPasswordCheck')?.addEventListener('change', function () {
      const passwordInput = document.getElementById('password');
      if (passwordInput) passwordInput.type = this.checked ? 'text' : 'password';
    });

    // Inisialisasi Toast sukses (kalau ada)
    const toastEl = document.getElementById('loginToast');
    if (toastEl && window.bootstrap?.Toast) {
      const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { autohide: true, delay: 3000 });
      toast.show();
    }
  </script>

  @stack('scripts')
</body>
</html>
