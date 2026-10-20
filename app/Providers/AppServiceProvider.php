<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, fn ($e) => AuditLog::record('login', get_class($e->user), $e->user->getAuthIdentifier(), null, null, $e->user));
        Event::listen(Logout::class, function ($e) {
            if ($e->user) {
                AuditLog::record('logout', get_class($e->user), $e->user->getAuthIdentifier(), null, null, $e->user);
            }
        });
        Event::listen(Failed::class, fn ($e) => AuditLog::record('login_failed', null, null, null, ['email' => $e->credentials['email'] ?? null]));
    }
}
