  <!-- Header atas: identitas, pencarian, dan translate -->
  <div class="site-header">
    <div class="header-top container d-flex justify-content-between align-items-center">
      <div class="navbar-brand" aria-label="Identitas MPR">
        <img src="{{ asset('logo1.webp') }}" alt="Logo DPR RI" width="164" height="142">
        <span>MAJELIS PERMUSYAWARATAN RAKYAT<br>REPUBLIK INDONESIA</span>
      </div>

      <div class="header-tools">
        @auth
          <form action="{{ route('surat.search') }}" method="GET" class="navbar-search-form">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Cari Disini..."
                   aria-label="Kata kunci pencarian surat" required>
            <button class="clear-search" type="button" aria-label="Hapus kata pencarian" title="Hapus pencarian">
              <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
            <button class="submit-search" type="submit" aria-label="Cari surat" title="Cari surat">
              <i class="bi bi-search" aria-hidden="true"></i>
            </button>
          </form>
        @endauth

      </div>
    </div>

    <!-- Navbar bawah: navigasi utama dan akun pengguna -->
    <nav class="navbar navbar-expand-lg">
      <div class="container d-flex justify-content-between align-items-center">

      <!-- Desktop Menu -->
      <div class="collapse navbar-collapse d-none d-lg-block">
        <ul class="navbar-nav ms-auto align-items-center">

          <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('halaman_muka') ? 'active' : '' }}"
               href="{{ route('halaman_muka') }}" aria-label="Halaman Muka" title="Halaman Muka">
              <i class="bi bi-house-door-fill" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('surat.ketuampr*') ? 'active' : '' }}"
               href="{{ route('surat.ketuampr') }}">Surat Ketua MPR</a>
          </li>
          <!-- Dropdown Wakil Ketua MPR -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::routeIs('surat.wakilketuampr*') ? 'active' : '' }}"
               href="#" id="navbarDropdownWakil" role="button" data-bs-toggle="dropdown"
               aria-expanded="false" aria-haspopup="true">
              Surat Wakil Ketua MPR
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownWakil">
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'bambang-wuryanto-mba']) }}">Ir. Bambang Wuryanto, M.B.A.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'kahar-muzakir']) }}">Drs. H. Kahar Muzakir</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'lestari-moerdijat-ss-mm']) }}">Dr. Lestari Moerdijat, S.S., M.M.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'rusdi-kirana-se']) }}">Rusdi Kirana, S.E.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'hidayat-nur-wahid-ma']) }}">Dr. H. M. Hidayat Nur Wahid, MA.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'eddy-dwiyanto-soeparno-sh-mh']) }}">M. Eddy Dwiyanto Soeparno, S.H., M.H.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'edhie-baskoro-yudhoyono-bcom']) }}">Dr. Edhie Baskoro Yudhoyono, B.Com.</a></li>
              <li><a class="dropdown-item" href="{{ route('surat.wakilketuampr', ['slug' => 'abcandra-ma-supratman-sh']) }}">Abcandra M.A Supratman, S.H.</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('surat.bagianTU*') ? 'active' : '' }}"
               href="{{ route('surat.bagianTU') }}">Bagian TU Pimpinan Setjen</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('surat.bagianKearsipan*') ? 'active' : '' }}"
               href="{{ route('surat.bagianKearsipan') }}">Bagian Persuratan dan Kearsipan</a>
          </li>

          <!-- Login / User Dropdown -->
          @auth
            <li class="nav-item dropdown ms-3 position-relative">
                <a class="nav-link dropdown-toggle user-menu-toggle" href="#" data-bs-toggle="dropdown"
                  data-bs-display="static" aria-expanded="false" aria-label="Menu pengguna" title="Menu pengguna">
                @if (Auth::user()->foto_url)
                  <img src="{{ Auth::user()->foto_url }}" alt="" class="user-photo" style="width:30px;height:30px;border-radius:50%;object-fit:cover;object-position:top;border:2px solid #d4af37;">
                @else
                  <i class="bi bi-person-circle" aria-hidden="true"></i>
                @endif
                <span class="user-name">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <form action="{{ route('logout') }}" method="POST" class="px-3">
                    @csrf
                    <button type="submit" class="btn">Log Out</button>
                  </form>
                </li>
              </ul>
            </li>
          @else
            <li class="nav-item ms-3">
              <a class="nav-link" href="{{ route('login') }}">Login</a>
            </li>
          @endauth

        </ul>
      </div>
    </div>
    </nav>
  </div>

  <!-- Mobile Slide Menu -->
  <div class="nav-slide" id="navMenu">
    <div class="language-switcher">
      <button class="translate-toggle" type="button" aria-label="Pilih bahasa" title="Pilih bahasa" aria-expanded="false">
        <i class="bi bi-translate" aria-hidden="true"></i>
        <span class="translate-label">Pilih bahasa</span>
      </button>
      <div class="translate-panel" aria-hidden="true">
        <div id="google_translate_element"></div>
      </div>
    </div>

    <div class="mobile-search">
      <button class="search-toggle mobile-search-toggle" type="button" aria-label="Buka pencarian surat" aria-expanded="false">
        <i class="bi bi-search" aria-hidden="true"></i>
        <span>Cari surat</span>
      </button>
      <div class="search-panel">
        <form action="{{ route('surat.search') }}" method="GET" class="d-flex gap-2">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                 placeholder="Cari surat..." aria-label="Kata kunci pencarian surat" required>
          <button class="btn btn-outline-light" type="submit" aria-label="Cari surat">
            <i class="bi bi-search" aria-hidden="true"></i>
          </button>
        </form>
      </div>
    </div>

    <a href="{{ route('halaman_muka') }}">Halaman Muka</a>
    <a href="{{ route('surat.ketuampr') }}">Surat Ketua MPR</a>

    <div class="dropdown-mobile">
      <a href="#"
         onclick="event.preventDefault(); this.classList.toggle('active'); this.nextElementSibling.classList.toggle('show');">
        Surat Wakil Ketua MPR <span class="chevron">▾</span>
      </a>
      <div class="submenu-mobile">
        <!-- semua link pakai key param 'slug' -->
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'bambang-wuryanto-mba']) }}">Ir. Bambang Wuryanto, M.B.A.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'kahar-muzakir']) }}">Drs. H. Kahar Muzakir</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'lestari-moerdijat-ss-mm']) }}">Dr. Lestari Moerdijat, S.S., M.M.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'rusdi-kirana-se']) }}">Rusdi Kirana, S.E.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'hidayat-nur-wahid-ma']) }}">Dr. H. M. Hidayat Nur Wahid, MA.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'eddy-dwiyanto-soeparno-sh-mh']) }}">M. Eddy Dwiyanto Soeparno, S.H., M.H.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'edhie-baskoro-yudhoyono-bcom']) }}">Dr. Edhie Baskoro Yudhoyono, B.Com.</a>
        <a href="{{ route('surat.wakilketuampr', ['slug' => 'abcandra-ma-supratman-sh']) }}">Abcandra M.A Supratman, S.H.</a>
      </div>
    </div>

    <a href="{{ route('surat.bagianTU') }}">Bagian TU Pimpinan</a>
    <a href="{{ route('surat.bagianKearsipan') }}">Bagian Persuratan dan Kearsipan</a>

    @auth
      <div class="dropdown-mobile mobile-user">
        <a href="#" aria-label="Menu pengguna"
           onclick="event.preventDefault(); this.classList.toggle('active'); this.nextElementSibling.classList.toggle('show');">
          <span class="mobile-user-identity">
            @if (Auth::user()->foto_url)
              <img src="{{ Auth::user()->foto_url }}" alt="" class="mobile-user-photo">
            @else
              <i class="bi bi-person-circle" aria-hidden="true"></i>
            @endif
            <span>{{ Auth::user()->name }}</span>
          </span>
          <span class="chevron">▾</span>
        </a>
        <div class="submenu-mobile">
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="mobile-logout-btn">
              <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Log Out
            </button>
          </form>
        </div>
      </div>
    @endauth
  </div>

  <script>
    document.querySelectorAll('.search-toggle').forEach((toggle) => {
      toggle.addEventListener('click', () => {
        const wrapper = toggle.closest('.nav-search, .mobile-search');
        const isOpen = wrapper.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));

        document.querySelectorAll('.nav-search.is-open, .mobile-search.is-open').forEach((other) => {
          if (other !== wrapper) {
            other.classList.remove('is-open');
            other.querySelector('.search-toggle').setAttribute('aria-expanded', 'false');
          }
        });

        if (isOpen) {
          wrapper.querySelector('input[type="search"]').focus();
        }
      });
    });

    document.addEventListener('click', (event) => {
      document.querySelectorAll('.nav-search.is-open, .mobile-search.is-open').forEach((wrapper) => {
        if (!wrapper.contains(event.target)) {
          wrapper.classList.remove('is-open');
          wrapper.querySelector('.search-toggle').setAttribute('aria-expanded', 'false');
        }
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        document.querySelectorAll('.nav-search.is-open, .mobile-search.is-open').forEach((wrapper) => {
          wrapper.classList.remove('is-open');
          wrapper.querySelector('.search-toggle').setAttribute('aria-expanded', 'false');
        });
      }
    });

    const translateSwitcher = document.querySelector('.language-switcher');
    const translateToggle = document.querySelector('.translate-toggle');

    if (translateSwitcher && translateToggle) {
      translateToggle.addEventListener('click', () => {
        const isOpen = translateSwitcher.classList.toggle('is-open');
        translateToggle.setAttribute('aria-expanded', String(isOpen));
        translateSwitcher.querySelector('.translate-panel').setAttribute('aria-hidden', String(!isOpen));

        if (isOpen && !window.googleTranslateScriptRequested) {
          window.googleTranslateScriptRequested = true;
          const translateScript = document.createElement('script');
          translateScript.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
          translateScript.async = true;
          translateScript.onerror = () => {
            window.googleTranslateScriptRequested = false;
            console.error('Google Translate failed to load.');
          };
          document.body.appendChild(translateScript);
        }
      });

      document.addEventListener('click', (event) => {
        if (!translateSwitcher.contains(event.target)) {
          translateSwitcher.classList.remove('is-open');
          translateToggle.setAttribute('aria-expanded', 'false');
          translateSwitcher.querySelector('.translate-panel').setAttribute('aria-hidden', 'true');
        }
      });
    }

    document.querySelectorAll('.navbar-search-form').forEach((searchForm) => {
      const searchInput = searchForm.querySelector('input[type="search"]');
      const clearSearch = searchForm.querySelector('.clear-search');

      const updateClearButton = () => {
        searchForm.classList.toggle('has-value', searchInput.value.trim() !== '');
      };

      searchInput.addEventListener('input', updateClearButton);
      clearSearch.addEventListener('click', () => {
        searchInput.value = '';
        updateClearButton();
        searchInput.focus();
      });

      updateClearButton();
    });
  </script>
  <script>
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'id,en',
        autoDisplay: false
      }, 'google_translate_element');
    }
  </script>
