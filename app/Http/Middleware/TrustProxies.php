<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    // Dibaca saat request berjalan: env() belum termuat ketika bootstrap/app.php dievaluasi.
    protected function proxies()
    {
        $value = trim((string) env('TRUSTED_PROXIES', ''));

        if ($value === '*' || $value === '**') {
            return $value;
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
