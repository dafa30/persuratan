@php
    // Slug dari ?wakil=... atau segmen URL terakhir; abaikan kata umum agar tidak salah deteksi
    $slug = trim((string) request('wakil', collect(request()->segments())->last()));
    $stop = ['biasa','surat','wakil','ketua','mpr','rahasia','sangat-rahasia','ketuampr','wakil-ketuampr'];
    if (in_array(strtolower($slug), $stop, true)) {
        $slug = '';
    }

    // Cocokkan slug URL ke nama resmi di config('pimpinan.daftar'):
    // buang gelar depan (Ir., Drs., Dr., H.), inisial satu huruf, titik & koma, lalu slugify
    $wakilNama = 'Wakil Ketua MPR';
    if ($slug !== '') {
        $makeWhitelistSlug = function (string $name): string {
            $n = trim($name);
            do {
                $before = $n;
                $n = preg_replace('/^(?:Ir|Drs|Dr|H)\.\s*/i', '', $n);
            } while ($n !== $before);
            $n = preg_replace('/^[A-Z]\.\s*/', '', $n);
            $n = str_replace([',', '.'], [' ', ''], $n);
            $n = preg_replace('/\s+/', ' ', $n);
            return \Illuminate\Support\Str::slug($n);
        };

        $map = collect(config('pimpinan.daftar', []))
            ->mapWithKeys(fn ($nama) => [$makeWhitelistSlug($nama) => $nama]);

        $wakilNama = $map->get($slug) ?? ucwords(str_replace(['-', '_'], ' ', $slug));
    }
@endphp
<h2>Daftar Surat Wakil Ketua MPR {{ $kategori }} — {{ $wakilNama }}</h2>
