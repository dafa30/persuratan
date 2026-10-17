@extends('layouts.app')

@section('no_navbar', '')

@push('styles')
  @include('surat.partials.modern-style')
@endpush

@section('content')
@php $backTo = $backUrl ?? route('surat.bagianTU_biasa'); @endphp
<div class="sx-wrap">
  <a class="sx-brand" href="{{ $backTo }}" aria-label="Kembali">
    <img src="{{ asset('logo1.png') }}" alt="Logo MPR RI">
    <span><strong>SITEMAN-SUCA</strong><small>Sekretariat Jenderal MPR RI</small></span>
  </a>

  <div class="sx-card">
    <div class="sx-card-head">
      <span class="sx-card-icon"><i class="bi bi-camera" aria-hidden="true"></i></span>
      <div>
        <h1>Upload Bukti Penerimaan</h1>
        <p>Unggah foto bukti surat telah diterima (Caraka).</p>
      </div>
    </div>

    <div class="sx-card-body">
      <form method="POST" action="{{ route('surat.update.caraka', $surat) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="back" value="{{ $backHash ?? base64_encode(route('surat.bagianTU_biasa')) }}">

        <h2 class="sx-section">Bukti Foto</h2>
        <label class="form-label">Bukti Foto (JPG/PNG)</label>
        <input type="file" name="file_bukti_terima" accept="image/png,image/jpeg"
               class="form-control @error('file_bukti_terima') is-invalid @enderror">
        @error('file_bukti_terima') <div class="invalid-feedback">{{ $message }}</div> @enderror

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
  // Batal → balik ke halaman asal + paksa refresh
  document.getElementById('btn-batal')?.addEventListener('click', function(e) {
    e.preventDefault();
    let target = @json($backUrl ?? route('surat.bagianTU_biasa'));
    target += (target.includes('?') ? '&' : '?') + 'refresh=1';
    window.location.replace(target);
  });

  // Auto-reload saat kembali via back/forward & bersihkan ?refresh
  window.addEventListener('pageshow', function(e) {
    const nav = performance.getEntriesByType('navigation')[0];
    if (e.persisted || (nav && nav.type === 'back_forward')) {
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
