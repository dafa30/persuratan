@php
    $createdAtWib = optional($surat->created_at)->timezone('Asia/Jakarta');
    $dibacaAtWib  = optional($surat->dibaca)->timezone('Asia/Jakarta');
    $kategoriLabel = ucwords(str_replace('_', ' ', $surat->kategori ?? ($kategori ?? '')));
    $statusDibaca = (bool) $dibacaAtWib;
@endphp

<div class="sx-wrap sx-wide">
  <div class="sx-toolbar">
    <div class="d-flex align-items-center gap-3">
      <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI" style="width:44px;height:44px;object-fit:contain">
      <div>
        <h1>{{ $surat->judul_surat ?? 'Detail Surat' }}</h1>
        <small>SITEMAN-SUCA &middot; Sekretariat Jenderal MPR RI</small>
      </div>
    </div>
    <a href="{{ $backTo }}" class="sx-btn sx-btn-ghost"><i class="bi bi-arrow-left" aria-hidden="true"></i>Kembali ke Daftar</a>
  </div>

  <div class="sx-card mb-4">
    <div class="sx-card-body">
      <div class="sx-meta">
        <div class="sx-chip"><span>Nomor Surat</span><strong>{{ $surat->nomor_surat ?? '-' }}</strong></div>
        <div class="sx-chip"><span>Jenis</span><strong>{{ ucfirst($surat->jenis_surat ?? '-') }}</strong></div>
        <div class="sx-chip"><span>Kategori</span><strong>{{ $kategoriLabel ?: '-' }}</strong></div>
        <div class="sx-chip"><span>Pengirim</span><strong>{{ optional($surat->user)->name ?? $surat->pengirim_nama ?? '-' }}</strong></div>
        <div class="sx-chip"><span>Penerima</span><strong>{{ optional($surat->penerima)->name ?? $surat->penerima_eksternal ?? '-' }}</strong></div>
        <div class="sx-chip"><span>Status</span>
          <strong><span class="sx-badge {{ $statusDibaca ? 'sx-badge-ok' : 'sx-badge-wait' }}">{{ $statusDibaca ? 'Dibaca' : 'Dikirim' }}</span></strong>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="sx-card sx-viewer h-100">
        <div class="sx-card-head" style="padding:14px 22px">
          <span class="sx-card-icon" style="width:36px;height:36px;font-size:17px"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i></span>
          <div><h1 style="font-size:16px">Dokumen Surat</h1></div>
        </div>
        @if(!empty($pdfPath))
          <iframe src="{{ $pdfPath }}#view=FitH" title="Dokumen PDF"></iframe>
        @else
          <div class="sx-empty"><i class="bi bi-file-earmark-x" aria-hidden="true"></i>File PDF tidak tersedia atau tidak ditemukan.</div>
        @endif
      </div>
    </div>

    <div class="col-lg-4">
      <div class="sx-card h-100">
        <div class="sx-card-head" style="padding:14px 22px">
          <span class="sx-card-icon" style="width:36px;height:36px;font-size:17px"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
          <div><h1 style="font-size:16px">Status Tracking</h1></div>
        </div>
        <div class="sx-card-body">
          <ol class="sx-track">
            <li class="sx-step">
              <span class="sx-dot"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
              <h3>Surat Dikirim</h3>
              <time>{{ $createdAtWib?->translatedFormat('d F Y, H:i') }} WIB</time>
              <p>Dokumen surat berhasil dikirim dan disimpan dalam sistem.</p>
            </li>
            @if($dibacaAtWib)
              <li class="sx-step">
                <span class="sx-dot"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                <h3>Surat Dibaca</h3>
                <time>{{ $dibacaAtWib->translatedFormat('d F Y, H:i') }} WIB</time>
                <p>Dokumen surat telah dibuka oleh penerima.</p>
              </li>
            @else
              <li class="sx-step pending">
                <span class="sx-dot"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                <h3>Menunggu Dibaca</h3>
                <p>Surat belum dibuka oleh penerima.</p>
              </li>
            @endif
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>
