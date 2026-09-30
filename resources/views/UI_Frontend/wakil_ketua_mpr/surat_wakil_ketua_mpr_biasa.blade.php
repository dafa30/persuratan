@extends('UI_Frontend.index')
@section('content')

<div class="container">
    @php
        $bulanIndo = [
            1=>'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        $backHashNow = base64_encode(url()->full());

        // detect role
        $u = auth()->user();
        $roleName = strtolower(trim(
            (string)(
                optional($u->role)->nama_role
                ?? optional($u->role)->name
                ?? (is_string(optional($u)->getAttribute('role')) ? $u->getAttribute('role') : '')
            )
        ));
        $isCaraka = ($roleName === 'caraka');

        // Ambil dari query ?wakil=... kalau ada, kalau tidak pakai segmen URL terakhir
        $slug = trim((string) request('wakil', collect(request()->segments())->last()));

        // Hindari kata umum agar tidak salah deteksi
        $stop = ['biasa','surat','wakil','ketua','mpr','rahasia','sangat-rahasia','ketuampr','wakil-ketuampr'];
        if (in_array(strtolower($slug), $stop, true)) {
            $slug = '';
        }

        /**
         * PATCH: Cocokkan slug URL ke nama resmi dari config('pimpinan.daftar')
         * - Hilangkan gelar depan (Ir., Drs., Dr., H.) berulang
         * - Hilangkan inisial satu huruf di depan (mis. "M. ")
         * - Hilangkan tanda titik & koma (M.B.A. -> MBA, S.H. -> SH, B.Com. -> BCom)
         * - Slugify lalu bandingkan dengan $slug dari URL
         */
        $wakilNama = 'Wakil Ketua MPR';
        if ($slug !== '') {
            $whitelist = collect(config('pimpinan.daftar', []));

            $makeWhitelistSlug = function (string $name): string {
                $n = trim($name);

                // Buang gelar depan berulang (Ir., Drs., Dr., H.)
                do {
                    $before = $n;
                    $n = preg_replace('/^(?:Ir|Drs|Dr|H)\.\s*/i', '', $n);
                } while ($n !== $before);

                // Buang inisial satu huruf di awal, mis. "M. "
                $n = preg_replace('/^[A-Z]\.\s*/', '', $n);

                // Hilangkan titik & koma, rapikan spasi
                $n = str_replace([',', '.'], [' ', ''], $n);
                $n = preg_replace('/\s+/', ' ', $n);

                // Slugify (tanpa perlu use; pakai FQCN)
                return \Illuminate\Support\Str::slug($n);
            };

            // Map-kan: slug_whitelist => nama_resmi
            $map = $whitelist->mapWithKeys(fn ($nama) => [$makeWhitelistSlug($nama) => $nama]);

            // Ambil nama resmi bila cocok; jika tidak, fallback ke Title Case dari slug
            $wakilNama = $map->get($slug) ?? ucwords(str_replace(['-','_'], ' ', $slug));
        }
    @endphp

    <h2>Daftar Surat Wakil Ketua MPR Biasa — {{ $wakilNama }}</h2>

    {{-- <form action="{{ request()->url() }}" method="GET" class="mb-4 row g-2">
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
                        {{ $bulanIndo[$m] }}
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
                // tanggal indo
                $dt = $surat->created_at ?? null;
                $tanggalIndo = $dt
                    ? ((int)$dt->format('j')).' '.$bulanIndo[(int)$dt->format('n')].' '.$dt->format('Y')
                    : '-';

                // badge jenis
                $jenis = strtolower((string)($surat->jenis_surat ?? ''));
                $isMasuk   = ($jenis === 'masuk');
                $jenisPill = $isMasuk
                    ? 'badge rounded-pill text-bg-success px-3 py-2'
                    : 'badge rounded-pill text-bg-danger px-3 py-2';

                // badge status
                $status = strtolower((string)($surat->status ?? ''));
                $statusClass = match ($status) {
                    'dikirim'  => 'badge bg-warning text-dark',
                    'dibaca'   => 'badge bg-primary',
                    'diterima' => 'badge bg-success',
                    'ditolak'  => 'badge bg-danger',
                    default    => 'badge bg-secondary'
                };

                // edit url (kalau bukan caraka pakai route normal), sertakan back
                $isSuratCaraka = (strtolower((string)($surat->pengirim_nama ?? '')) === 'caraka')
                                 || !empty($surat->penerima_eksternal ?? null);
                $editUrl = $isSuratCaraka
                    ? route('surat.edit.caraka', ['surat' => $surat->id_surats, 'back' => $backHashNow])
                    : route('surat.edit',       ['id_surats' => $surat->id_surats, 'back' => $backHashNow]);
            @endphp

            <tr class="text-center">
                <td>{{ $i + 1 }}</td>
                <td class="text-start">{{ $surat->judul_surat ?? '-' }}</td>
                <td>{{ $surat->nomor_surat ?? '-' }}</td>
                <td>
                    <span class="{{ $jenisPill }}">{{ $surat->jenis_surat ? ucfirst($surat->jenis_surat) : '-' }}</span>
                </td>
                <td>{{ $tanggalIndo }}</td>
                <td class="text-start">{{ $surat->pengirim_display ?? ($surat->pengirim_nama ?? '-') }}</td>
                <td class="text-start">{{ $surat->perihal ?? '-' }}</td>
                <td>
                    <span class="{{ $statusClass }}">{{ $surat->status ? ucfirst($surat->status) : 'Tidak diketahui' }}</span>
                </td>

                {{-- Aksi --}}
                <td class="text-nowrap">
                    <div class="action-group">
                        @if(!empty($surat->file_surat))
                            {{-- Unduh --}}
                            <a href="{{ asset($surat->file_surat) }}" class="action-btn btn-download" target="_blank" download aria-label="Unduh surat">
                                <span class="btn-layer icon"><i class="bi bi-download"></i></span>
                                <span class="btn-layer label">Unduh</span>
                            </a>

                            {{-- Lihat --}}
                            <a href="{{ route('lihat.surat.wakil.ketuampr', [
                                    'kategori' => $surat->kategori ?? 'biasa',
                                    'id_surats' => $surat->id_surats,
                                    'back'     => $backHashNow,
                                ]) }}" class="action-btn btn-view" aria-label="Lihat surat">
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
                        <form action="{{ route('surat.destroy', $surat->id_surats) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="d-inline">
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