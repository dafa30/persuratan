@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Daftar Pengguna</h2>

    <div class="table-responsive shadow-sm rounded mt-3">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-secondary text-center">
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th style="width:260px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>

                    <td>
                        <span class="badge bg-info text-dark">
                            {{ $user->role?->nama_role ?? 'Role belum diatur' }}
                        </span>
                    </td>

                    <td class="text-center d-flex justify-content-center gap-2">

                        {{-- 🔍 Lihat User --}}
                        <button class="action-hover-btn btn-view" data-bs-toggle="modal"
                            data-bs-target="#detailUserModal{{ $user->id_users }}" aria-label="Lihat User">
                            <i class="bi bi-eye"></i>
                            <span class="btn-text">Lihat</span>
                        </button>

                        {{-- ✏️ Ubah Role --}}
                        <button class="action-hover-btn btn-edit" data-bs-toggle="modal"
                            data-bs-target="#roleModal{{ $user->id_users }}" aria-label="Ubah Role">
                            <i class="bi bi-pencil-square"></i>
                            <span class="btn-text">Edit</span>
                        </button>

                        {{-- 🗑️ Hapus User --}}
                        <button class="action-hover-btn btn-delete" data-bs-toggle="modal"
                            data-bs-target="#hapusUserModal{{ $user->id_users }}" aria-label="Hapus User">
                            <i class="bi bi-trash"></i>
                            <span class="btn-text">Hapus</span>
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Belum ada pengguna terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 🔧 Modal UPDATE ROLE --}}
    @foreach($users as $user)
    <div class="modal fade" id="roleModal{{ $user->id_users }}" tabindex="-1"
        aria-labelledby="roleModalLabel{{ $user->id_users }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel{{ $user->id_users }}">
                        Ubah Role untuk <b>{{ $user->name }}</b>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('setting.role.update', $user->id_users) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 text-start">
                            <label for="role_id_{{ $user->id_users }}" class="form-label">Pilih Role Baru</label>
                            <select name="role_id" id="role_id_{{ $user->id_users }}" class="form-select" required>
                                <option value="" disabled>-- Pilih Role --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id_roles }}"
                                    {{ (int)$user->role_id === (int)$role->id_roles ? 'selected' : '' }}>
                                    {{ $role->nama_role }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- 🔎 Modal DETAIL USER --}}
    @foreach($users as $user)
    <div class="modal fade" id="detailUserModal{{ $user->id_users }}" tabindex="-1"
        aria-labelledby="detailUserLabel{{ $user->id_users }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailUserLabel{{ $user->id_users }}">Detail Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Nama</small>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Email</small>
                        <div class="fw-semibold">{{ $user->email }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Jabatan/Bagian</small>
                        <div class="fw-semibold">
                            {{ $user->role?->nama_role ?? 'Belum diatur' }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- 🗑️ Modal KONFIRMASI HAPUS --}}
    @foreach($users as $user)
    <div class="modal fade" id="hapusUserModal{{ $user->id_users }}" tabindex="-1"
        aria-labelledby="hapusUserLabel{{ $user->id_users }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="hapusUserLabel{{ $user->id_users }}">
                        Konfirmasi Hapus Pengguna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pengguna <b>{{ $user->name }}</b>? Tindakan dapat dibatalkan.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('setting.role.destroy', $user->id_users) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('styles')
<style>
/* Grup tombol */
td.d-flex { gap:.45rem; }

/* Tombol dasar */
.action-hover-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 58px;             /* kecil seperti contoh */
  height: 44px;
  border: none;
  border-radius: .8rem;
  cursor: pointer;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,.05);
  transition: transform .25s ease, filter .25s ease;
}

/* Ikon */
.action-hover-btn i {
  position: absolute;
  font-size: 1.1rem;
  color: inherit;
  opacity: 1;
  transform: translateY(0);
  transition: transform .35s ease, opacity .35s ease;
}

/* Label teks */
.action-hover-btn .btn-text {
  position: absolute;
  top: 52%;
  left: 50%;
  transform: translate(-50%, 12px);
  font-size: .75rem;
  font-weight: 500;
  color: inherit;
  opacity: 0;
  transition: transform .35s ease, opacity .35s ease;
  white-space: nowrap;
  pointer-events: none;
}

/* Hover: animasi ikon ke atas, teks fade in dari bawah */
.action-hover-btn:hover i {
  transform: translateY(-16px);
  opacity: 0;
}
.action-hover-btn:hover .btn-text {
  opacity: 1;
  transform: translate(-50%, -50%);
}

/* Ripple klik */
.action-hover-btn::after {
  content: "";
  position: absolute;
  border-radius: 50%;
  width: 10px;
  height: 10px;
  opacity: 0;
  background: rgba(255,255,255,.4);
  transform: scale(1);
  transition: transform .4s ease, opacity .6s ease;
}
.action-hover-btn:active::after {
  transform: scale(8);
  opacity: 1;
  transition: 0s;
}

/* Saat satu tombol dihover, yang lain redup */
td.d-flex:hover .action-hover-btn:not(:hover) {
  opacity: .5;
  transform: scale(.97);
  filter: saturate(.85);
}

/* Warna */
.btn-download { background:#e9f2ff; color:#1d4ed8; }
.btn-view     { background:#e7f6ef; color:#047857; }
.btn-edit     { background:#fff6e5; color:#b45309; }
.btn-delete   { background:#fdecef; color:#b91c1c; }

/* Hover warna lembut */
.btn-download:hover { background:#dbeafe; }
.btn-view:hover     { background:#d1fae5; }
.btn-edit:hover     { background:#ffedd5; }
.btn-delete:hover   { background:#fee2e2; }

/* Fokus ring */
.action-hover-btn:focus-visible {
  outline: 2px solid rgba(59,130,246,.45);
  outline-offset: 2px;
}
</style>
@endpush



