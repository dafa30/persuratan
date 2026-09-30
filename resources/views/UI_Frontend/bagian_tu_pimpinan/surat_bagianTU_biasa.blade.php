@extends('UI_Frontend.index')
@section('content')

<div class="container">
    <h2>Daftar Surat Bagian TU Pimpinan Setjen</h2>

    {{-- Form filter --}}
    {{-- <form action="{{ url()->current() }}" method="GET" class="mb-4 row g-2">
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
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ (int)request('month') === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <select name="day" class="form-select">
                <option value="">Pilih Tanggal</option>
                @foreach(range(1, 31) as $d)
                    <option value="{{ $d }}" {{ (int)request('day') === $d ? 'selected' : '' }}>
                        {{ $d }}
                    </option>
                @endforeach
            </select>
        </div> --}}

        <div class="col-auto d-flex gap-2">
            @auth
                @php
                    $roleName = strtolower(optional(auth()->user()->role)->nama_role ?? '');
                    $isCarakaUser = ($roleName === 'caraka');
                @endphp
                <a href="{{ $isCarakaUser ? route('surat.create.caraka') : route('surat.create') }}" class="btn btn-primary">
                    {{ $isCarakaUser ? 'Tambah Surat (Caraka)' : 'Tambah Surat' }}
                </a>
            @endauth
            <!-- <button type="submit" class="btn btn-secondary">Filter</button> -->
        </div>
    </form>

    @php
        // URL halaman ini + query sebagai 'back'
        $currentFullUrl = url()->full();
        $backHashNow = base64_encode($currentFullUrl);
    @endphp

    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width:56px">No</th>
                <th>Judul Surat</th>
                <th>Nomor Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Penerima</th> {{-- ✅ kolom baru --}}
                <th>Perihal</th>
                <th>Status</th>
                <th style="width:240px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_surat as $surat)
                @php
                    $jenisUntukTU    = $surat->jenis_untuk_tu ?? $surat->jenis_surat;
                    $jenisBadgeClass = strtolower($jenisUntukTU) === 'masuk' ? 'bg-success' : 'bg-info';

                    $status = strtolower($surat->status ?? '');
                    $statusClass = match ($status) {
                        'dikirim'  => 'bg-warning text-dark',
                        'dibaca'   => 'bg-primary',
                        'diterima' => 'bg-success',
                        'ditolak'  => 'bg-danger',
                        default    => 'bg-secondary'
                    };

                    // Surat dari Caraka?
                    $isSuratCaraka = (strtolower($surat->pengirim_nama ?? '') === 'caraka') || !empty($surat->penerima_eksternal);

                    // URL edit/unggah + param 'back' (base64)
                    $editUrl = $isSuratCaraka
                        ? route('surat.edit.caraka', ['surat' => $surat->id_surats, 'back' => $backHashNow])
                        : route('surat.edit',        ['id_surats' => $surat->id_surats, 'back' => $backHashNow]);

                    // ===== DETEKSI BUKTI =====
                    $buktiCandidates = [
                        $surat->file_bukti_terima ?? null,
                        $surat->bukti             ?? null,
                        $surat->bukti_file        ?? null,
                        $surat->bukti_gambar      ?? null,
                        $surat->bukti_foto        ?? null,
                        $surat->bukti_path        ?? null,
                        $surat->bukti_url         ?? null,
                        $surat->lampiran_bukti    ?? null,
                    ];
                    $buktiPath = collect($buktiCandidates)->first(fn($v) => filled($v));

                    $buktiUrl = null;
                    if ($buktiPath) {
                        $buktiUrl = \Illuminate\Support\Str::startsWith($buktiPath, ['http://','https://'])
                            ? $buktiPath
                            : asset($buktiPath);
                    }

                    // ✅ tambahan: display penerima (ambil yang ada saja)
                    $penerimaDisplay = $surat->penerima_display
                        ?? $surat->penerima_nama
                        ?? $surat->penerima_eksternal
                        ?? $surat->penerima
                        ?? '-';
                @endphp

                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $surat->judul_surat }}</td>
                    <td>{{ $surat->nomor_surat }}</td>
                    <td><span class="badge {{ $jenisBadgeClass }}">{{ ucfirst($jenisUntukTU) }}</span></td>
                    <td>{{ optional($surat->created_at)->isoFormat('D MMMM Y') }}</td>
                    <td class="text-start">{{ $surat->pengirim_display }}</td>
                    <td class="text-start">{{ $penerimaDisplay }}</td> {{-- ✅ kolom penerima --}}
                    <td class="text-start">{{ $surat->perihal }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ ucfirst($surat->status) }}</span></td>

                    {{-- Aksi --}}
                    <td class="text-nowrap">
                        <div class="action-group">
                            @if($surat->file_surat)
                                {{-- Unduh --}}
                                <a href="{{ asset($surat->file_surat) }}"
                                   class="action-btn btn-download"
                                   target="_blank" download aria-label="Unduh surat">
                                    <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                    <span class="btn-layer label">Unduh</span>
                                </a>

                                {{-- Lihat --}}
                                <a href="{{ route('lihat.surat.bagian_tu', ['kategori' => $surat->kategori, 'id_surats' => $surat->id_surats]) }}"
                                   class="action-btn btn-view" aria-label="Lihat surat">
                                    <span class="btn-layer icon"><i class="bi bi-eye"></i></span>
                                    <span class="btn-layer label">Lihat</span>
                                </a>
                            @else
                                <span class="text-muted me-2">Tidak ada file</span>
                            @endif

                            {{-- Edit / Unggah --}}
                            @auth
                                @if($isSuratCaraka && $roleName === 'caraka')
                                    <a href="{{ $editUrl }}" class="action-btn btn-upload" aria-label="Unggah">
                                        <span class="btn-layer icon"><i class="bi bi-upload"></i></span>
                                        <span class="btn-layer label">Unggah</span>
                                    </a>
                                @else
                                    <a href="{{ $editUrl }}" class="action-btn btn-edit" aria-label="Edit surat">
                                        <span class="btn-layer icon"><i class="bi bi-pencil-square"></i></span>
                                        <span class="btn-layer label">Edit</span>
                                    </a>
                                @endif
                            @endauth

                            {{-- Lihat Bukti --}}
                            @if($isSuratCaraka && $buktiUrl)
                                <a href="{{ $buktiUrl }}"
                                   class="action-btn btn-proof"
                                   target="_blank"
                                   aria-label="Lihat bukti"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat bukti">
                                    <span class="btn-layer icon"><i class="bi bi-card-image"></i></span>
                                    <span class="btn-layer label">Lihat Bukti</span>
                                </a>
                            @endif

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

@endsection
