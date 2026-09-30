@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Edit (Caraka) - Upload Bukti</h3>

    <form method="POST" action="{{ route('surat.update.caraka', $surat) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- penting: agar setelah simpan kembali ke halaman asal (Bagian TU) --}}
        <input type="hidden" name="back" value="{{ $backHash ?? base64_encode(route('surat.bagianTU_biasa')) }}">

        <div class="mb-3">
            <label class="form-label">Bukti Foto (JPG/PNG)</label>
            <input
                type="file"
                name="file_bukti_terima"
                accept="image/png,image/jpeg"
                class="form-control @error('file_bukti_terima') is-invalid @enderror">
            @error('file_bukti_terima') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ $backUrl ?? route('surat.bagianTU_biasa') }}" id="btn-batal" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
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
