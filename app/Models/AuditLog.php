<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /** Catat satu kejadian; kegagalan pencatatan tidak boleh mengganggu aplikasi. */
    public static function record(string $event, ?string $type = null, $id = null, ?array $old = null, ?array $new = null, $user = null): void
    {
        try {
            $user = $user ?? Auth::user();
            $request = app()->bound('request') ? request() : null;

            static::create([
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name,
                'event' => $event,
                'auditable_type' => $type,
                'auditable_id' => $id !== null ? (string) $id : null,
                'old_values' => $old ?: null,
                'new_values' => $new ?: null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit log gagal dicatat: ' . $e->getMessage());
        }
    }
}
