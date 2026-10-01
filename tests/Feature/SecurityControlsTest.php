<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityControlsTest extends TestCase
{
    public function test_anonymous_visitors_see_login_prompts_without_letter_categories(): void
    {
        foreach (['/surat_bagianTU', '/surat_bagianKearsipan'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('Silakan Login Untuk Melihat Surat')
                ->assertSee(route('login'))
                ->assertDontSee('Surat Kategori Biasa')
                ->assertDontSee('Surat Kategori Rahasia')
                ->assertDontSee('Surat Kategori Sangat Rahasia');
        }
    }

    public function test_confidential_pages_require_authentication(): void
    {
        foreach ([
            '/surat_ketua_mpr_rahasia',
            '/surat_ketua_mpr_sangat_rahasia',
            '/surat/filterRahasia',
            '/surat/filterSangatRahasia',
            '/surat_wakil_ketua_mpr_rahasia/bambang-wuryanto-mba',
            '/surat_wakil_ketua_mpr_sangat_rahasia/bambang-wuryanto-mba',
            '/surat_bagianKearsipan_rahasia',
            '/surat_bagianKearsipan_sangat_rahasia',
            '/lihat_surat_bagianKearsipan/rahasia/1',
        ] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_four_failed_login_attempts_lock_the_email_for_ten_minutes(): void
    {
        $email = 'security.test@example.com';
        $throttleKey = 'login:' . hash('sha256', Str::lower($email));
        $lockoutKey = $throttleKey . ':lockout';

        RateLimiter::clear($throttleKey);
        RateLimiter::clear($lockoutKey);
        Auth::shouldReceive('attempt')->times(4)->andReturn(false);

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->post('/login', [
                'email' => $email,
                'password' => 'StrongPass1!',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => $email,
            'password' => 'StrongPass1!',
        ])->assertSessionHasErrors('email');

        $this->assertGreaterThan(590, RateLimiter::availableIn($lockoutKey));

        RateLimiter::clear($throttleKey);
        RateLimiter::clear($lockoutKey);
    }
}