<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SITEMAN-SUCA</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">

    <!-- Bootstrap & Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        .surat-table-scroll {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .surat-table-scroll > .table {
            min-width: 1100px;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding-top: 200px;
            overflow-x: hidden;
        }

        .site-header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: #fff;
        }

        .header-top {
            display: grid !important;
            grid-template-columns: minmax(310px, 1fr) minmax(520px, 1.45fr);
            min-height: 136px;
            background: #fff;
            gap: 40px;
        }

        .header-tools {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
        }

        /* Styling untuk navigasi bawah */
        nav.navbar {
            background-color: #000 !important;
            position: static;
            width: 100%;
            min-height: 64px;
            padding: .55rem 0;
            transition: background-color 0.4s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Poppins-Bold', sans-serif;
            box-shadow: 0 3px 14px rgba(0, 0, 0, .16);
        }


        nav.navbar.scrolled {
            background-color: #000 !important;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #222 !important;
            font-weight: bold;
            font-size: 1.05rem;
            line-height: 1.35;
            text-decoration: none;
            font-family: 'Poppins-Bold', sans-serif;
            white-space: nowrap;
        }

        .navbar-brand span {
            font-family: 'Work Sans', sans-serif;
            font-size: 1.25rem;
            font-style: normal !important;
            font-weight: 700;
            letter-spacing: .01em;
            line-height: 1.25;
        }

        .navbar-brand img {
            width: 82px;
            max-height: 82px;
            object-fit: contain;
        }

        .navbar-nav .nav-item {
            position: relative;
        }

        .navbar-nav {
            width: 100%;
            justify-content: space-between;
            margin-left: 0 !important;
            gap: .5rem;
        }

        .navbar-search-form {
            width: min(780px, 100%);
            height: 62px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 16px 0 24px;
            border: 1px solid #f5a623;
            border-radius: 999px;
            background: #fff;
        }

        .navbar-search-form input {
            min-width: 0;
            flex: 1;
            border: 0;
            outline: 0;
            color: #333;
            font-family: inherit;
            font-size: 1rem;
        }

        .navbar-search-form input::placeholder {
            color: #52627a;
            opacity: 1;
        }

        .navbar-search-form input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
        }

        .clear-search,
        .submit-search,
        .translate-toggle {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border: 0;
            background: transparent;
            color: #111;
            font-size: 1.35rem;
        }

        .clear-search {
            visibility: hidden;
            opacity: 0;
            transform: scale(.8);
            transition: opacity .18s ease, transform .18s ease, visibility .18s;
        }

        .navbar-search-form.has-value .clear-search {
            visibility: visible;
            opacity: 1;
            transform: scale(1);
        }

        .clear-search:hover,
        .submit-search:hover,
        .translate-toggle:hover {
            color: #f5a623;
        }

        .language-switcher {
            position: relative;
            margin-left: 8px;
        }

        .translate-toggle {
            width: 48px;
            height: 48px;
            font-size: 1.7rem;
        }

        .translate-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1100;
            min-width: 180px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .14);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: opacity .2s ease, transform .2s ease, visibility .2s;
        }

        .language-switcher.is-open .translate-panel {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .translate-panel .goog-te-gadget {
            color: #333;
            font-family: inherit;
            font-size: .8rem;
        }

        .translate-panel .goog-te-combo {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #d9dce1;
            border-radius: 5px;
            color: #333;
            font-size: .8rem;
        }

        .translate-panel .goog-te-gadget-icon {
            width: 28px !important;
            height: 28px !important;
            margin-right: 6px !important;
            background-image: url('{{ asset('logo1.png') }}') !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
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
            color: #111 !important;
            background: #f2f2f2;
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
            background-color: #252525;
            border-radius: .5rem;
        }

        .navbar-nav .nav-link,
        .navbar-nav .separator,
        .search-toggle {
            color: #fff !important;
            font-size: .88rem;
        }

        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        .navbar-nav .nav-link i {
            font-size: 1.15rem;
        }

        .user-menu-toggle i {
            font-size: 1.35rem !important;
        }

        .user-menu-toggle .user-name {
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-menu-toggle {
            display: inline-flex !important;
            align-items: center;
            gap: 8px;
            padding: .7rem .9rem !important;
            border-radius: 12px;
            background: #242424;
            border: 1px solid rgba(255, 255, 255, .85);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08),
                0 3px 8px rgba(0, 0, 0, .28);
        }

        .user-menu-toggle + .dropdown-menu {
            right: auto;
            left: auto;
            min-width: 140px !important;
            width: 140px !important;
            max-width: 140px !important;
        }

        .user-menu-fixed {
            position: fixed !important;
            z-index: 9999 !important;
            margin: 0 !important;
            min-width: 140px !important;
            width: 140px !important;
            max-width: 140px !important;
            overflow: visible !important;
            border: 1px solid rgba(0, 0, 0, .18);
            box-shadow: 0 8px 18px rgba(0, 0, 0, .2);
        }

        .user-menu-fixed .dropdown-item-text {
            display: block !important;
            overflow: visible !important;
            white-space: nowrap !important;
            text-overflow: clip !important;
            width: max-content !important;
            max-width: none !important;
            font-size: 1rem !important;
            line-height: 1.3;
            padding: .65rem 1rem;
            padding-right: 20px !important;
        }

        .user-menu-fixed form {
            width: 100%;
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .user-menu-fixed form .btn {
            width: 100%;
        }

        .navbar-nav .nav-link.active,
        .navbar-nav .nav-item.active .nav-link {
            color: #f5a623 !important;
            border-radius: 0.5rem;
        }

        .navbar-nav .dropdown-toggle::after {
            display: inline-block;
            border-top-color: #fff;
            transition: transform .28s cubic-bezier(.22, .61, .36, 1);
            transform-origin: center;
        }

        .navbar-nav .dropdown.show > .dropdown-toggle::after,
        .navbar-nav .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        /* Styling untuk dropdown */
        .navbar-nav .dropdown-menu {
            display: block;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-10px) scale(.98);
            transform-origin: top center;
            transition: opacity .28s ease, transform .28s ease, visibility .28s;
        }

        .navbar-nav .dropdown-menu.show {
            display: block !important;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
            z-index: 1200;
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
            background-color: #111;
            transition: all 0.3s ease-in-out;
            left: 0;
        }

        .burger:hover span {
            background-color: #f5a623;
        }

        /* Warna burger berubah jadi silver saat scroll */
        .top-bar-fixed.scrolled .burger span {
            background-color: #111 !important;
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
            body {
                padding-top: 82px;
            }

            .header-top {
                display: flex !important;
                min-height: 82px;
                padding: .5rem 1rem;
            }

            .header-tools {
                min-width: 0;
                gap: 2px;
            }

            .header-tools .navbar-search-form {
                display: none;
            }

            nav.navbar {
                display: none !important;
            }

            .navbar-brand {
                gap: 8px;
                margin-left: 45px;
                min-width: 0;
                font-size: .65rem;
                line-height: 1.25;
            }

            .navbar-brand span {
                overflow: hidden;
                font-size: .58rem;
                text-overflow: ellipsis;
            }

            .navbar-brand img {
                width: 46px;
                max-height: 46px;
            }

            .navbar-search-form {
                width: min(220px, 42vw);
                height: 36px;
                padding-left: 10px;
                padding-right: 4px;
            }

            .navbar-search-form input {
                font-size: .65rem;
            }

            .clear-search,
            .submit-search,
            .translate-toggle {
                width: 28px;
                height: 28px;
                font-size: .9rem;
            }

            .burger {
                display: block;
            }

            .nav-slide {
                top: 82px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .top-bar-fixed {
                top: 30px;
                left: 16px;
            }

            .hero-section h1 {
                font-size: clamp(1.9rem, 9vw, 3rem);
                white-space: nowrap;
                margin-top: 25px;
                top: 0;
            }

            .hero-section p {
                max-width: 90%;
                margin: 56px auto 0;
                font-size: 1rem;
                line-height: 1.5;
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
            margin-top: 0;
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
            color: #344054;
            font-size: 1.05rem;
        }

        .container > h2:first-child {
            margin-top: 2.25rem;
            margin-bottom: 1.5rem;
        }

        .about-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(120deg, #f8fafc 0%, #eef4f7 52%, #fff8ec 100%);
            border-top: 1px solid rgba(245, 166, 35, .2);
            border-bottom: 1px solid rgba(20, 45, 70, .08);
        }

        .about-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 28%;
            height: 4px;
            background: #f5a623;
            animation: aboutAccent 5s ease-in-out infinite alternate;
        }

        .about-section .content-section {
            position: relative;
            z-index: 1;
            max-width: 1180px;
        }

        .about-section h5 {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            color: #142d46;
            font-size: clamp(1.25rem, 2vw, 1.65rem);
        }

        .about-section h5::before {
            content: '';
            width: 9px;
            height: 28px;
            border-radius: 3px;
            background: #f5a623;
        }

        @keyframes aboutAccent {
            from { width: 18%; }
            to { width: 42%; }
        }

        @media (prefers-reduced-motion: reduce) {
            .about-section::before {
                animation: none;
            }
        }

        .calendar-section {
            text-align: center;
            padding: 30px 0;
            background-color: #fff;
        }

        .bagian-persuratan {
            position: relative;
            overflow: hidden;
            padding: 72px 0;
            background: linear-gradient(135deg, #eef4f7 0%, #fff 58%, #fff7e9 100%);
            border-top: 1px solid rgba(245, 166, 35, .18);
        }

        .map-frame {
            position: relative;
            height: 100%;
            min-height: 320px;
            overflow: hidden;
            border: 1px solid rgba(20, 45, 70, .12);
            border-radius: 14px;
            background: #dfe7ec;
            box-shadow: 0 10px 24px rgba(20, 45, 70, .12),
                0 24px 44px rgba(20, 45, 70, .08);
            transition: transform .45s ease, box-shadow .45s ease;
        }

        .map-frame:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 30px rgba(20, 45, 70, .16),
                0 30px 50px rgba(20, 45, 70, .1);
        }

        .map-frame iframe {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 320px;
            border: 0;
            transition: filter .8s ease;
        }

        .bagian-persuratan.is-night .map-frame iframe {
            filter: invert(.88) hue-rotate(180deg) brightness(.78) contrast(1.08) saturate(.78);
        }

        .map-mode-label {
            position: absolute;
            right: 14px;
            bottom: 14px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, .55);
            border-radius: 999px;
            background: rgba(20, 45, 70, .78);
            color: #fff;
            font-size: .78rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
        }

        .contact-panel {
            height: 100%;
            padding: 34px 32px;
            border: 1px solid rgba(20, 45, 70, .1);
            border-radius: 14px;
            background: rgba(255, 255, 255, .84);
            box-shadow: 0 10px 24px rgba(20, 45, 70, .08);
            text-align: left;
        }

        .contact-eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            color: #d48806;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .contact-panel h2 {
            margin: 0;
            color: #142d46;
            font-size: clamp(1.25rem, 2vw, 1.7rem);
            line-height: 1.25;
        }

        .contact-panel > p {
            margin: 10px 0 26px;
            color: #52627a;
            font-size: .95rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .contact-details {
            display: grid;
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid rgba(20, 45, 70, .12);
        }

        .contact-details p {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 0;
            color: #344054;
            line-height: 1.5;
        }

        .contact-details i {
            flex: 0 0 20px;
            color: #d48806;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .bagian-persuratan {
                padding: 48px 0;
            }

            .map-frame,
            .map-frame iframe {
                min-height: 280px;
            }

            .contact-panel {
                padding: 26px 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .map-frame,
            .map-frame iframe {
                transition: none;
            }
        }

        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .08),
                0 12px 24px rgba(20, 45, 70, .06);
            text-align: center;
            padding: 20px;
            background-color: #fff;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
            transition: transform .55s cubic-bezier(.16, 1, .3, 1),
                box-shadow .55s cubic-bezier(.16, 1, .3, 1);
            will-change: transform, box-shadow;
        }

        .card-custom:hover,
        .card-custom:focus-visible {
            transform: translate3d(0, -10px, 0) !important;
            z-index: 2;
            box-shadow: 0 12px 20px rgba(20, 45, 70, .14),
                0 26px 44px rgba(20, 45, 70, .12);
            outline: none;
        }

        .card-custom img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        @media (prefers-reduced-motion: reduce) {
            .card-custom {
                transition: none;
            }
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
            top: 42px;
            left: 24px;
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
    <!-- Burger untuk navigasi mobile -->
    <div class="top-bar-fixed" id="topBar">
        <div class="burger" onclick="toggleNav()">
            <span></span>
            <span></span>
            <span></span>
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
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <!-- Scroll & Burger Script -->
    <script>
        AOS.init({
            duration: 800,
            once: false,
            offset: 80
        });

        window.addEventListener('pageshow', function(event) {
            const navigationEntry = performance.getEntriesByType('navigation')[0];
            const cameFromHistory = navigationEntry?.type === 'back_forward';

            if (event.persisted || cameFromHistory) {
                window.location.reload();
            }
        });

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