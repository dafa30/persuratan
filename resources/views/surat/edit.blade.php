@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
@php $backTo = $backUrl ?? url()->previous(); @endphp
<div class="sx-wrap">
  <a class="sx-brand" href="{{ $backTo }}" aria-label="Kembali">
    <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI">
    <span><strong>SITEMAN-SUCA</strong><small>Sekretariat Jenderal MPR RI</small></span>
  </a>

  <div class="sx-card">
    <div class="sx-card-head">
      <span class="sx-card-icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
      <div>
        <h1>Edit Surat</h1>
        <p>Perbarui judul, nomor, atau ganti dokumen surat.</p>
      </div>
    </div>

    <div class="sx-card-body">
      <form id="formEditSurat" action="{{ route('surat.update', $surat->id_surats) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="back" value="{{ $backHash ?? base64_encode(url()->previous()) }}">

        <h2 class="sx-section">Ringkasan Surat</h2>
        <div class="sx-meta">
          <div class="sx-chip"><span>Jenis</span><strong>{{ ucfirst($surat->jenis_surat) }}</strong></div>
          <div class="sx-chip"><span>Kategori</span><strong>{{ ucfirst(str_replace('_', ' ', $surat->kategori)) }}</strong></div>
          <div class="sx-chip"><span>Pengirim</span><strong>{{ optional($surat->user)->name ?? $surat->pengirim_nama ?? '-' }}</strong></div>
          <div class="sx-chip"><span>Penerima</span><strong>{{ optional($surat->penerima)->name ?? $surat->penerima_eksternal ?? '-' }}</strong></div>
          <div class="sx-chip" style="grid-column: 1 / -1;"><span>Perihal</span><strong>{{ $surat->perihal ?: '-' }}</strong></div>
        </div>

        <h2 class="sx-section">Data yang Dapat Diubah</h2>
        <div class="row g-3">
          <div class="col-12">
            <label for="judul_surat" class="form-label">Judul Surat <span class="text-danger">*</span></label>
            <input type="text" id="judul_surat" name="judul_surat"
              class="form-control @error('judul_surat') is-invalid @enderror"
              value="{{ old('judul_surat', $surat->judul_surat) }}" required
              oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
              oninput="this.setCustomValidity('')">
            @error('judul_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
            <input type="text" id="nomor_surat" name="nomor_surat"
              class="form-control @error('nomor_surat') is-invalid @enderror"
              value="{{ old('nomor_surat', $surat->nomor_surat) }}" required
              oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
              oninput="this.setCustomValidity('')">
            @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <h2 class="sx-section">Dokumen</h2>
        <label for="file_surat" class="form-label">Ganti File Surat (opsional)</label>
        <input type="file" id="file_surat" name="file_surat"
          class="form-control @error('file_surat') is-invalid @enderror"
          accept=".pdf,.doc,.docx,.xls,.xlsx">
        @error('file_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if(!empty($surat->file_surat))
          <small class="text-muted d-block mt-2">
            <i class="bi bi-paperclip me-1" aria-hidden="true"></i>File saat ini:
            <a href="{{ route('surat.file', ['surat' => $surat->id_surats]) }}" target="_blank">{{ basename($surat->file_surat) }}</a>
          </small>
        @endif

        <div class="sx-actions">
          <a href="{{ $backTo }}" id="btn-batal" class="sx-btn sx-btn-ghost">Batal</a>
          <button type="submit" class="sx-btn sx-btn-primary"><i class="bi bi-check2-circle" aria-hidden="true"></i>Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Tombol Batal → balik ke halaman asal + paksa refresh
  document.getElementById('btn-batal')?.addEventListener('click', function(e) {
    e.preventDefault();
    let target = @json($backUrl ?? url()->previous());
    target += (target.includes('?') ? '&' : '?') + 'refresh=1';
    window.location.replace(target);
  });

  // Auto-reload saat kembali via back/forward & bersihkan ?refresh
  window.addEventListener('pageshow', function(e) {
    if (e.persisted || (performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
      window.location.reload();
    }
    const sp = new URLSearchParams(location.search);
    if (sp.has('refresh')) {
      const url = new URL(location.href);
      url.searchParams.delete('refresh');
      history.replaceState({}, '', url);
    }
  });
</script>
@endpush
