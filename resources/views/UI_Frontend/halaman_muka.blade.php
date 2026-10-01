@extends('UI_Frontend.index') 
@section('content')

<!-- Hero Section -->
<div class="hero-section">
  <div class="container">
    <h1>SITEMAN-SUCA</h1>
    <p class="lead">Sistem Informasi Temu-Kembali Pengiriman Surat Dengan Caraka</p>
  </div>
</div>

<!-- Tentang SITEMAN-SUCA -->
<section class="about-section py-5">
  <div class="container content-section" data-aos="fade-up" data-aos-duration="800">
    <h5 class="fw-bold">Tentang SITEMAN-SUCA</h5>
    <p> Sistem informasi ini digunakan untuk menyampaikan informasi pengiriman surat kepada Bagian Sekretariat Ketua (H. Ahmad Muzani), Bagian Sekretariat Wakil Ketua (Ir. Bambang Wuryanto, M.B.A.), Bagian Sekretariat Wakil Ketua (Drs. H. Kahar Muzakir), Bagian Sekretariat Wakil Ketua (Dr. Lestari Moerdijat, S.S., M.M.), Bagian Sekretariat Wakil Ketua (Rusdi Kirana, S.E.), Bagian Sekretariat Wakil Ketua (Dr. H. M. Hidayat Nur Wahid, MA.), Bagian Sekretariat Wakil Ketua (M. Eddy Dwiyanto Soeparno, S.H., M.H.), Bagian Sekretariat Wakil Ketua (Dr. Edhie Baskoro Yudhoyono, B.Com.), Bagian Sekretariat Wakil Ketua (Abcandra M.A Supratman, S.H.), serta Bagian Tata Usaha Pimpinan Sekretariat Jenderal untuk meningkatkan kualitas pelayanan surat-menyurat di Majelis Permusyawaratan Rakyat Republik Indonesia secara tepat, cepat, mudah, dan terjangkau.
        </p>
  </div>
</section>

<!-- Section Layanan -->
<section class="py-5">
  <div class="container">
    <p class="mb-4 fw-bold fs-5 text-center">Layanan Kami:</p>
    <div class="row text-center">

      @php
      // gunakan slug untuk semua wakil
      $anggotaMPR = [
        // Ketua (tetap)
        ["type"=>"ketua", "url" => route('surat.ketuampr'), "img" => "anggota_mpr/ahmad_muzani.jpg", "jabatan" => "Bagian Set. Ketua MPR", "nama" => "H. Ahmad Muzani"],

        // Wakil (dinamis by slug)
        ["type"=>"wakil","slug"=>"bambang-wuryanto-mba",          "img"=>"anggota_mpr/bambang_wuryanto.jpg", "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Ir. Bambang Wuryanto, M.B.A."],
        ["type"=>"wakil","slug"=>"kahar-muzakir",                 "img"=>"anggota_mpr/kahar_muzakir.jpg",    "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Drs. H. Kahar Muzakir"],
        ["type"=>"wakil","slug"=>"lestari-moerdijat-ss-mm",       "img"=>"anggota_mpr/lestari.jpg",          "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Dr. Lestari Moerdijat, S.S., M.M."],
        ["type"=>"wakil","slug"=>"rusdi-kirana-se",               "img"=>"anggota_mpr/rusdi.jpg",            "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Rusdi Kirana, S.E."],
        ["type"=>"wakil","slug"=>"hidayat-nur-wahid-ma",          "img"=>"anggota_mpr/hidayat_nur.jpg",      "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Dr. H. M. Hidayat Nur Wahid, MA."],
        ["type"=>"wakil","slug"=>"eddy-dwiyanto-soeparno-sh-mh",  "img"=>"anggota_mpr/eddy_soeparno.jpg",    "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"M. Eddy Dwiyanto Soeparno, S.H., M.H."],
        ["type"=>"wakil","slug"=>"edhie-baskoro-yudhoyono-bcom",  "img"=>"anggota_mpr/edhie_baskoro.jpg",    "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Dr. Edhie Baskoro Yudhoyono, B.Com."],
        ["type"=>"wakil","slug"=>"abcandra-ma-supratman-sh",      "img"=>"anggota_mpr/abcandra.jpg",         "jabatan"=>"Bagian Set. Wakil Ketua MPR", "nama"=>"Abcandra M.A Supratman, S.H."],

        // Lainnya
        ["type"=>"static","url"=>route('surat.bagianTU'),        "img"=>"anggota_mpr/setjen.jpg", "jabatan"=>"Bagian TU Pimpinan Setjen", "nama"=>"Bagian Tata Usaha"],
        ["type"=>"static","url"=>route('surat.bagianKearsipan'), "img"=>"anggota_mpr/setjen.jpg", "jabatan"=>"Bagian Kearsipan Dan Persuratan", "nama"=>"Kearsipan dan Persuratan"],
      ];

      $isLoggedIn = auth()->check();
      @endphp

      @foreach($anggotaMPR as $anggota)
      @php
        // tentukan URL tujuan
        $href = match($anggota['type']) {
          'ketua'  => $anggota['url'],
          'wakil'  => route('surat.wakilketuampr', $anggota['slug']),
          default  => $anggota['url'],
        };
      @endphp

      <div class="col-md-3 mb-4 d-flex">
          <a href="{{ $isLoggedIn ? $href : route('login') }}"
            data-aos="zoom-in"
           class="card-custom w-100 text-decoration-none text-dark"
           onclick="{{ $isLoggedIn ? '' : 'alert(\'Silakan login terlebih dahulu!\');' }}">
          <img src="{{ asset($anggota['img']) }}" alt="{{ $anggota['nama'] }}">
          <div class="card-body">
            <h5 class="fw-bold card-title">{{ $anggota['jabatan'] }}</h5>
            <p class="text-muted small card-subtitle">{{ $anggota['nama'] }}</p>
          </div>
        </a>
      </div>
      @endforeach

    </div>
  </div>
</section>

@endsection
