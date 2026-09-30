<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\Surat;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;     
use Illuminate\Support\Facades\Log;  
use App\Mail\SuratCreated;
use Illuminate\Support\Str;


class SuratController extends Controller
{

    // Tambah/helper di dalam SuratController
    private function hasRole(array $roles): bool
    {
        return in_array($this->currentRoleName(), array_map('strtolower', $roles), true);
    }

    private function isSekretariatOrUser(): bool
    {
        return $this->hasRole(['sekretariat', 'user']);
    }

    /**
         * Ambil nama role user login (lowercase) dengan berbagai fallback.
         */
        private function currentRoleName(): string
    {
        $user = auth()->user();
        if (!$user) return '';

        // 1) Langsung dari kolom string "role" di users (kalau ada)
        $raw = $user->getAttribute('role');
        if (is_string($raw) && trim($raw) !== '') {
            $val = strtolower(trim($raw));
            Log::debug('[ROLE] users.role = '.$val);
            return $val;
        }

        // 2) Dari relasi $user->role (kalau ada)
        if ($user->relationLoaded('role') || method_exists($user, 'role')) {
            $rel = $user->role ?? null;
            if ($rel) {
                // dukung beberapa kemungkinan nama kolom
                foreach (['nama_role','name','role','role_name','nama'] as $col) {
                    if (isset($rel->{$col}) && is_string($rel->{$col}) && trim($rel->{$col}) !== '') {
                        $val = strtolower(trim($rel->{$col}));
                        Log::debug('[ROLE] relasi role.'.$col.' = '.$val);
                        return $val;
                    }
                }
            }
        }

        // 3) Dari role_id → tabel roles.nama_role
        if (!empty($user->role_id)) {
            // prioritas: nama_role (sesuai skema kamu)
            $val = DB::table('roles')->where('id_roles', $user->role_id)->value('nama_role');
            if (!$val) {
                $val = DB::table('roles')->where('id_roles', $user->role_id)
                    ->value('name')
                    ?? DB::table('roles')->where('id_roles', $user->role_id)->value('role')
                    ?? DB::table('roles')->where('id_roles', $user->role_id)->value('role_name')
                    ?? DB::table('roles')->where('id_roles', $user->role_id)->value('nama');
            }

            if (is_string($val) && trim($val) !== '') {
                $val = strtolower(trim($val));
                Log::debug('[ROLE] roles(...)= '.$val);
                return $val;
            }
        }

        Log::debug('[ROLE] tidak terdeteksi');
        return '';
    }

    /** True bila role user adalah 'caraka'. */
    private function isCaraka(): bool
    {
        $role = $this->currentRoleName();
        $is = ($role === 'caraka');
        Log::debug('[ROLE] isCaraka? '.($is ? 'YA' : 'TIDAK').' (nilai="'.$role.'")');
        return $is;
    }

    /** True bila role user adalah 'admin'. */
    private function isAdmin(): bool
    {
        $role = $this->currentRoleName();
        $is = in_array($role, ['admin','administrator','superadmin','super admin'], true);
        \Log::debug('[ROLE] isAdmin? '.($is ? 'YA' : 'TIDAK').' (nilai="'.$role.'")');
        return $is;
    }

    /** True bila role adalah 'user' atau 'caraka'. */
    private function isUserOrCaraka(): bool
    {
        return in_array($this->currentRoleName(), ['user', 'caraka'], true);
    }

    private function ketuaUserId(): ?int
    {
        // Sesuaikan: ambil dari config atau hardcode nama ketua
        $namaKetua = config('pimpinan.ketua', 'Ahmad Muzani');
        $ketua = \App\Models\User::where('name', $namaKetua)->first();
        return $ketua?->id_users;
    }

    private function ketuaFilter(\Illuminate\Database\Eloquent\Builder $q): \Illuminate\Database\Eloquent\Builder
    {
        $ketuaName = \Illuminate\Support\Str::lower(trim(config('pimpinan.ketua', 'Ahmad Muzani')));

        $ketuaId = \App\Models\User::whereRaw('LOWER(name) = ?', [$ketuaName])->value('id_users')
            ?? \App\Models\User::whereRaw('LOWER(name) LIKE ?', ['%'.$ketuaName.'%'])->orderBy('id_users')->value('id_users');

        return $q->where(function ($qq) use ($ketuaId, $ketuaName) {
            if ($ketuaId) {
                $qq->orWhere('penerima_id', $ketuaId);
            }
            $qq->orWhereHas('penerima', function ($q2) use ($ketuaName) {
                $q2->whereRaw('LOWER(name) LIKE ?', ['%'.$ketuaName.'%']);
            });
            $qq->orWhereRaw('LOWER(penerima_eksternal) LIKE ?', ['%'.$ketuaName.'%']);
        });
    }


    /** Nama/keyword yang merepresentasikan Bagian TU di penerima_eksternal (Caraka) */
    private function bagianTUKeywords(): array
    {
        return ['bagian tu', 'tu pimpinan', 'tata usaha'];
    }

    /** Kumpulan user ID untuk akun internal Bagian TU (jika ada). Ambil dari config atau fallback nama umum. */
    private function bagianTUUserIds(): array
    {
        $names = config('pimpinan.bagian_tu', []); // contoh: ['Bagian TU Pimpinan']
        if (empty($names)) {
            $names = ['Bagian TU', 'Bagian TU Pimpinan', 'Tata Usaha'];
        }
        return User::whereIn('name', $names)->pluck('id_users')->all();
    }

    /** Query builder dasar untuk filter surat yang memang milik Bagian TU. */
    private function scopeSuratBagianTU($q)
    {
        $tuIds  = $this->bagianTUUserIds();
        $words  = $this->bagianTUKeywords();

        return $q->where(function ($q) use ($tuIds, $words) {
            if (!empty($tuIds)) {
                $q->orWhereIn('penerima_id', $tuIds);
            }
            $q->orWhere(function ($qq) use ($words) {
                $qq->whereNotNull('penerima_eksternal');
                foreach ($words as $w) {
                    $qq->orWhere('penerima_eksternal', 'like', "%{$w}%");
                }
            });
        });
    }

    private function isBagianTURecipientId(?int $id): bool
    {
        $role = $this->currentRoleName();
        // dukung semua variasi penamaan yang mungkin muncul
        return in_array($role, ['bagian_tu','bagian tu','bagian tu pimpinan','tata usaha'], true);
    }

    /** True bila role user adalah 'bagian tu'. */
    private function isBagianTU(): bool
    {
        $role = $this->currentRoleName();
        return in_array($role, ['bagian tu','bagian tu pimpinan','tata usaha'], true);
    }

    /** Lempar 403 bila role saat ini Bagian TU (dipakai di halaman non-TU). */
    private function forbidBagianTUNonTU(): void
    {
        if ($this->isBagianTU()) {
            abort(403, 'Akses ditolak: halaman ini bukan untuk Bagian TU.');
        }
    }

    public function index()
    {
        // Hanya admin yang boleh akses halaman index surat (dashboard)
        abort_unless($this->isAdmin(), 403);

        $suratMasuk  = Surat::with(['user:id_users,name','penerima:id_users,name'])
                            ->where('jenis_surat','masuk')
                            ->latest()->get();

        $suratKeluar = Surat::with(['user:id_users,name','penerima:id_users,name'])
                            ->where('jenis_surat','keluar')
                            ->latest()->get();

        return view('surat.index', compact('suratMasuk', 'suratKeluar'));
    }

    // ===== Wakil: mapping & helper =====
    private function wakilMap(): array
    {
        return [
            'bambang-wuryanto-mba' => [
                'nama'    => 'Ir. Bambang Wuryanto, M.B.A.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/bambang_wuryanto.jpg',
            ],
            'kahar-muzakir' => [
                'nama'    => 'Drs. H. Kahar Muzakir',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/kahar_muzakir.jpg',
            ],
            'lestari-moerdijat-ss-mm' => [
                'nama'    => 'Dr. Lestari Moerdijat, S.S., M.M.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/lestari.jpg',
            ],
            'rusdi-kirana-se' => [
                'nama'    => 'Rusdi Kirana, S.E.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/rusdi.jpg',
            ],
            'hidayat-nur-wahid-ma' => [
                'nama'    => 'Dr. H. M. Hidayat Nur Wahid, MA.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/hidayat_nur.jpg',
            ],
            'eddy-dwiyanto-soeparno-sh-mh' => [
                'nama'    => 'M. Eddy Dwiyanto Soeparno, S.H., M.H.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/eddy_soeparno.jpg',
            ],
            'edhie-baskoro-yudhoyono-bcom' => [
                'nama'    => 'Dr. Edhie Baskoro Yudhoyono, B.Com.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/edhie_baskoro.jpg',
            ],
            'abcandra-ma-supratman-sh' => [
                'nama'    => 'Abcandra M.A Supratman, S.H.',
                'jabatan' => 'Bagian Set. Wakil Ketua MPR',
                'img'     => 'anggota_mpr/abcandra.jpg',
            ],
        ];
    }

    private function wakilFromSlug(string $slug): ?array
    {
        $map = $this->wakilMap();
        return $map[$slug] ?? null;
    }

    /** cari slug dari nama (case-insensitive, ignore dot/comma/space) */
    private function slugFromName(string $name): ?string
    {
        $norm = fn($s)=> strtolower(preg_replace('/[^\pL0-9]+/u','-',trim($s)));
        $needle = $norm($name);
        foreach ($this->wakilMap() as $slug => $w) {
            if ($norm($w['nama']) === $needle) return $slug;
            // contain check (kalau nama di DB sedikit beda tanda baca)
            if (str_contains($norm($w['nama']), $needle) || str_contains($needle, $norm($w['nama']))) {
                return $slug;
            }
        }
        return null;
    }

    public function showDetailsurat($kategori, $id_surats)
    {
        try {
            $surat = Surat::where('jenis_surat', $kategori)->findOrFail($id_surats);
            $pdfPath = null;

            if ($surat->file_surat) {
                $publicUrlPath = $surat->file_surat;
                $storageDiskRelativePath = null;

                if (strpos($publicUrlPath, 'storage/') === 0) {
                    $storageDiskRelativePath = substr($publicUrlPath, strlen('storage/'));
                } else {
                    \Log::warning("Format file_surat tidak terduga untuk surat ID {$surat->id_surats}: {$publicUrlPath}");
                }

                if ($storageDiskRelativePath && Storage::disk('public')->exists($storageDiskRelativePath)) {
                    $pdfPath = asset($publicUrlPath);
                } else {
                    \Log::error("File PDF tidak ditemukan di disk public untuk surat ID {$surat->id_surats}. Path yang dicari: 'public/{$storageDiskRelativePath}'. Nilai file_surat: '{$publicUrlPath}'");
                }
            }

            return view('surat.kategori', [
                'surat' => $surat,
                'pdfPath' => $pdfPath,
                'kategori' => $kategori,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('surat.index')
                ->with('error_message', 'Surat yang Anda cari tidak ditemukan.');
        }
    }

    public function showDetail($id)
    {
        $surat = Surat::findOrFail($id);
        return view('surat.detail', compact('surat'));
    }

    public function create(Request $request)
    {
        if ($this->isBagianTU()) {
            return redirect()->route('surat.bagianTU_biasa')
                ->with('error', 'Akses pembuatan surat dibatasi untuk Bagian TU.');
        }
        
        // Tentukan halaman asal untuk tombol Batal
        $ref      = $request->headers->get('referer');
        $fallback = route('surat.ketuamprbiasa'); // SILAKAN ganti fallback sesuai halaman daftar yang kamu mau
        $backUrl  = ($ref && !str_contains($ref, '/surat/create')) ? $ref : $fallback;

        if ($this->isCaraka()) {
            return redirect()->route('surat.create.caraka', ['back' => base64_encode($backUrl)]);
        }

        // Non-Caraka: whitelist pimpinan
        $pimpinanNames = config('pimpinan.daftar', []);
        $pimpinan = User::query()
            ->whereIn('name', $pimpinanNames)
            ->orderBy('name')
            ->get(['id_users','name']);

        // + Bagian TU
        $bagianTu = User::whereIn('id_users', $this->bagianTUUserIds())
            ->orderBy('name')
            ->get(['id_users','name']);

        return view('surat.create_select', [
            'pimpinan'  => $pimpinan,
            'bagianTu'  => $bagianTu,
            'backUrl'   => $backUrl,
        ]);
    }

    public function createCaraka(Request $request)
    {
        abort_unless($this->isCaraka(), 403);

        // Ambil backUrl dari query ?back=... kalau ada, kalau tidak pakai referer
        $back = $request->query('back');
        $ref  = $request->headers->get('referer');
        $fallback = route('surat.ketuamprbiasa'); // fallback jika nggak ada referer
        $backUrl  = $back ? base64_decode($back) : (($ref && !str_contains($ref, '/surat/create')) ? $ref : $fallback);

        return view('surat.create_caraka', compact('backUrl'));
    }

    public function store(Request $request)
    {
        // 1) Deteksi role Caraka
        $isCaraka = $this->isCaraka();

        // 2) Rules & messages umum
        $commonRules = [
            'judul_surat' => 'required|string|max:255',
            'nomor_surat' => 'required|string|max:255',
            'jenis_surat' => 'required|string',
            'kategori'    => 'required|string',
            'file_surat'  => 'required|mimes:pdf,doc,docx,xlsx,xls|max:2048',
            'perihal'     => 'required|string',
        ];

        $messages = [
            'required'                    => 'Kolom tidak boleh kosong.',
            'penerima_id.required'        => 'Anda harus memilih penerima surat.',
            'penerima_id.exists'          => 'Penerima yang Anda pilih tidak valid.',
            'penerima_id.in'              => 'Penerima harus pimpinan yang ditentukan.',
            'penerima_eksternal.required' => 'Nama penerima wajib diisi.',
            'file_surat.mimes'            => 'Hanya PDF, Word, atau Excel yang diperbolehkan.',
            'file_surat.max'              => 'Ukuran file maksimal 2 MB.',
        ];

        // 3) Tentukan rules spesifik per role
        if ($isCaraka) {
            // Caraka: penerima manual (tidak kirim email)
            $rules = $commonRules + [
                'penerima_eksternal' => 'required|string|max:255',
                // pengirim_nama akan diset "Caraka" di bawah
            ];
        } else {
            // Non-Caraka: penerima dari whitelist pimpinan + Bagian TU
            $pimpinanNames = config('pimpinan.daftar', []);
            $allowedIds = [];
            if (!empty($pimpinanNames)) {
                $allowedIds = User::whereIn('name', $pimpinanNames)->pluck('id_users')->all();
            }
            // Tambahkan Bagian TU user ids
            $allowedIds = array_values(array_unique(array_merge($allowedIds, $this->bagianTUUserIds())));

            if (empty($allowedIds)) {
                return back()->withInput()
                    ->with('error', 'Belum ada akun pimpinan terdaftar. Silakan buat akun Ketua/Wakil terlebih dahulu.');
            }

            $rules = $commonRules + [
                'penerima_id'   => ['required', Rule::in($allowedIds)],
                'pengirim_nama' => 'required|string|max:255',
            ];
        }

        $validated = $request->validate($rules, $messages);

        // 4) Upload file → simpan path publik (konsisten dg view)
        $filePath = null;
        $namaFileAsli = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $path = $file->store('surats', 'public'); // "surats/abc.pdf"
            $filePath = 'storage/' . $path;                                  // "storage/surats/abc.pdf"
            $namaFileAsli = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        }

        // 5) Siapkan data simpan
        if ($isCaraka) {
            $validated['pengirim_nama'] = 'Caraka';
        }

        // Deteksi apakah penerima internal adalah Bagian TU
        $isBagianTU = false;
        if (!$isCaraka) {
            $isBagianTU = in_array((int)($validated['penerima_id'] ?? 0), $this->bagianTUUserIds(), true);
        }
        // Jika Bagian TU → paksa jenis_surat = 'masuk' (server-side lock)
        if ($isBagianTU) {
            $validated['jenis_surat'] = 'masuk';
        }

        // 6) Simpan
        $surat = Surat::create([
            'judul_surat'        => $validated['judul_surat'],
            'nomor_surat'        => $validated['nomor_surat'],
            'jenis_surat'        => $validated['jenis_surat'],
            'kategori'           => $validated['kategori'],
            'file_surat'         => $filePath,
            'nama_file_asli'     => $namaFileAsli,
            'perihal'            => $validated['perihal'],
            'pengirim_id'        => auth()->user()?->id_users,
            'pengirim_nama'      => $validated['pengirim_nama'],
            'penerima_id'       => $validated['penerima_id']        ?? null,
            'penerima_eksternal' => $validated['penerima_eksternal'] ?? null,
            'status'             => 'dikirim',
        ]);

        // 7) Set pesan sukses lebih awal (agar tersedia di semua cabang)
        $successMessage = 'Surat berhasil ditambahkan.';

        // 8) Jika penerima Bagian TU → langsung kembali ke daftar Bagian TU (tanpa email)
        if ($isBagianTU && $surat->kategori === 'biasa') {
            return redirect()->route('surat.bagianTU_biasa')->with('success', $successMessage);
        }

        // 9) Kirim email (hanya non-Caraka & penerima internal normal)
        if (!$isCaraka && $surat->penerima) {
            try {
                Mail::to($surat->penerima->email)->send(new SuratCreated($surat));
                $successMessage = 'Surat berhasil ditambahkan dan notifikasi email telah terkirim.';
            } catch (\Throwable $e) {
                \Log::error("Email Gagal Dikirim: " . $e->getMessage());
                return back()->with('error', 'Surat disimpan, tetapi notifikasi email gagal dikirim.');
            }
        }

        // 10) Caraka → balik ke halaman asal (tempat klik Tambah Surat)
        if ($isCaraka) {
            $back = $request->input('back'); // base64
            if ($back && ($decoded = base64_decode($back, true))) {
                $target = $decoded;
                $appUrl = rtrim(config('app.url'), '/');
                if (Str::startsWith($target, ['/', $appUrl])) {
                    $sep = Str::contains($target, '?') ? '&' : '?';
                    return redirect()->to($target.$sep.'refresh=1')->with('success', $successMessage);
                }
            }
            return redirect()->route('surat.ketuamprbiasa')->with('success', $successMessage);
        }

        // 11) NON-CARAKA: routing berdasarkan penerima
        $nama = strtolower(optional($surat->penerima)->name ?? ($surat->penerima_eksternal ?? ''));

        // Ketua MPR
        if (strpos($nama, 'ahmad muzani') !== false) {
            return match ($surat->kategori) {
                'biasa'          => redirect()->route('surat.ketuamprbiasa')->with('success', $successMessage),
                'rahasia'        => redirect()->route('surat.ketuamprrahasia')->with('success', $successMessage),
                'sangat_rahasia' => redirect()->route('surat.ketuamprsangatrahasia')->with('success', $successMessage),
                default          => redirect()->route('surat.ketuamprbiasa')->with('success', $successMessage),
            };
        }

        // Wakil Ketua MPR → pakai slug
        $slug = $this->slugFromName($nama);
        if ($slug) {
            return match ($surat->kategori) {
                'biasa'          => redirect()->route('surat.wakilketuamprbiasa',          ['slug' => $slug])->with('success', $successMessage),
                'rahasia'        => redirect()->route('surat.wakilketuamprrahasia',        ['slug' => $slug])->with('success', $successMessage),
                'sangat_rahasia' => redirect()->route('surat.wakilketuamprsangatrahasia', ['slug' => $slug])->with('success', $successMessage),
                default          => redirect()->route('surat.ketuamprbiasa')->with('success', $successMessage),
            };
        }

        // 12) Fallback non-admin page
        return redirect()->route('surat.ketuamprbiasa')->with('success', $successMessage);
    }

    public function ketua_mpr()
    {
        return view('UI_Frontend.surat_ketua_mpr');
    }

    public function surat_ketua_mpr_biasa(Request $request)
    {
        $base = Surat::with(['user:id_users,name','penerima:id_users,name'])
            ->where('kategori', 'biasa');

        $base = $this->ketuaFilter($base);

        // dropdown tahun
        $years = (clone $base)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year','desc')->pluck('year');

        // filter tanggal
        if ($request->filled('year'))  $base->whereYear('created_at', $request->year);
        if ($request->filled('month')) $base->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $base->whereDay('created_at', $request->day);

        $data_surat = $base->orderBy('created_at','desc')->get();

        return view('UI_Frontend.ketua_mpr.surat_ketua_mpr_biasa', compact('data_surat','years'));
    }

    public function surat_ketua_mpr_rahasia(Request $request)
    {
        $base = Surat::with(['user:id_users,name','penerima:id_users,name'])
            ->where('kategori', 'rahasia');

        $base = $this->ketuaFilter($base);

        $years = (clone $base)->selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year','desc')->pluck('year');

        if ($request->filled('year'))  $base->whereYear('created_at', $request->year);
        if ($request->filled('month')) $base->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $base->whereDay('created_at', $request->day);

        $data_surat = $base->orderBy('created_at','desc')->get();

        return view('UI_Frontend.ketua_mpr.surat_ketua_mpr_rahasia', compact('data_surat','years'));
    }

    public function surat_ketua_mpr_sangat_rahasia(Request $request)
    {
        $base = Surat::with(['user:id_users,name','penerima:id_users,name'])
            ->where('kategori', 'sangat_rahasia');

        $base = $this->ketuaFilter($base);

        $years = (clone $base)->selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year','desc')->pluck('year');

        if ($request->filled('year'))  $base->whereYear('created_at', $request->year);
        if ($request->filled('month')) $base->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $base->whereDay('created_at', $request->day);

        $data_surat = $base->orderBy('created_at','desc')->get();

        return view('UI_Frontend.ketua_mpr.surat_ketua_mpr_sangat_rahasia', compact('data_surat','years'));
    }

    // Halaman profil wakil + card (single template)
    public function wakil(string $slug)
    {
        $w = $this->wakilFromSlug($slug);
        abort_unless($w, 404);
        // kirim obyek sederhana ke view
        $anggota = (object) array_merge($w, ['slug'=>$slug]);
        return view('UI_Frontend.surat_wakil_ketua_mpr', compact('anggota'));
    }

    public function surat_bagianTU()
    {
        return view('UI_Frontend.surat_bagianTU');
    }

    public function surat_bagianKearsipan()
    {
        return view('UI_Frontend.surat_bagianKearsipan');
    }
    
    public function surat_bagianTU_biasa(Request $request)
    {
        // base query: hanya surat untuk Bagian TU + kategori 'biasa' dan jenis 'masuk'
        $base = $this->scopeSuratBagianTU(
        Surat::query()->where('kategori', 'biasa')
            )->where(function ($q) {
        $q->where('jenis_surat', 'masuk')
          ->orWhere(function ($qq) {
              $qq->where('jenis_surat', 'keluar')
                 ->where('pengirim_nama', 'Caraka'); // identifikasi asal Caraka
          });
    });

        // dropdown tahun (pakai base yg sama)
        $years = (clone $base)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // filter tanggal
        $query = (clone $base);
        if ($request->filled('year'))  $query->whereYear('created_at', $request->year);
        if ($request->filled('month')) $query->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $query->whereDay('created_at', $request->day);

        $data_surat = $query
            ->with(['user:id_users,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('UI_Frontend.bagian_tu_pimpinan.surat_bagianTU_biasa', compact('data_surat', 'years'));
    }

    
    public function destroy(Request $request, $id_surats)
    {
        $surat = Surat::findOrFail($id_surats);

        // Hapus file surat jika ada
        if ($surat->file_surat) {
            // contoh: "storage/surats/abc.pdf" -> "surats/abc.pdf"
            $publicUrlPath = $surat->file_surat;
            $storageDiskRelativePath = Str::startsWith($publicUrlPath, 'storage/')
                ? substr($publicUrlPath, strlen('storage/'))
                : null;

            if ($storageDiskRelativePath && \Storage::disk('public')->exists($storageDiskRelativePath)) {
                \Storage::disk('public')->delete($storageDiskRelativePath);
            }
        }

        // Hapus bukti foto jika ada
        if ($surat->file_bukti_terima) {
            $publicUrlPath = $surat->file_bukti_terima;
            $storageDiskRelativePath = Str::startsWith($publicUrlPath, 'storage/')
                ? substr($publicUrlPath, strlen('storage/'))
                : null;

            if ($storageDiskRelativePath && \Storage::disk('public')->exists($storageDiskRelativePath)) {
                \Storage::disk('public')->delete($storageDiskRelativePath);
            }
        }

        // Hapus record
        $surat->delete();

        // Ambil URL asal (base64) dari form, lalu redirect balik jika valid & lokal
        $decodedBack = null;
        if ($request->filled('back')) {
            $tmp = base64_decode($request->input('back'), true); // true => strict, hindari nilai non-base64
            if ($tmp && Str::startsWith($tmp, url('/'))) {
                $decodedBack = $tmp;
            }
        }

        if ($decodedBack) {
            return redirect()->to($decodedBack)->with('success', 'Surat berhasil dihapus');
        }

        // Fallback: kembali ke halaman sebelumnya (bukan route admin)
        if (url()->previous()) {
            return redirect()->back()->with('success', 'Surat berhasil dihapus');
        }

        // Fallback terakhir (silakan ganti ke route user non-admin jika perlu)
        return redirect()->route('home')->with('success', 'Surat berhasil dihapus');
    }

    public function showHalaman()
    {
        return view('UI_Frontend.halaman_muka');
    }

    public function showBagianTU()
    {
        return view('UI_Frontend.bagian_tu_pimpinan');
    }

    // Listing surat kategori BIASA
    public function wakil_biasa(Request $request, string $slug)
    {
        $w = $this->wakilFromSlug($slug); abort_unless($w, 404);

        $penerima = User::where('name', $w['nama'])->first();
        $years = collect(); $data_surat = collect();

        if ($penerima) {
            $years = Surat::selectRaw('YEAR(created_at) as year')
                ->where('kategori','biasa')
                ->where('penerima_id',$penerima->id_users)
                ->distinct()->orderBy('year','desc')->pluck('year');

            $q = Surat::where('kategori','biasa')->where('penerima_id',$penerima->id_users);
            if ($request->filled('year'))  $q->whereYear('created_at', $request->year);
            if ($request->filled('month')) $q->whereMonth('created_at', $request->month);
            if ($request->filled('day'))   $q->whereDay('created_at', $request->day);
            $data_surat = $q->with(['user:id_users,name','penerima:id_users,name'])->orderBy('created_at','desc')->get();
        }

        return view('UI_Frontend.wakil_ketua_mpr.surat_wakil_ketua_mpr_biasa', [
            'nama_anggota_wakil' => $w['nama'],
            'years' => $years,
            'data_surat' => $data_surat,
        ]);
    }

    public function wakil_rahasia(Request $request, string $slug)
    {
        $w = $this->wakilFromSlug($slug); abort_unless($w, 404);

        $penerima = User::where('name', $w['nama'])->first();
        $years = collect(); $data_surat = collect();

        if ($penerima) {
            $years = Surat::selectRaw('YEAR(created_at) as year')
                ->where('kategori','rahasia')->where('penerima_id',$penerima->id_users)
                ->distinct()->orderBy('year','desc')->pluck('year');

            $q = Surat::where('kategori','rahasia')->where('penerima_id',$penerima->id_users);
            if ($request->filled('year'))  $q->whereYear('created_at', $request->year);
            if ($request->filled('month')) $q->whereMonth('created_at', $request->month);
            if ($request->filled('day'))   $q->whereDay('created_at', $request->day);
            $data_surat = $q->with(['user:id_users,name','penerima:id_users,name'])->orderBy('created_at','desc')->get();
        }

        return view('UI_Frontend.wakil_ketua_mpr.surat_wakil_ketua_mpr_rahasia', [
            'nama_anggota_wakil' => $w['nama'],
            'years' => $years,
            'data_surat' => $data_surat,
        ]);
    }

    public function wakil_sangat_rahasia(Request $request, string $slug)
    {
        $w = $this->wakilFromSlug($slug); abort_unless($w, 404);

        $penerima = User::where('name', $w['nama'])->first();
        $years = collect(); $data_surat = collect();

        if ($penerima) {
            $years = Surat::selectRaw('YEAR(created_at) as year')
                ->where('kategori','sangat_rahasia')->where('penerima_id',$penerima->id_users)
                ->distinct()->orderBy('year','desc')->pluck('year');

            $q = Surat::where('kategori','sangat_rahasia')->where('penerima_id',$penerima->id_users);
            if ($request->filled('year'))  $q->whereYear('created_at', $request->year);
            if ($request->filled('month')) $q->whereMonth('created_at', $request->month);
            if ($request->filled('day'))   $q->whereDay('created_at', $request->day);
            $data_surat = $q->with(['user:id_users,name','penerima:id_users,name'])->orderBy('created_at','desc')->get();
        }

        return view('UI_Frontend.wakil_ketua_mpr.surat_wakil_ketua_mpr_sangat_rahasia', [
            'nama_anggota_wakil' => $w['nama'],
            'years' => $years,
            'data_surat' => $data_surat,
        ]);
    }


    // Lihat Surat Ketua MPR
    public function lihat_surat_ketuampr($kategori, $id_surats)
    {
        try {
            $surat = Surat::where('kategori', $kategori)->findOrFail($id_surats);

            // Jika status surat belum 'dibaca', maka ubah status jadi 'dibaca' dan catat waktu dibaca
            if ($surat->status !== 'dibaca') {
                $surat->status = 'dibaca';
                $surat->dibaca = Carbon::now();
                $surat->save();
            }

            $pdfPath = null;
            if ($surat->file_surat) {
                $publicUrlPath = $surat->file_surat;
                $storageDiskRelativePath = null;

                if (strpos($publicUrlPath, 'storage/') === 0) {
                    $storageDiskRelativePath = substr($publicUrlPath, strlen('storage/'));
                } else {
                    \Log::warning("Format file_surat tidak terduga untuk surat ID {$surat->id_surats}: {$publicUrlPath}");
                }

                if ($storageDiskRelativePath && Storage::disk('public')->exists($storageDiskRelativePath)) {
                    $pdfPath = asset($publicUrlPath);
                } else {
                    \Log::error("File PDF tidak ditemukan di disk public untuk surat ID {$surat->id_surats}. Path yang dicari: 'public/{$storageDiskRelativePath}'. Nilai file_surat: '{$publicUrlPath}'");
                }
            }

            return view('UI_Frontend.ketua_mpr.lihat_surat_ketuampr', [
            'surat' => $surat,
            'pdfPath' => $pdfPath,
            'kategori' => $kategori,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return redirect()->route('UI_Frontend.ketua_mpr.lihat_surat_ketuampr', ['kategori' => $kategori])
                         ->with('error_message', 'Surat yang Anda cari tidak ditemukan.');
        }
    }

    //Lihat Surat Wakil Ketua MPR
    public function lihat_surat_wakil_ketuampr(Request $request, string $kategori, int $id_surats)
    {
        try {
            // 1) Ambil surat sesuai kategori
            $surat = Surat::where('kategori', $kategori)->findOrFail($id_surats);

            // 2) Update status baca (sekali saja)
            if ($surat->status !== 'dibaca') {
                $surat->status = 'dibaca';
                $surat->dibaca = Carbon::now();
                $surat->save();
            }

            // 3) Siapkan PDF path publik (sesuai penyimpanan "storage/...")
            $pdfPath = null;
            if ($surat->file_surat) {
                $publicUrlPath = $surat->file_surat; // contoh: "storage/surats/abc.pdf"
                $storageDiskRelativePath = Str::startsWith($publicUrlPath, 'storage/')
                    ? substr($publicUrlPath, strlen('storage/'))  // -> "surats/abc.pdf"
                    : null;

                if ($storageDiskRelativePath && Storage::disk('public')->exists($storageDiskRelativePath)) {
                    $pdfPath = asset($publicUrlPath);
                } else {
                    \Log::error("File PDF tidak ditemukan di disk public untuk surat ID {$surat->id_surats}. Cari: 'public/{$storageDiskRelativePath}'. Nilai file_surat: '{$publicUrlPath}'");
                }
            }

            // 4) Tangkap URL asal (back) agar tombol kembali tepat ke daftar sebelumnya
            //    Prioritas: ?back= (base64) -> Referer -> fallback halaman muka
            $backRaw = $request->query('back');
            $backUrl = $backRaw ? base64_decode($backRaw, true) : $request->headers->get('referer');

            // Sanitasi same-origin (hindari open redirect)
            $appUrl = rtrim(config('app.url'), '/');   // mis. http://persuratan.test
            $sameOrigin = is_string($backUrl) && $backUrl !== '' && (
                Str::startsWith($backUrl, '/') || Str::startsWith($backUrl, $appUrl)
            );
            if (!$sameOrigin) {
                $backUrl = route('halaman_muka'); // fallback aman
            }

            // 5) Kirim ke view
            return view('UI_Frontend.wakil_ketua_mpr.lihat_surat_wakil_ketuampr', [
                'surat'    => $surat,
                'pdfPath'  => $pdfPath,
                'kategori' => $kategori,
                'backUrl'  => $backUrl,   // <— dipakai tombol "Kembali ke Daftar"
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error_message', 'Surat yang Anda cari tidak ditemukan.');
        }
    }
    // Lihat Surat Bagian TU
    public function lihat_surat_bagian_tu($kategori, $id_surats)
    {
        try {
            $surat = Surat::where('kategori', $kategori)->findOrFail($id_surats);

            // Jika status surat belum 'dibaca', maka ubah status jadi 'dibaca' dan catat waktu dibaca
            if ($surat->status !== 'dibaca') {
                $surat->status = 'dibaca';
                $surat->dibaca = Carbon::now();
                $surat->save();
            }

            $pdfPath = null;
            if ($surat->file_surat) {
                $publicUrlPath = $surat->file_surat;
                $storageDiskRelativePath = null;

                if (strpos($publicUrlPath, 'storage/') === 0) {
                    $storageDiskRelativePath = substr($publicUrlPath, strlen('storage/'));
                } else {
                    \Log::warning("Format file_surat tidak terduga untuk surat ID {$surat->id_surats}: {$publicUrlPath}");
                }

                if ($storageDiskRelativePath && Storage::disk('public')->exists($storageDiskRelativePath)) {
                    $pdfPath = asset($publicUrlPath);
                } else {
                    \Log::error("File PDF tidak ditemukan di disk public untuk surat ID {$surat->id_surats}. Path yang dicari: 'public/{$storageDiskRelativePath}'. Nilai file_surat: '{$publicUrlPath}'");
                }
            }

            return view('UI_Frontend.bagian_tu_pimpinan.lihat_surat_bagian_tu_pimpinan', [
            'surat' => $surat,
            'pdfPath' => $pdfPath,
            'kategori' => $kategori,
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return redirect()->route('UI_Frontend.bagian_tu_pimpinan.lihat_surat_bagian_tu_pimpinan', ['kategori' => $kategori])
                         ->with('error_message', 'Surat yang Anda cari tidak ditemukan.');
        }
    }

    // Bagian Kearsipan – Surat Kategori Biasa
    public function surat_bagianKearsipan_biasa(Request $request)
    {
        $years = Surat::selectRaw('YEAR(created_at) as year')
            ->where('kategori', 'biasa')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

        $query = Surat::where('kategori', 'biasa')
            ->with(['user:id_users,name','penerima:id_users,name']); // <— tambah ini

        if ($request->filled('year'))  $query->whereYear('created_at', $request->year);
        if ($request->filled('month')) $query->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $query->whereDay('created_at', $request->day);

        $data_surat = $query->orderBy('created_at', 'desc')->get();

        return view('UI_Frontend.bagian_kearsipan.surat_bagianKearsipan_biasa', compact('data_surat','years'));
    }

    // Bagian Kearsipan – Surat Kategori Rahasia
    public function surat_bagianKearsipan_rahasia(Request $request)
    {
        // <- tambahkan ini
        if ($this->isUserOrCaraka()) {
            return redirect()
                ->route('surat.bagianKearsipan_biasa')
                ->with('error', 'Akses dibatasi. Anda hanya dapat melihat surat kategori BIASA.');
        }

        $years = Surat::selectRaw('YEAR(created_at) as year')
            ->where('kategori', 'rahasia')
            ->distinct()->orderBy('year','desc')->pluck('year');

        $query = Surat::where('kategori','rahasia')
            ->with(['user:id_users,name','penerima:id_users,name']); // <— tambah ini

        if ($request->filled('year'))  $query->whereYear('created_at', $request->year);
        if ($request->filled('month')) $query->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $query->whereDay('created_at', $request->day);

        $data_surat = $query->orderBy('created_at','desc')->get();

        return view('UI_Frontend.bagian_kearsipan.surat_bagianKearsipan_rahasia', compact('data_surat','years'));
    }

    // Bagian Kearsipan – Surat Kategori Sangat Rahasia
    public function surat_bagianKearsipan_sangat_rahasia(Request $request)
    {
        // <- tambahkan ini
        if ($this->isUserOrCaraka()) {
            return redirect()
                ->route('surat.bagianKearsipan_biasa')
                ->with('error', 'Akses dibatasi. Anda hanya dapat melihat surat kategori BIASA.');
        }

        $years = Surat::selectRaw('YEAR(created_at) as year')
            ->where('kategori', 'sangat_rahasia')
            ->distinct()->orderBy('year','desc')->pluck('year');

        $query = Surat::where('kategori','sangat_rahasia')
            ->with(['user:id_users,name','penerima:id_users,name']); // <— tambah ini

        if ($request->filled('year'))  $query->whereYear('created_at', $request->year);
        if ($request->filled('month')) $query->whereMonth('created_at', $request->month);
        if ($request->filled('day'))   $query->whereDay('created_at', $request->day);

        $data_surat = $query->orderBy('created_at','desc')->get();

        return view('UI_Frontend.bagian_kearsipan.surat_bagianKearsipan_sangat_rahasia', compact('data_surat','years'));
    }

    //lihat surat bagian kearsipan
    public function lihat_surat_bagian_kearsipan($kategori, $id_surats)
    {
        // <- tambahkan ini
        if ($this->isUserOrCaraka() && $kategori !== 'biasa') {
            return redirect()
                ->route('surat.bagianKearsipan_biasa')
                ->with('error', 'Akses dibatasi. Anda hanya dapat melihat surat kategori BIASA.');
        }

        try {
            $surat = Surat::where('kategori', $kategori)->findOrFail($id_surats);
            // Jika status surat belum 'dibaca', maka ubah status jadi 'dibaca' dan catat waktu dibaca
            if ($surat->status !== 'dibaca') {
                $surat->status = 'dibaca';
                $surat->dibaca = Carbon::now();
                $surat->save();
            }   
            $pdfPath = null;
            if ($surat->file_surat) {
                $publicUrlPath = $surat->file_surat;
                $storageDiskRelativePath = null;        
                if (strpos($publicUrlPath, 'storage/') === 0) {
                    $storageDiskRelativePath = substr($publicUrlPath, strlen('storage/'));
                } else {
                    \Log::warning("Format file_surat tidak terduga untuk surat ID {$surat->id_surats}: {$publicUrlPath}");
                }
                if ($storageDiskRelativePath && Storage::disk('public')->exists($storageDiskRelativePath)) {
                    $pdfPath = asset($publicUrlPath);
                } else {
                    \Log::error("File PDF tidak ditemukan di disk public untuk surat ID {$surat->id_surats}. Path yang dicari: 'public/{$storageDiskRelativePath}'. Nilai file_surat: '{$publicUrlPath}'");
                }
            }
            return view('UI_Frontend.bagian_kearsipan.lihat_surat_bagianKearsipan', [
                'surat' => $surat,
                'pdfPath' => $pdfPath,
                'kategori' => $kategori,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('UI_Frontend.bagian_kearsipan.lihat_surat_bagianKearsipan', ['kategori' => $kategori])
                            ->with('error_message', 'Surat yang Anda cari tidak ditemukan.');
        }
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if ($q === '') {
            return back()->with('error', 'Masukkan kata kunci untuk mencari surat.');
        }

        $role     = $this->currentRoleName();          // sudah ada helper di controller
        $isCaraka = ($role === 'caraka');

        $query = Surat::query()
            ->with(['user:id_users,name','penerima:id_users,name'])
            ->where(function ($qq) use ($q) {
                $like = "%{$q}%";

                $qq->where('judul_surat', 'like', $like)
                    ->orWhere('nomor_surat', 'like', $like)
                    ->orWhere('file_surat', 'like', $like)
                    ->orWhere('nama_file_asli', 'like', $like)
                    ->orWhere('pengirim_nama', 'like', $like)
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $like))
                    ->orWhere('penerima_eksternal', 'like', $like)
                    ->orWhereHas('penerima', fn ($userQuery) => $userQuery->where('name', 'like', $like));
            });

        // Role-based visibility
        if (in_array($role, ['sekretariat','kearsipan','bagian kearsipan','admin','administrator','superadmin','super admin'], true)) {
            // Tampilkan semua kategori (tanpa filter tambahan)
        } elseif ($isCaraka) {
            // Caraka: hanya kategori biasa + jenis masuk
            $query->where('kategori', 'biasa')
                ->where('jenis_surat', 'masuk');
        } elseif (in_array($role, ['user','bagian tu','bagian tu pimpinan','tata usaha'], true)) {
            // User & Bagian TU: hanya kategori biasa
            $query->where('kategori', 'biasa');
        } else {
            // Default konservatif: batasi ke kategori biasa
            $query->where('kategori', 'biasa');
        }

        $data_surat = $query->orderBy('created_at', 'desc')->get();

        return view('search.results', [
            'data_surat' => $data_surat,
            'q'          => $q,
        ]);
    }

    public function edit(Request $request, $id_surats)
    {
        // Hanya sekretariat/user yang boleh edit umum
        abort_unless(in_array($this->currentRoleName(), [
        'user','sekretariat','admin','administrator','superadmin','super admin'
            ]), 403);

        $surat = Surat::findOrFail($id_surats);

        // 1) Ambil back dari query (?back=...) -> base64 decode yang aman
        $backRaw = $request->query('back');
        $backUrl = null;

        if ($backRaw) {
            $decoded = base64_decode($backRaw, true);
            if (is_string($decoded) && $decoded !== '') {
                $backUrl = $decoded;
            }
        }

        // 2) Fallback kalau tidak ada: previous()
        if (!$backUrl) {
            $backUrl = url()->previous();
        }

        // 3) Amankan: hanya allow path relatif ("/...") ATAU absolute dengan host yang sama
        $appUrlHost  = parse_url(rtrim(config('app.url'), '/'), PHP_URL_HOST);
        $backUrlHost = parse_url($backUrl, PHP_URL_HOST);

        $sameOrigin = Str::startsWith($backUrl, '/')
                    || ($backUrlHost && $appUrlHost && $backUrlHost === $appUrlHost);

        if (!$sameOrigin) {
            // Fallback terakhir jika bukan origin yang sama
            $backUrl = route('halaman_muka');
        }

        // 4) Kirim ke blade
        $backHash = base64_encode($backUrl);

        return view('surat.edit', compact('surat', 'backUrl', 'backHash'));
    }

    public function editCaraka(Request $request, Surat $surat)
    {
        abort_unless($this->isCaraka(), 403);

        // Ambil ?back= (base64) atau previous()
        $backRaw = $request->query('back');
        $backUrl = null;

        if ($backRaw) {
            $decoded = base64_decode($backRaw, true);
            if (is_string($decoded) && $decoded !== '') {
                $backUrl = $decoded;
            }
        }
        if (!$backUrl) {
            $backUrl = url()->previous();
        }

        // Sanitasi same-origin
        $appUrl  = rtrim(config('app.url'), '/');
        $sameOrigin = Str::startsWith($backUrl, ['/',$appUrl]);
        if (!$sameOrigin) {
            $backUrl = route('surat.bagianTU_biasa'); // fallback aman
        }

        $backHash = base64_encode($backUrl);

        return view('surat.edit_caraka', compact('surat','backUrl','backHash'));
    }

    public function updateCaraka(Request $request, Surat $surat)
    {
        // Log upload untuk diagnosa
        if ($request->hasFile('file_bukti_terima')) {
            Log::info('CARAKA UPLOAD: ada file', [
                'name'  => $request->file('file_bukti_terima')->getClientOriginalName(),
                'size'  => $request->file('file_bukti_terima')->getSize(),
                'mime'  => $request->file('file_bukti_terima')->getMimeType(),
                'error' => $request->file('file_bukti_terima')->getError(),
            ]);
        } else {
            Log::warning('CARAKA UPLOAD: TIDAK ADA FILE diterima oleh PHP');
        }

        // Hanya Caraka yang boleh akses
        abort_unless($this->isCaraka(), 403);

        // STATUS dibuat OPSIONAL; FILE opsional, valid JPG/PNG max 2MB
        $rules = [
            'status'            => 'nullable|in:dikirim,diterima,ditolak',
            'file_bukti_terima' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'back'              => 'nullable|string',
        ];
        $messages = [
            'status.in'               => 'Status tidak valid.',
            'file_bukti_terima.image' => 'Bukti harus berupa gambar.',
            'file_bukti_terima.mimes' => 'Hanya JPG/PNG yang diperbolehkan.',
            'file_bukti_terima.max'   => 'Ukuran gambar maksimal 2 MB.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (ValidationException $e) {
            Log::error('VALIDATION FAIL updateCaraka', $e->errors());
            throw $e;
        }

        // Simpan file jika ada
        if ($request->hasFile('file_bukti_terima')) {
            // hapus lama jika ada
            if ($surat->file_bukti_terima) {
                $old = Str::startsWith($surat->file_bukti_terima, 'storage/')
                    ? Str::after($surat->file_bukti_terima, 'storage/')
                    : $surat->file_bukti_terima;
                if (Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }
            $path = $request->file('file_bukti_terima')->store('bukti_terima', 'public');
            $surat->file_bukti_terima = 'storage/' . $path; // agar langsung bisa di-asset()
        }

        // Update status HANYA jika ada pada request
        if ($request->filled('status')) {
            $surat->status = $validated['status'];
        }

        $surat->save();

        $success = 'Perubahan berhasil disimpan.';

        // Redirect balik sesuai 'back'
        $backRaw = $validated['back'] ?? null;
        if ($backRaw && ($decoded = base64_decode($backRaw, true))) {
            $target = $decoded;
            $appUrl = rtrim(config('app.url'), '/');
            if (Str::startsWith($target, ['/',$appUrl])) {
                $sep = str_contains($target, '?') ? '&' : '?';
                return redirect()->to($target.$sep.'refresh=1')->with('success', $success);
            }
        }
        return redirect()->route('surat.bagianTU_biasa')->with('success', $success);
    }


    public function update(Request $request, $id_surats)
    {
        abort_unless(in_array($this->currentRoleName(), [
                'user','sekretariat','admin','administrator','superadmin','super admin'
            ]), 403);

        $request->validate([
            'judul_surat' => 'required|string|max:255',
            'nomor_surat' => 'required|string|max:255',
            'back'        => 'nullable|string', // base64 dari halaman asal
        ]);

        $surat = Surat::findOrFail($id_surats);

        $surat->update([
            'judul_surat' => $request->judul_surat,
            'nomor_surat' => $request->nomor_surat,
        ]);

        $success = 'Surat berhasil diperbarui.';

        // Redirect balik ke halaman asal
        $backRaw = $request->input('back');
        if ($backRaw && ($decoded = base64_decode($backRaw, true))) {
            $target     = $decoded;
            $appUrlHost = parse_url(rtrim(config('app.url'), '/'), PHP_URL_HOST);
            $tgtHost    = parse_url($target, PHP_URL_HOST);

            $sameOrigin = Str::startsWith($target, '/')
                        || ($tgtHost && $appUrlHost && $tgtHost === $appUrlHost);

            if ($sameOrigin) {
                $sep = str_contains($target, '?') ? '&' : '?';
                return redirect()->to($target.$sep.'refresh=1')->with('success', $success);
            }
        }

        // Fallback aman
        return redirect()->route('halaman_muka')->with('success', $success);
    }

}