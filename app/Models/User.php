<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $primaryKey = 'id_users';
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \App\Models\Concerns\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Tambahkan role_id agar dapat diisi
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_roles');
    }

    /**
     * URL foto anggota di public/anggota_mpr sesuai nama user; null jika tidak ada.
     */
    public function getFotoUrlAttribute(): ?string
    {
        $map = [
            'muzani'    => 'ahmad_muzani',
            'bambang'   => 'bambang_wuryanto',
            'kahar'     => 'kahar_muzakir',
            'lestari'   => 'lestari',
            'rusdi'     => 'rusdi',
            'hidayat'   => 'hidayat_nur',
            'soeparno'  => 'eddy_soeparno',
            'edhie'     => 'edhie_baskoro',
            'abcandra'  => 'abcandra',
            'setjen'    => 'setjen',
        ];

        $name = strtolower((string) $this->name);
        foreach ($map as $keyword => $file) {
            if (!str_contains($name, $keyword)) {
                continue;
            }
            foreach (['webp', 'jpg'] as $ext) {
                if (file_exists(public_path("anggota_mpr/{$file}.{$ext}"))) {
                    return asset("anggota_mpr/{$file}.{$ext}");
                }
            }
        }

        return null;
    }

}