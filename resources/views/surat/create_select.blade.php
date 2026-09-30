@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">Buat Surat</h3>

  <form id="formSurat" method="POST" action="{{ route('surat.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Judul --}}
    <div class="mb-3">
      <label class="form-label">Judul Surat</label>
      <input type="text" name="judul_surat"
             class="form-control @error('judul_surat') is-invalid @enderror"
             value="{{ old('judul_surat') }}" required
             oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
             oninput="this.setCustomValidity('')">
      @error('judul_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Nomor --}}
    <div class="mb-3">
      <label class="form-label">Nomor Surat</label>
      <input type="text" name="nomor_surat"
             class="form-control @error('nomor_surat') is-invalid @enderror"
             value="{{ old('nomor_surat') }}" required
             oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
             oninput="this.setCustomValidity('')">
      @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Jenis Surat --}}
    <div class="mb-3">
      <label class="form-label">Jenis Surat</label>
      <select id="jenisSelect" name="jenis_surat"
              class="form-select @error('jenis_surat') is-invalid @enderror"
              required oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
              oninput="this.setCustomValidity('')">
        <option value="">-- pilih --</option>
        <option value="masuk"  {{ old('jenis_surat')=='masuk'  ? 'selected' : '' }}>Masuk</option>
        <option value="keluar" {{ old('jenis_surat')=='keluar' ? 'selected' : '' }}>Keluar</option>
      </select>
      @error('jenis_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Pengirim (otomatis user yang login) --}}
    <input type="hidden" name="pengirim_id" value="{{ auth()->user()->id_users }}">

    <div class="mb-3">
      <label class="form-label">Nama Pengirim</label>
      <input type="text" name="pengirim_nama"
             class="form-control @error('pengirim_nama') is-invalid @enderror"
             value="{{ auth()->user()->name }}" readonly>
      @error('pengirim_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Kategori --}}
    <div class="mb-3">
      <label class="form-label">Kategori</label>
      <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
        <option value="">-- pilih --</option>
        <option value="biasa"          {{ old('kategori')=='biasa'          ? 'selected' : '' }}>Biasa</option>
        <option value="rahasia"        {{ old('kategori')=='rahasia'        ? 'selected' : '' }}>Rahasia</option>
        <option value="sangat_rahasia" {{ old('kategori')=='sangat_rahasia' ? 'selected' : '' }}>Sangat Rahasia</option>
      </select>
      @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- File --}}
    <div class="mb-3">
      <label class="form-label">File Surat (PDF/DOC/DOCX/XLS/XLSX)</label>
      <input type="file" id="file_surat" name="file_surat"
             class="form-control @error('file_surat') is-invalid @enderror"
             accept=".pdf,.doc,.docx,.xls,.xlsx"
             required>
      @error('file_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Perihal --}}
    <div class="mb-3">
      <label class="form-label">Perihal</label>
      <input type="text" name="perihal"
             class="form-control @error('perihal') is-invalid @enderror"
             value="{{ old('perihal') }}" required>
      @error('perihal') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Penerima --}}
    <div class="mb-3">
      <label class="form-label">Penerima</label>
      <select id="penerimaSelect" name="penerima_id"
              class="form-select @error('penerima_id') is-invalid @enderror" required>
        <option value="">-- pilih penerima --</option>

        {{-- Pimpinan (Ketua & Wakil) --}}
        @if(isset($pimpinan) && count($pimpinan))
          <optgroup label="Pimpinan (Ketua & Wakil)">
            @foreach($pimpinan as $p)
              <option value="{{ $p->id_users }}"
                      data-role="pimpinan"
                      {{ old('penerima_id') == $p->id_users ? 'selected' : '' }}>
                {{ $p->name }}
              </option>
            @endforeach
          </optgroup>
        @endif

        {{-- Bagian TU --}}
        @if(isset($bagianTu) && count($bagianTu))
          <optgroup label="Bagian TU">
            @foreach($bagianTu as $tu)
              <option value="{{ $tu->id_users }}"
                      data-role="bagian_tu"
                      {{ old('penerima_id') == $tu->id_users ? 'selected' : '' }}>
                {{ $tu->name }}
              </option>
            @endforeach
          </optgroup>
        @endif
      </select>
      @error('penerima_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
      <small class="text-muted">Jika memilih Bagian TU, Jenis Surat akan otomatis dikunci ke “Masuk”.</small>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ $backUrl }}" class="btn btn-outline-secondary" id="btn-batal">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    const penerima = document.getElementById('penerimaSelect');
    const jenisSel = document.getElementById('jenisSelect');

    function ensureHiddenJenis(value) {
      let hid = document.getElementById('jenis_surat_hidden');
      if (!hid) {
        hid = document.createElement('input');
        hid.type = 'hidden';
        hid.id = 'jenis_surat_hidden';
        hid.name = 'jenis_surat';
        jenisSel.parentNode.appendChild(hid);
      }
      hid.value = value;
    }
    function removeHiddenJenis() {
      const hid = document.getElementById('jenis_surat_hidden');
      if (hid) hid.remove();
    }

    function lockJenisMasuk(lock) {
      if (lock) {
        jenisSel.value = 'masuk';
        jenisSel.disabled = true;
        ensureHiddenJenis('masuk');
      } else {
        jenisSel.disabled = false;
        removeHiddenJenis();
      }
    }

    function isBagianTUOption(opt) {
      if (!opt) return false;
      const role = (opt.dataset.role || '').toLowerCase();
      return role === 'bagian_tu' ||
        (opt.textContent || '').toLowerCase().includes('bagian tu');
    }

    function applyLockFromSelection() {
      const opt = penerima.options[penerima.selectedIndex];
      lockJenisMasuk(isBagianTUOption(opt));
    }

    applyLockFromSelection();
    penerima.addEventListener('change', applyLockFromSelection);

    // Tombol Batal → kembali ke halaman sebelumnya
    document.getElementById('btn-batal')?.addEventListener('click', function (e) {
      e.preventDefault();
      const ref = document.referrer;
      const fallback = @json($backUrl);
      let target = ref && !ref.includes('/surat/create') ? ref : fallback;
      target += (target.includes('?') ? '&' : '?') + 'refresh=1';
      window.location.replace(target);
    });
  })();
</script>
@endpush
