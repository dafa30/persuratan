<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SITEMAN-SURAT</title>

    <!-- Bootstrap & Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Custom Style -->
    <style>
    /* letakkan di blok <style> yang sudah ada */

    /* Pacifico di public root */
    @font-face{
        font-family: 'Pacifico';
        src: url('{{ asset('Pacifico-Regular.ttf') }}') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    /* Poppins SemiBold di folder public/Poppins */
    @font-face{
        font-family: 'Poppins-Bold';
        src: url('{{ asset('Poppins/Poppins-SemiBold.ttf') }}') format('truetype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding-top: 70px;
        }

        /* Styling untuk Navbar */
        nav.navbar {
            background-color: transparent !important;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 0.6rem 1rem;
            transition: background-color 0.4s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Poppins-Bold', sans-serif;
            /* Ukuran font menggunakan px daripada rem */
            font-size: 14px;
        }


        nav.navbar.scrolled {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 253, 253, 0.8) !important;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #000 !important;
            font-weight: bold;
            font-size: 1.2rem;
            text-decoration: none;
            font-family: 'Poppins-Bold', sans-serif;
            /* Menggunakan Poppins-Bold di navbar brand */
        }

        .navbar-brand img {
            width: 40px;
            height: auto;
        }

        .navbar-nav .nav-item {
            position: relative;
        }

        .search-toggle {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 6px;
            color: inherit;
            background: transparent;
            font-size: 1.15rem;
            transition: color .2s ease, background-color .2s ease, transform .2s ease;
        }

        .search-toggle:hover,
        .search-toggle:focus-visible {
            color: #fff;
            background: rgba(33, 37, 41, .72);
            transform: translateY(-1px);
        }

        .nav-search .search-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1100;
            width: min(320px, calc(100vw - 24px));
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, .65);
            border-radius: 8px;
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(.98);
            transform-origin: top right;
            transition: opacity .22s ease, transform .22s ease, visibility .22s;
            pointer-events: none;
        }

        .nav-search.is-open .search-panel {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .mobile-search {
            padding: 0 5px;
        }

        .mobile-search-toggle {
            width: 100%;
            justify-content: flex-start;
            padding: 10px 5px;
            color: white;
            font-size: 1rem;
            font-weight: bold;
        }

        .mobile-search .search-panel {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            visibility: hidden;
            overflow: hidden;
            transition: grid-template-rows .3s ease, opacity .25s ease, visibility 0s linear .3s;
        }

        .mobile-search .search-panel form {
            min-height: 0;
            overflow: hidden;
            padding: 0 4px;
        }

        .mobile-search.is-open .search-panel {
            grid-template-rows: 1fr;
            opacity: 1;
            visibility: visible;
            transition-delay: 0s;
        }

        .mobile-search.is-open .search-panel form {
            padding-top: 10px;
            padding-bottom: 8px;
        }

        .search-toggle:focus-visible {
            outline: 2px solid #f0b429;
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            .search-toggle,
            .search-panel {
                transition: none !important;
            }
        }

        .navbar-nav .nav-item:hover {
            background-color: #d3d3d3;
            border-radius: .5rem;
        }

        nav.navbar.scrolled .navbar-nav .nav-link {
            color: black !important;
        }

        .navbar-nav .nav-link.active,
        .navbar-nav .nav-item.active .nav-link {
            background-color: #6c757d;
            color: white !important;
            border-radius: 0.5rem;
        }

        /* Styling untuk dropdown */
        .navbar-nav .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
        }

        .navbar-nav .nav-item:hover .dropdown-menu {
            display: block;
            animation: slideDown 0.3s ease-in-out;
        }

        /* Animasi untuk dropdown */
        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .burger {
            width: 30px;
            height: 22px;
            position: relative;
            display: none;
            margin-right: 15px;
            cursor: pointer;
        }

        .burger span {
            position: absolute;
            height: 3px;
            width: 100%;
            background-color: white;
            transition: all 0.3s ease-in-out;
            left: 0;
        }

        .burger:hover span {
            background-color: #ccc;
        }

        /* Warna burger berubah jadi silver saat scroll */
        .top-bar-fixed.scrolled .burger span {
            background-color: silver !important;
        }

        .burger span:nth-child(1) {
            top: 0;
        }

        .burger span:nth-child(2) {
            top: 9px;
        }

        .burger span:nth-child(3) {
            top: 18px;
        }

        .burger.open span:nth-child(1) {
            transform: rotate(45deg);
            top: 9px;
        }

        .burger.open span:nth-child(2) {
            opacity: 0;
        }

        .burger.open span:nth-child(3) {
            transform: rotate(-45deg);
            top: 9px;
        }

        .nav-slide {
            position: fixed;
            top: 0;
            left: -100%;
            width: 250px !important;
            height: 100%;
            padding: 5rem 1rem;
            background-color: rgba(0, 0, 0, 0.95);
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: left 0.3s ease-in-out;
            z-index: 1000;

            /* scroll vertikal */
            overflow-y: auto;
            flex-shrink: 0;

            /* reserve gutter untuk scrollbar agar lebar tidak berubah */
            scrollbar-gutter: stable both-edges;
        }


        .nav-slide.show {
            left: 0;
        }

        /* Sidebar menu spacing & style */
        .nav-slide a,
        .dropdown-mobile>a {
            font-weight: bold;
            font-size: 1rem;
            color: white;
            text-decoration: none;
            padding: 10px 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .nav-slide a:hover,
        .dropdown-mobile>a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ccc;
        }

        .dropdown-mobile>a .chevron {
            background: none !important;
            color: inherit;
            font-size: 1rem;
            line-height: 1;
            transition: transform 0.3s ease;
        }

        .dropdown-mobile>a.active .chevron {
            transform: rotate(120deg);
        }

        /* Submenu style */
        .submenu-mobile {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            opacity: 0;
            padding-left: 15px;
        }

        .submenu-mobile.show {
            max-height: 1000px;
            opacity: 1;
            padding-top: 5px;
        }

        .submenu-mobile a {
            font-weight: normal;
            font-size: 0.95rem;
            color: #eee;
            padding: 8px 0;
            display: block;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .submenu-mobile a:hover {
            color: #ccc;
        }

        @media (max-width: 768px) {
            .burger {
                display: block;
            }

            .nav-slide {
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            body.nav-open {
                overflow: hidden;
            }
        }

        @media (min-width: 769px) {
            .nav-slide {
                display: none !important;
            }
        }

        .hero-section {
            background-image: url("{{ asset('mpr1.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 80px 0;
            margin-top: -70px;
        }

        .hero-section h1 {
            font-family: 'Pacifico', cursive, sans-serif;
            font-size: 4rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-top: 50px;
            position: relative;
            top: 30px;
        }

        .hero-section p {
            font-size: 20px;
            font-weight: bold;
            margin-top: 100px;
        }

        .content-section p {
            text-align: justify;
            line-height: 1.8;
            margin-top: 15px;
        }

        .calendar-section {
            text-align: center;
            padding: 30px 0;
            background-color: #fff;
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            background-color: #fff;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-custom img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .card-subtitle {
            font-size: 14px;
            color: gray;
        }

        .search-icon {
            cursor: pointer;
            color: white;
            font-size: 1rem;
            margin-left: 15px;
        }

        .submenu-mobile {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.5s ease, opacity 0.5s ease;
            opacity: 0;
            padding-left: 10px;
        }

        .submenu-mobile.show {
            max-height: 1000px;
            opacity: 1;
            padding-top: 5px;
        }

        .dropdown-mobile {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .dropdown-mobile>a {
            font-weight: bold;
            color: white;
            cursor: pointer;
        }

        .submenu-mobile a {
            font-weight: normal;
            font-size: 0.95rem;
            color: white;
            text-decoration: none;
        }

        .submenu-mobile a:hover {
            color: #ccc;
        }

        .letter-card {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .letter-item {
            text-align: center;
        }

        /* Top-bar fixed logo & burger */
        .top-bar-fixed {
            position: fixed;
            top: 15px;
            left: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10001;
            transition: all 0.3s ease-in-out;
        }

        .top-bar-fixed .logo img {
            height: 40px;
        }

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
</head>

<body>
    <!-- Burger & Logo tetap di atas -->
    <div class="top-bar-fixed" id="topBar">
        <div class="burger" onclick="toggleNav()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">
            <img src="{{ asset('logo1.png') }}" alt="Logo">
        </div>
    </div>

    @include('UI_Frontend.component.navbar')

    <main>
        @yield('content')
    </main>

    {{-- Toast container --}}
    @if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @include('UI_Frontend.component.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scroll & Burger Script -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const topBar = document.getElementById('topBar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                topBar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
                topBar.classList.remove('scrolled');
            }
        });
        const sidebar = document.querySelector('.nav-slide');
        sidebar.addEventListener('wheel', function(e) {
            // cegah scroll halaman utama
            e.preventDefault();
            // hitung posisi baru, batasi antara 0 dan max
            const maxScroll = sidebar.scrollHeight - sidebar.clientHeight;
            let newScroll = sidebar.scrollTop + e.deltaY;
            if (newScroll < 0) newScroll = 0;
            if (newScroll > maxScroll) newScroll = maxScroll;
            sidebar.scrollTop = newScroll;
        }, {
            passive: false
        });

        function toggleNav() {
            const nav = document.querySelector('.nav-slide');
            const burger = document.querySelector('.burger');
            nav.classList.toggle('show');
            burger.classList.toggle('open');
        }
        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('successToast');
            if (toastEl) {
            new bootstrap.Toast(toastEl, { delay: 3000 }).show();
            }
        });
    </script>
</body>

</html>