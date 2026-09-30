@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Buat Surat (Caraka)</h3>

    <div class="alert alert-info">
        <strong>Catatan:</strong> Pengirim otomatis <b>Caraka</b>. Penerima ditulis manual (tidak dikirim via sistem
        internal).
    </div>

    <form id="formSurat" method="POST" action="{{ route('surat.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- URL kembali (aman, encoded) --}}
        <input type="hidden" name="back" value="{{ base64_encode($backUrl ?? url()->previous()) }}">

        {{-- Pengirim otomatis (Caraka) --}}
        <input type="hidden" name="pengirim_id" value="{{ auth()->user()->id_users }}">
        <input type="hidden" name="pengirim_nama" value="{{ auth()->user()->name ?? 'Caraka' }}">

        <div class="mb-3">
            <label class="form-label">Pengirim</label>
            <input type="text" class="form-control" value="Caraka" disabled readonly tabindex="-1" aria-disabled="true">
            <small class="text-muted">Diatur otomatis oleh sistem.</small>
        </div>

        {{-- Judul --}}
        <div class="mb-3">
            <label class="form-label">Judul Surat</label>
            <input type="text" name="judul_surat" class="form-control @error('judul_surat') is-invalid @enderror"
                value="{{ old('judul_surat') }}" required oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
                oninput="this.setCustomValidity('')">
            @error('judul_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Nomor --}}
        <div class="mb-3">
            <label class="form-label">Nomor Surat</label>
            <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror"
                value="{{ old('nomor_surat') }}" required oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
                oninput="this.setCustomValidity('')">
            @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Jenis Surat --}}
        <div class="mb-3">
            <label class="form-label">Jenis Surat</label>
            <select name="jenis_surat" class="form-select @error('jenis_surat') is-invalid @enderror" required>
                <option value="keluar" selected>Keluar</option>
            </select>
            @error('jenis_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                <option value="">-- pilih --</option>
                <option value="biasa" {{ old('kategori')=='biasa' ? 'selected':'' }}>Biasa</option>
                <option value="rahasia" {{ old('kategori')=='rahasia' ? 'selected':'' }}>Rahasia</option>
                <option value="sangat_rahasia" {{ old('kategori')=='sangat_rahasia' ? 'selected':'' }}>Sangat Rahasia
                </option>
            </select>
            @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- File --}}
        <div class="mb-3">
            <label class="form-label">File Surat (PDF/DOC/DOCX/XLS/XLSX)</label>
            <input type="file" id="file_surat" name="file_surat"
                class="form-control @error('file_surat') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx"
                required>
            @error('file_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Perihal --}}
        <div class="mb-3">
            <label class="form-label">Perihal</label>
            <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror"
                value="{{ old('perihal') }}" required oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')"
                oninput="this.setCustomValidity('')">
            @error('perihal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Penerima Eksternal --}}
        <div class="mb-3">
            <label class="form-label">Nama Penerima (Eksternal)</label>
            <input type="text" name="penerima_eksternal"
                class="form-control @error('penerima_eksternal') is-invalid @enderror"
                value="{{ old('penerima_eksternal') }}" placeholder="Nama penerima" required
                oninvalid="this.setCustomValidity('Kolom tidak boleh kosong')" oninput="this.setCustomValidity('')">
            @error('penerima_eksternal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="text-muted">Caraka mengirim ke pihak eksternal di luar sistem (tidak memilih user
                internal).</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary" id="btn-batal">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (!form) return;
            form.querySelectorAll('[required]').forEach(function(el) {
                el.addEventListener('invalid', function() {
                    this.setCustomValidity('Kolom tidak boleh kosong');
                });
                el.addEventListener('input', function() {
                    this.setCustomValidity('');
                });
            });
        });
    </script>
</div>
@endsection

@push('scripts')
<script>
    // Tombol Batal → kembali ke halaman asal + paksa refresh
    document.getElementById('btn-batal') ? .addEventListener('click', function(e) {
        e.preventDefault();
        const ref = document.referrer;
        const fallback = @json($backUrl);
        let target = ref && !ref.includes('/surat/create-caraka') ? ref : fallback;
        target += (target.includes('?') ? '&' : '?') + 'refresh=1';
        window.location.replace(target);
    });
</script>
@endpush