@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
<div class="sx-wrap">
  <a class="sx-brand" href="{{ $backUrl }}" aria-label="Kembali">
    <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI">
    <span><strong>SITEMAN-SUCA</strong><small>Sekretariat Jenderal MPR RI</small></span>
  </a>

  <div class="sx-card">
    <div class="sx-card-head">
      <span class="sx-card-icon"><i class="bi bi-envelope-plus" aria-hidden="true"></i></span>
      <div>
        <h1>Buat Surat (Caraka)</h1>
        <p>Pengirim otomatis <b>Caraka</b>; penerima ditulis manual.</p>
      </div>
    </div>

    <div class="sx-card-body">
      <form id="formSurat" method="POST" action="{{ route('surat.store') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="back" value="{{ base64_encode($backUrl ?? url()->previous()) }}">
        <input type="hidden" name="pengirim_id" value="{{ auth()->user()->id_users }}">
        <input type="hidden" name="pengirim_nama" value="{{ auth()->user()->name ?? 'Caraka' }}">

        <h2 class="sx-section">Informasi Surat</h2>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Pengirim</label>
            <input type="text" class="form-control" value="Caraka" disabled readonly tabindex="-1" aria-disabled="true">
            <small class="text-muted">Diatur otomatis oleh sistem.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Jenis Surat</label>
            <select name="jenis_surat" class="form-select @error('jenis_surat') is-invalid @enderror" required>
              <option value="keluar" selected>Keluar</option>
            </select>
            @error('jenis_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label">Judul Surat</label>
            <input type="text" name="judul_surat" class="form-control @error('judul_surat') is-invalid @enderror"
                   value="{{ old('judul_surat') }}" required placeholder="Masukkan judul surat">
            @error('judul_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Nomor Surat</label>
            <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror"
                   value="{{ old('nomor_surat') }}" required placeholder="Contoh: 001/MPR/2026">
            @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
              <option value="">-- pilih --</option>
              <option value="biasa" {{ old('kategori')=='biasa' ? 'selected':'' }}>Biasa</option>
              <option value="rahasia" {{ old('kategori')=='rahasia' ? 'selected':'' }}>Rahasia</option>
              <option value="sangat_rahasia" {{ old('kategori')=='sangat_rahasia' ? 'selected':'' }}>Sangat Rahasia</option>
            </select>
            @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label">Perihal</label>
            <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror"
                   value="{{ old('perihal') }}" required placeholder="Perihal surat">
            @error('perihal') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <h2 class="sx-section">Penerima</h2>
        <label class="form-label">Nama Penerima (Eksternal)</label>
        <input type="text" name="penerima_eksternal"
               class="form-control @error('penerima_eksternal') is-invalid @enderror"
               value="{{ old('penerima_eksternal') }}" placeholder="Nama penerima" required>
        @error('penerima_eksternal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted d-block mt-1">Caraka mengirim ke pihak eksternal di luar sistem (tidak memilih user internal).</small>

        <h2 class="sx-section">Dokumen</h2>
        <label class="form-label">File Surat (PDF/DOC/DOCX/XLS/XLSX)</label>
        <input type="file" id="file_surat" name="file_surat"
               class="form-control @error('file_surat') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
        @error('file_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted d-block mt-1">Ukuran maksimal 2 MB.</small>

        <div class="sx-actions">
          <a href="{{ $backUrl }}" class="sx-btn sx-btn-ghost" id="btn-batal">Batal</a>
          <button type="submit" class="sx-btn sx-btn-primary"><i class="bi bi-send" aria-hidden="true"></i>Simpan Surat</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#formSurat [required]').forEach(function (el) {
      el.addEventListener('invalid', function () { this.setCustomValidity('Kolom tidak boleh kosong'); });
      el.addEventListener('input', function () { this.setCustomValidity(''); });
    });
  });

  // Tombol Batal → kembali ke halaman asal + paksa refresh
  document.getElementById('btn-batal')?.addEventListener('click', function (e) {
    e.preventDefault();
    const ref = document.referrer;
    const fallback = @json($backUrl);
    let target = ref && !ref.includes('/surat/create-caraka') ? ref : fallback;
    target += (target.includes('?') ? '&' : '?') + 'refresh=1';
    window.location.replace(target);
  });
</script>
@endpush
