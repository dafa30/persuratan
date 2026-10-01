@extends('UI_Frontend.index')
@section('content')

<div class="container">
    <h2>Daftar Surat Kearsipan Sangat Rahasia</h2>

    {{-- Form Filter --}}
    <form action="{{ route('surat.bagianKearsipan_sangat_rahasia') }}" method="GET" class="mb-4 row g-2">
        <div class="col-auto">
            <select name="year" class="form-select">
                <option value="">Pilih Tahun</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="month" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ (int)request('month') === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="day" class="form-select">
                <option value="">Pilih Tanggal</option>
                @foreach(range(1,31) as $d)
                    <option value="{{ $d }}" {{ (int)request('day') === $d ? 'selected' : '' }}>
                        {{ $d }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>

    {{-- Tabel Surat --}}
    <div class="table-responsive surat-table-scroll">
    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width:56px">No</th>
                <th>Judul Surat</th>
                <th>Nomor Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Perihal</th>
                <th>Status</th>
                <th style="width:280px">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($data_surat as $i => $surat)
            @php
                $jenis = strtolower((string)($surat->jenis_surat ?? ''));
                $jenisBadgeClass = $jenis === 'masuk'
                    ? 'badge rounded-pill text-bg-success px-3 py-2'
                    : 'badge rounded-pill text-bg-danger px-3 py-2';

                $status = strtolower((string)($surat->status ?? ''));
                $statusClass = match ($status) {
                    'dikirim'  => 'badge bg-warning text-dark',
                    'dibaca'   => 'badge bg-primary',
                    'diterima' => 'badge bg-success',
                    'ditolak'  => 'badge bg-danger',
                    default    => 'badge bg-secondary'
                };
            @endphp

            <tr class="text-center">
                <td>{{ $i + 1 }}</td>
                <td class="text-start">{{ $surat->judul_surat ?? '-' }}</td>
                <td>{{ $surat->nomor_surat ?? '-' }}</td>
                <td><span class="{{ $jenisBadgeClass }}">{{ ucfirst($surat->jenis_surat ?? '-') }}</span></td>
                <td>{{ optional($surat->created_at)->isoFormat('D MMMM Y') }}</td>
                <td class="text-start">
                    {{ $surat->user->name ?? ($surat->pengirim_nama ?? '-') }}
                </td>
                <td class="text-start">
                    {{ $surat->penerima->name ?? ($surat->penerima_eksternal ?? '-') }}
                </td>
                <td class="text-start">{{ $surat->perihal ?? '-' }}</td>
                <td><span class="{{ $statusClass }}">{{ ucfirst($surat->status ?? 'Tidak diketahui') }}</span></td>

                <td class="text-nowrap">
                    <div class="action-group">
                        @if(!empty($surat->file_surat))
                            {{-- Unduh --}}
                            <a href="{{ asset($surat->file_surat) }}"
                               class="action-btn btn-download"
                               target="_blank" download aria-label="Unduh surat">
                                <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                <span class="btn-layer label">Unduh</span>
                            </a>

                            {{-- Lihat --}}
                            <a href="{{ route('lihat.surat.bagian.kearsipan', ['kategori' => $surat->kategori, 'id_surats' => $surat->id_surats]) }}"
                               class="action-btn btn-view" aria-label="Lihat surat">
                                <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                <span class="btn-layer label">Lihat</span>
                            </a>
                        @else
                            <span class="text-muted me-2">Tidak ada file</span>
                        @endif

                        {{-- Edit --}}
                        <a href="{{ route('surat.edit', ['id_surats' => $surat->id_surats]) }}"
                           class="action-btn btn-edit" aria-label="Edit surat">
                            <span class="btn-layer icon"><i class="bi bi-pencil-square"></i></span>
                            <span class="btn-layer label">Edit</span>
                        </a>

                        {{-- Hapus --}}
                        @auth
                        <form action="{{ route('surat.destroy', $surat->id_surats) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn btn-delete" aria-label="Hapus surat">
                                <span class="btn-layer icon"><i class="bi bi-trash"></i></span>
                                <span class="btn-layer label">Hapus</span>
                            </button>
                        </form>
                        @endauth
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada data surat.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection