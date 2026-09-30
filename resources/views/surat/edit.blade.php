@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <h2>Edit Surat</h2>

  {{-- gunakan id_surats untuk primary key --}}
  <form id="formEditSurat" action="{{ route('surat.update', $surat->id_surats) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- alamat asal (base64) dikirim ke controller --}}
    <input type="hidden" name="back" value="{{ $backHash ?? base64_encode(url()->previous()) }}">

    {{-- Judul --}}
    <div class="mb-3">
      <label for="judul_surat" class="form-label">Judul Surat <span class="text-danger">*</span></label>
      <input type="text" id="judul_surat" name="judul_surat"
        class="form-control @error('judul_surat') is-invalid @enderror"
        value="{{ old('judul_surat', $surat->judul_surat) }}" required
        oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
        oninput="this.setCustomValidity('')">
      @error('judul_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Nomor --}}
    <div class="mb-3">
      <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
      <input type="text" id="nomor_surat" name="nomor_surat"
        class="form-control @error('nomor_surat') is-invalid @enderror"
        value="{{ old('nomor_surat', $surat->nomor_surat) }}" required
        oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
        oninput="this.setCustomValidity('')">
      @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Info lain (non-editable) --}}
    <div class="mb-3">
      <label class="form-label">Jenis Surat</label>
      <input type="text" class="form-control" value="{{ ucfirst($surat->jenis_surat) }}" disabled readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Kategori</label>
      <input type="text" class="form-control" value="{{ ucfirst($surat->kategori) }}" disabled readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Perihal</label>
      <input type="text" class="form-control" value="{{ $surat->perihal }}" disabled readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Pengirim</label>
      <input type="text" class="form-control" 
        value="{{ optional($surat->user)->name ?? $surat->pengirim_nama ?? '-' }}" disabled readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Penerima</label>
      <input type="text" class="form-control"
        value="{{ optional($surat->penerima)->name ?? $surat->penerima_eksternal ?? '-' }}" disabled readonly>
    </div>

    {{-- File surat baru (opsional update) --}}
    <div class="mb-3">
      <label for="file_surat" class="form-label">Ganti File Surat (opsional)</label>
      <input type="file" id="file_surat" name="file_surat"
        class="form-control @error('file_surat') is-invalid @enderror"
        accept=".pdf,.doc,.docx,.xls,.xlsx">
      @error('file_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror

      @if(!empty($surat->file_surat))
        <small class="text-muted">
          File saat ini: <a href="{{ asset($surat->file_surat) }}" target="_blank">{{ basename($surat->file_surat) }}</a>
        </small>
      @endif
    </div>

    <div class="d-flex gap-2">
      <a href="{{ $backUrl ?? url()->previous() }}" id="btn-batal" class="btn btn-outline-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
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
