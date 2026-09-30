<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $primaryKey = 'id_surats';

    use HasFactory;

    // (opsional) jika nama tabel non-standar, tapi di sini sudah "surats" -> aman.
    // protected $table = 'surats';

    /**
     * Kolom yang boleh diisi mass-assignment.
     */
    protected $fillable = [
        'judul_surat',
        'nomor_surat',
        'jenis_surat',
        'status',
        'dibaca',
        'file_surat',
        'nama_file_asli',
        'perihal',
        'kategori',            // string kategori (bukan kategori_id)
        'pengirim_id',         // ID user pengirim (yang mencatat/mengunggah)
        'pengirim_nama',       // snapshot nama pengirim (khusus Caraka -> "Caraka")
        'penerima_id',         // ID penerima internal (untuk email)
        'penerima_eksternal',  // nama penerima bebas (alur Caraka)
        'file_bukti_terima',   // path bukti foto (jpg/png) saat diterima oleh Caraka
    ];

    /**
     * Casting kolom tanggal.
     */
    protected $casts = [
        'dibaca' => 'datetime',
    ];

    /**
     * (Opsional) otomatis ikut saat toArray()/toJson()
     * sehingga bisa langsung terlihat pengirim_display & penerima_display.
     */
    protected $appends = [
        'pengirim_display',
        'penerima_display',
    ];

    /**
     * Relasi ke User pengirim (yang mencatat/mengunggah).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'pengirim_id', 'id_users'); // Sesuaikan foreign key dan local key
    }

    /**
     * Relasi ke User penerima internal (jika pakai select ID).
     */
    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id', 'id_users'); // Sesuaikan foreign key dan local key
    }

    /**
     * Accessor: nama pengirim siap cetak.
     * Prioritas: snapshot 'pengirim_nama' -> relasi user->name.
     */
    public function getPengirimDisplayAttribute(): ?string
    {
        return $this->pengirim_nama ?: optional($this->user)->name;
    }

    /**
     * Accessor: nama penerima siap cetak.
     * Prioritas: 'penerima_eksternal' -> relasi penerima->name.
     */
    public function getPenerimaDisplayAttribute(): ?string
    {
        return $this->penerima_eksternal ?: optional($this->penerima)->name;
    }

    public function getJenisUntukTuAttribute()
    {
        return ($this->jenis_surat === 'keluar' && $this->pengirim_nama === 'Caraka')
            ? 'masuk'
            : $this->jenis_surat;
    }
}
