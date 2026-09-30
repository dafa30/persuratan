@extends('UI_Frontend.index')
@section('content')

<div class="container">
    <h2>Daftar Surat Ketua MPR Biasa</h2>

   @php
        $bulanIndo = [
            1=>'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];
        $backHashNow = base64_encode(url()->full());

        $u = auth()->user();
        $roleName = strtolower(trim(
            (string)(
                optional($u->role)->nama_role
                ?? optional($u->role)->name
                ?? (is_string(optional($u)->getAttribute('role')) ? $u->getAttribute('role') : '')
            )
        ));
        $isCaraka = ($roleName === 'caraka');
    @endphp

   {{-- <form action="{{ route('surat.filter') }}" method="GET" class="mb-4 row g-2">
        <div class="col-auto">
            <select name="year" class="form-select">
                <option value="">Pilih Tahun</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <select name="month" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ (int)request('month') === $m ? 'selected' : '' }}>{{ $bulanIndo[$m] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <select name="day" class="form-select">
                <option value="">Pilih Tanggal</option>
                @foreach(range(1,31) as $d)
                    <option value="{{ $d }}" {{ (int)request('day') === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div> --}}

        <div class="col-auto d-flex gap-2">
            @auth
                <a href="{{ $isCaraka ? route('surat.create.caraka') : route('surat.create') }}" class="btn btn-primary">
                    {{ $isCaraka ? 'Tambah Surat (Caraka)' : 'Tambah Surat' }}
                </a>
            @endauth
            <!-- <button type="submit" class="btn btn-secondary">Filter</button> -->
        </div>
    </form>

    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width:56px">No</th>
                <th>Judul Surat</th>
                <th>Nomor Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Perihal</th>
                <th>Status</th>
                <th style="width:240px">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data_surat as $i => $surat)
            @php
                $dt = $surat->created_at;
                $tanggalIndo = $dt ? ((int)$dt->format('j')).' '.$bulanIndo[(int)$dt->format('n')].' '.$dt->format('Y') : '-';

                $isMasuk = strtolower($surat->jenis_surat ?? '') === 'masuk';
                $jenisPill = $isMasuk
                    ? 'badge rounded-pill text-bg-success px-3 py-2'
                    : 'badge rounded-pill text-bg-danger px-3 py-2';

                $status = strtolower($surat->status ?? '');
                $statusClass = match ($status) {
                    'dikirim' => 'badge bg-warning text-dark',
                    'dibaca' => 'badge bg-primary',
                    'diterima' => 'badge bg-success',
                    'ditolak' => 'badge bg-danger',
                    default => 'badge bg-secondary'
                };

                $isSuratCaraka = (strtolower($surat->pengirim_nama ?? '') === 'caraka') || !empty($surat->penerima_eksternal);
                $editUrl = $isSuratCaraka
                    ? route('surat.edit.caraka', ['surat' => $surat->id_surats, 'back' => $backHashNow])
                    : route('surat.edit', ['id_surats' => $surat->id_surats, 'back' => $backHashNow]);
            @endphp

            <tr class="text-center">
                <td>{{ $i+1 }}</td>
                <td class="text-start">{{ $surat->judul_surat }}</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td><span class="{{ $jenisPill }}">{{ ucfirst($surat->jenis_surat) }}</span></td>
                <td>{{ $tanggalIndo }}</td>
                <td class="text-start">{{ $surat->pengirim_display }}</td>
                <td class="text-start">{{ $surat->perihal }}</td>
                <td><span class="{{ $statusClass }}">{{ ucfirst($surat->status) }}</span></td>

                {{-- Aksi --}}
                <td class="text-nowrap">
                    <div class="action-group">
                        @if($surat->file_surat)
                            {{-- Unduh --}}
                            <a href="{{ asset($surat->file_surat) }}" class="action-btn btn-download" target="_blank" download aria-label="Unduh surat">
                                <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                <span class="btn-layer label">Unduh</span>
                            </a>

                            {{-- Lihat --}}
                            <a href="{{ route('lihat.surat.ketuampr', ['kategori'=>$surat->kategori, 'id_surats'=>$surat->id_surats]) }}" class="action-btn btn-view" aria-label="Lihat surat">
                                <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                <span class="btn-layer label">Lihat</span>
                            </a>
                        @else
                            <span class="text-muted me-2">Tidak ada file</span>
                        @endif

                        {{-- Edit --}}
                        @unless($isCaraka)
                            <a href="{{ $editUrl }}" class="action-btn btn-edit" aria-label="Edit surat">
                                <span class="btn-layer icon"><i class="bi bi-pencil-square"></i></span>
                                <span class="btn-layer label">Edit</span>
                            </a>
                        @endunless

                        {{-- Hapus --}}
                        <form action="{{ route('surat.destroy', $surat->id_surats) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="back" value="{{ $backHashNow }}">
                            <button type="submit" class="action-btn btn-delete" aria-label="Hapus surat">
                                <span class="btn-layer icon"><i class="bi bi-trash"></i></span>
                                <span class="btn-layer label">Hapus</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data surat.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection