<?php

namespace Tests\Feature;

use App\Mail\SuratCreated;
use App\Models\Role;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuratWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::connection()->getPdo()->sqliteCreateFunction('YEAR', static function ($value) {
            return $value ? date('Y', strtotime($value)) : null;
        }, 1);

        Storage::fake('public');
        Storage::fake('local');

        $roleNames = ['admin', 'sekretariat', 'caraka', 'user', 'bagian_tu', 'kearsipan'];
        foreach ($roleNames as $index => $roleName) {
            DB::table('roles')->insert([
                'id_roles' => $index + 1,
                'nama_role' => $roleName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_secretariat_can_create_a_surat_and_send_its_email(): void
    {
        Mail::fake();
        $sender = $this->makeUser('sekretariat', 'QA Sekretariat', 'sekretariat@example.test');
        $recipient = $this->makeUser('ketua', 'H. Ahmad Muzani', 'ketua@example.test');

        $response = $this->actingAs($sender)->post(route('surat.store'), [
            'judul_surat' => 'QA surat masuk otomatis',
            'nomor_surat' => 'QA-IN-001',
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
            'file_surat' => UploadedFile::fake()->create('qa-in.pdf', 10, 'application/pdf'),
            'perihal' => 'Pengujian alur surat masuk',
            'pengirim_nama' => 'QA Sekretariat',
            'penerima_id' => $recipient->id_users,
        ]);

        $response->assertRedirect(route('surat.ketuamprbiasa'));
        $this->assertDatabaseHas('surats', [
            'nomor_surat' => 'QA-IN-001',
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
            'status' => 'dikirim',
            'pengirim_id' => $sender->id_users,
            'penerima_id' => $recipient->id_users,
        ]);

        $surat = Surat::where('nomor_surat', 'QA-IN-001')->firstOrFail();
        $this->assertStringStartsWith('storage/surats/', $surat->file_surat);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $surat->file_surat));
        Mail::assertSent(SuratCreated::class, fn (SuratCreated $mail) => $mail->hasTo($recipient->email));
    }

    public function test_caraka_can_create_outgoing_surat_and_update_delivery_with_receipt(): void
    {
        $caraka = $this->makeUser('caraka', 'QA Caraka', 'caraka@example.test');

        $response = $this->actingAs($caraka)->post(route('surat.store'), [
            'judul_surat' => 'QA pengiriman Caraka',
            'nomor_surat' => 'QA-CARAKA-001',
            'jenis_surat' => 'keluar',
            'kategori' => 'biasa',
            'file_surat' => UploadedFile::fake()->create('qa-caraka.pdf', 10, 'application/pdf'),
            'perihal' => 'Pengujian pengiriman',
            'penerima_eksternal' => 'Penerima Uji',
        ]);

        $response->assertRedirect(route('surat.ketuamprbiasa'));
        $surat = Surat::where('nomor_surat', 'QA-CARAKA-001')->firstOrFail();
        $this->assertSame('Caraka', $surat->pengirim_nama);
        $this->assertNull($surat->penerima_id);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $surat->file_surat));

        $response = $this->actingAs($caraka)->put(route('surat.update.caraka', ['surat' => $surat]), [
            'status' => 'diterima',
            'file_bukti_terima' => UploadedFile::fake()->image('receipt.png'),
        ]);

        $response->assertRedirect(route('surat.bagianTU_biasa'));
        $surat->refresh();
        $this->assertSame('diterima', $surat->status);
        $this->assertStringStartsWith('storage/bukti_terima/', $surat->file_bukti_terima);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $surat->file_bukti_terima));
    }

    public function test_store_rejects_missing_fields_and_unsupported_uploads(): void
    {
        $sender = $this->makeUser('sekretariat', 'QA Sekretariat', 'sekretariat@example.test');
        $recipient = $this->makeUser('ketua', 'H. Ahmad Muzani', 'ketua@example.test');
        $this->actingAs($sender);

        $this->post(route('surat.store'), [
            'penerima_id' => $recipient->id_users,
        ])->assertSessionHasErrors(['judul_surat', 'kategori', 'file_surat', 'perihal']);

        $this->post(route('surat.store'), [
            'judul_surat' => 'QA file tidak valid',
            'nomor_surat' => 'QA-INVALID-001',
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
            'file_surat' => UploadedFile::fake()->create('not-a-document.exe', 10, 'application/octet-stream'),
            'perihal' => 'Uji validasi lampiran',
            'pengirim_nama' => 'QA Sekretariat',
            'penerima_id' => $recipient->id_users,
        ])->assertSessionHasErrors('file_surat');

        $this->assertDatabaseCount('surats', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('surats'));
    }

    public function test_authorized_user_can_update_surat_metadata(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $surat = $this->makeSurat(['nomor_surat' => 'QA-EDIT-001']);

        $response = $this->actingAs($admin)->put(route('surat.update', ['id_surats' => $surat->id_surats]), [
            'judul_surat' => 'Judul hasil edit QA',
            'nomor_surat' => 'QA-EDIT-002',
        ]);

        $response->assertRedirect(route('halaman_muka'));
        $this->assertDatabaseHas('surats', [
            'id_surats' => $surat->id_surats,
            'judul_surat' => 'Judul hasil edit QA',
            'nomor_surat' => 'QA-EDIT-002',
        ]);
    }

    public function test_search_and_lists_return_only_matching_surat_data(): void
    {
        $user = $this->makeUser('user', 'QA User', 'user@example.test');
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $ordinary = $this->makeSurat([
            'judul_surat' => 'QA-SCOPE-ordinary-incoming',
            'nomor_surat' => 'QA-SCOPE-001',
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
        ]);
        $this->makeSurat([
            'judul_surat' => 'QA-SCOPE-confidential',
            'nomor_surat' => 'QA-SCOPE-002',
            'jenis_surat' => 'masuk',
            'kategori' => 'rahasia',
        ]);
        $outgoing = $this->makeSurat([
            'judul_surat' => 'QA-SCOPE-ordinary-outgoing',
            'nomor_surat' => 'QA-SCOPE-003',
            'jenis_surat' => 'keluar',
            'kategori' => 'biasa',
        ]);

        $response = $this->actingAs($user)->get(route('surat.search', ['q' => 'QA-SCOPE']));
        $response->assertOk()->assertSee($ordinary->judul_surat)->assertDontSee('QA-SCOPE-confidential');

        $this->actingAs($admin)->get(route('surat.masuk'))
            ->assertOk()->assertSee($ordinary->judul_surat)->assertDontSee($outgoing->judul_surat);
        $this->actingAs($admin)->get(route('surat.keluar'))
            ->assertOk()->assertSee($outgoing->judul_surat)->assertDontSee($ordinary->judul_surat);
    }

    public function test_surat_detail_and_file_download_work(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        Storage::disk('public')->put('surats/qa-document.pdf', '%PDF-1.4 QA fixture');
        $surat = $this->makeSurat([
            'judul_surat' => 'QA surat untuk detail',
            'jenis_surat' => 'masuk',
            'file_surat' => 'storage/surats/qa-document.pdf',
            'nama_file_asli' => 'qa-document.pdf',
        ]);

        $this->get(route('surat.detailsurat', ['kategori' => 'masuk', 'id_surats' => $surat->id_surats]))
            ->assertOk()->assertSee('Status Tracking Surat');
        $this->actingAs($admin)->get(route('surat.file', ['surat' => $surat->id_surats]))
            ->assertOk()->assertDownload('qa-document.pdf');
    }

    public function test_opening_surat_viewer_marks_it_as_read(): void
    {
        $recipient = $this->makeUser('ketua', 'QA Ketua', 'ketua@example.test');
        $surat = $this->makeSurat([
            'kategori' => 'biasa',
            'status' => 'dikirim',
            'penerima_id' => $recipient->id_users,
        ]);
        $sentAt = $surat->created_at;

        $this->actingAs($recipient)->get(route('lihat.surat.ketuampr', [
            'kategori' => 'biasa',
            'id_surats' => $surat->id_surats,
        ]))->assertOk()
            ->assertSee('Surat Dikirim')
            ->assertSee('Surat Dibaca');

        $this->assertDatabaseHas('surats', [
            'id_surats' => $surat->id_surats,
            'status' => 'dibaca',
        ]);
        $readSurat = $surat->fresh();
        $this->assertNotNull($readSurat->dibaca);
        $this->assertSame($sentAt->toDateTimeString(), $readSurat->created_at->toDateTimeString());
    }

    public function test_admin_can_delete_surat_and_its_stored_file(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        Storage::disk('public')->put('surats/qa-delete.pdf', '%PDF-1.4 QA fixture');
        $surat = $this->makeSurat([
            'nomor_surat' => 'QA-DELETE-001',
            'file_surat' => 'storage/surats/qa-delete.pdf',
        ]);

        $this->actingAs($admin)->delete(route('surat.destroy', $surat->id_surats))
            ->assertSessionHas('success', 'Surat berhasil dihapus');

        $this->assertDatabaseMissing('surats', ['id_surats' => $surat->id_surats]);
        Storage::disk('public')->assertMissing('surats/qa-delete.pdf');
    }

    public function test_user_can_register_login_and_logout_with_the_default_role(): void
    {
        $this->post(route('register.post'), [
            'name' => 'QA Registered User',
            'email' => 'registered@example.test',
            'password' => 'TestPassword123!',
        ])->assertRedirect(route('register'))
            ->assertSessionHas('register_success', true);

        $registered = User::where('email', 'registered@example.test')->firstOrFail();
        $this->assertSame(4, $registered->role_id);

        $this->post(route('login.post'), [
            'email' => 'registered@example.test',
            'password' => 'TestPassword123!',
        ])->assertRedirect(route('halaman_muka'));
        $this->assertAuthenticatedAs($registered);

        $this->post(route('logout'))->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_guest_cannot_open_the_dashboard_directly(): void
    {
        $this->get(route('surat.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_login_and_is_redirected_to_the_dashboard(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin Login', 'admin-login@example.test');

        $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'TestPassword123!',
        ])->assertRedirect(route('surat.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_role_settings_are_admin_only_and_admin_can_change_a_role(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $secretariat = $this->makeUser('sekretariat', 'QA Sekretariat', 'sekretariat@example.test');
        $target = $this->makeUser('user', 'QA Role Target', 'target@example.test');

        $this->actingAs($admin)->get(route('setting.role.index'))->assertOk();
        $this->actingAs($admin)->put(route('setting.role.update', $target->id_users), [
            'role_id' => Role::where('nama_role', 'caraka')->value('id_roles'),
        ])->assertRedirect(route('setting.role.index'));
        $this->assertDatabaseHas('users', [
            'id_users' => $target->id_users,
            'role_id' => 3,
        ]);

        $this->actingAs($secretariat)->get(route('setting.role.index'))->assertForbidden();
        $this->actingAs($secretariat)->put(route('setting.role.update', $target->id_users), [
            'role_id' => 1,
        ])->assertForbidden();
    }

    public function test_non_admin_cannot_delete_a_user_through_role_management_route(): void
    {
        $user = $this->makeUser('user', 'QA User', 'user@example.test');
        $target = $this->makeUser('sekretariat', 'QA Protected Account', 'protected@example.test');

        $this->actingAs($user)->delete(route('setting.role.destroy', $target->id_users))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id_users' => $target->id_users]);
    }

    public function test_dashboard_counts_and_date_filter_use_matching_surat_records(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $oldIncoming = $this->makeSurat(['jenis_surat' => 'masuk']);
        $oldIncoming->created_at = now()->subYear();
        $oldIncoming->save();
        $this->makeSurat(['jenis_surat' => 'masuk']);
        $this->makeSurat(['jenis_surat' => 'keluar']);

        $this->actingAs($admin)->get(route('surat.index', ['year' => now()->year]))
            ->assertOk()
            ->assertViewHas('suratMasukCount', 1)
            ->assertViewHas('suratKeluarCount', 1);
    }

    public function test_bagian_tu_recipient_is_saved_as_incoming_without_email(): void
    {
        Mail::fake();
        $sender = $this->makeUser('sekretariat', 'QA Sekretariat', 'sekretariat@example.test');
        $recipient = $this->makeUser('bagian_tu', 'Bagian TU Pimpinan Setjen', 'tu@example.test');

        $this->actingAs($sender)->post(route('surat.store'), [
            'judul_surat' => 'QA surat untuk TU',
            'nomor_surat' => 'QA-TU-001',
            'jenis_surat' => 'keluar',
            'kategori' => 'biasa',
            'file_surat' => UploadedFile::fake()->create('qa-tu.pdf', 10, 'application/pdf'),
            'perihal' => 'Pengujian penerima Bagian TU',
            'pengirim_nama' => 'QA Sekretariat',
            'penerima_id' => $recipient->id_users,
        ])->assertRedirect(route('surat.bagianTU_biasa'));

        $this->assertDatabaseHas('surats', [
            'nomor_surat' => 'QA-TU-001',
            'jenis_surat' => 'masuk',
            'penerima_id' => $recipient->id_users,
        ]);
        Mail::assertNothingSent();
    }

    public function test_confidential_surat_detail_and_file_require_authentication(): void
    {
        $user = $this->makeUser('user', 'QA User', 'user@example.test');
        $archiver = $this->makeUser('kearsipan', 'QA Kearsipan', 'kearsipan@example.test');

        foreach (['biasa', 'rahasia', 'sangat_rahasia'] as $category) {
            $surat = $this->makeSurat(['kategori' => $category]);

            if ($category !== 'biasa') {
                auth()->logout();
                $this->get(route('surat.detailsurat', [
                    'kategori' => 'masuk',
                    'id_surats' => $surat->id_surats,
                ]))->assertRedirect(route('login'));
            }

            $this->actingAs($archiver)->get(route('lihat.surat.bagian.kearsipan', [
                'kategori' => $category,
                'id_surats' => $surat->id_surats,
            ]))->assertOk();
        }

        Storage::disk('local')->put('surat-private/qa-secret.pdf', '%PDF-1.4 QA private fixture');
        $privateSurat = $this->makeSurat([
            'kategori' => 'rahasia',
            'file_surat' => 'private/surat-private/qa-secret.pdf',
            'nama_file_asli' => 'qa-secret.pdf',
        ]);

        auth()->logout();
        $this->get(route('surat.file', ['surat' => $privateSurat->id_surats]))
            ->assertRedirect(route('login'));
        $this->actingAs($user)->get(route('surat.file', ['surat' => $privateSurat->id_surats]))
            ->assertOk()->assertDownload('qa-secret.pdf');
    }

    public function test_authentication_and_registration_reject_invalid_inputs(): void
    {
        $this->post(route('login.post'), [])->assertSessionHasErrors(['email', 'password']);
        $this->post(route('login.post'), [
            'email' => 'not-an-email',
            'password' => 'TestPassword123!',
        ])->assertSessionHasErrors('email');

        $this->makeUser('user', 'Existing QA User', 'duplicate@example.test');
        $this->post(route('register.post'), [
            'name' => 'Duplicate QA User',
            'email' => 'duplicate@example.test',
            'password' => 'TestPassword123!',
        ])->assertSessionHasErrors('email');

        $this->post(route('register.post'), [
            'name' => 'Weak Password User',
            'email' => 'weak@example.test',
            'password' => 'weak',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'weak@example.test']);
    }

    public function test_admin_can_delete_a_test_account(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $target = $this->makeUser('user', 'QA Removable Account', 'remove@example.test');

        $this->actingAs($admin)->delete(route('setting.role.destroy', $target->id_users))
            ->assertRedirect(route('setting.role.index'));

        $this->assertDatabaseMissing('users', ['id_users' => $target->id_users]);
    }

    public function test_admin_dashboard_shows_zero_counts_when_no_surat_exist(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');

        $this->actingAs($admin)->get(route('surat.index'))
            ->assertOk()
            ->assertViewHas('suratMasukCount', 0)
            ->assertViewHas('suratKeluarCount', 0);
    }

    public function test_surat_lists_filter_by_date_and_show_empty_results(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');
        $matched = $this->makeSurat(['judul_surat' => 'QA-DATE-MATCH']);
        $matched->created_at = now()->setDate(2025, 4, 10)->setTime(9, 0);
        $matched->save();
        $unmatched = $this->makeSurat(['judul_surat' => 'QA-DATE-OTHER']);
        $unmatched->created_at = now()->setDate(2025, 4, 11)->setTime(9, 0);
        $unmatched->save();

        $this->actingAs($admin)->get(route('surat.masuk', [
            'year' => 2025,
            'month' => 4,
            'day' => 10,
        ]))->assertOk()->assertSee('QA-DATE-MATCH')->assertDontSee('QA-DATE-OTHER');

        $this->get(route('surat.masuk', ['year' => 2024]))
            ->assertOk()->assertSee('Tidak ada data surat masuk.');
    }

    public function test_incoming_and_outgoing_lists_have_empty_states(): void
    {
        $admin = $this->makeUser('admin', 'QA Admin', 'admin@example.test');

        $this->actingAs($admin)->get(route('surat.masuk'))
            ->assertOk()->assertSee('Tidak ada data surat masuk.');
        $this->get(route('surat.keluar'))
            ->assertOk()->assertSee('Tidak ada data surat keluar.');
    }

    public function test_search_displays_empty_state_when_there_are_no_matches(): void
    {
        $user = $this->makeUser('user', 'QA User', 'user@example.test');

        $this->actingAs($user)->get(route('surat.search', ['q' => 'NO-SUCH-QA-SURAT']))
            ->assertOk()->assertSee('Tidak ada surat ditemukan.');
    }

    public function test_invalid_surat_id_or_type_redirects_to_dashboard(): void
    {
        $surat = $this->makeSurat(['jenis_surat' => 'masuk']);

        $this->get(route('surat.detailsurat', ['kategori' => 'masuk', 'id_surats' => 999999]))
            ->assertRedirect(route('surat.index'));
        $this->get(route('surat.detailsurat', ['kategori' => 'keluar', 'id_surats' => $surat->id_surats]))
            ->assertRedirect(route('surat.index'));
    }

    public function test_bagian_tu_list_contains_only_surat_for_that_section(): void
    {
        $bagianTu = $this->makeUser('bagian_tu', 'Bagian TU Pimpinan Setjen', 'tu@example.test');
        $owned = $this->makeSurat([
            'judul_surat' => 'QA-TU-OWNED',
            'jenis_surat' => 'masuk',
            'penerima_id' => $bagianTu->id_users,
        ]);
        $this->makeSurat(['judul_surat' => 'QA-TU-UNRELATED', 'jenis_surat' => 'masuk']);

        $this->actingAs($bagianTu)->get(route('surat.bagianTU_biasa'))
            ->assertOk()
            ->assertViewHas('data_surat', fn ($data) => $data->contains('id_surats', $owned->id_surats))
            ->assertSee('QA-TU-OWNED')
            ->assertDontSee('QA-TU-UNRELATED');
    }

    public function test_kearsipan_lists_each_confidentiality_category(): void
    {
        $archiver = $this->makeUser('kearsipan', 'QA Kearsipan', 'kearsipan@example.test');
        $categories = [
            ['biasa', 'surat.bagianKearsipan_biasa', 'QA-ARSIP-BIASA'],
            ['rahasia', 'surat.bagianKearsipan_rahasia', 'QA-ARSIP-RAHASIA'],
            ['sangat_rahasia', 'surat.bagianKearsipan_sangat_rahasia', 'QA-ARSIP-SANGAT-RAHASIA'],
        ];

        foreach ($categories as [$category, $routeName, $title]) {
            $surat = $this->makeSurat(['kategori' => $category, 'judul_surat' => $title]);

            $this->actingAs($archiver)->get(route($routeName))
                ->assertOk()
                ->assertViewHas('data_surat', fn ($data) => $data->contains('id_surats', $surat->id_surats))
                ->assertSee($title);
        }
    }

    public function test_ketua_list_only_includes_surat_addressed_to_the_chair(): void
    {
        $ketua = $this->makeUser('user', 'H. Ahmad Muzani', 'ketua@example.test');
        $categories = [
            ['biasa', 'surat.ketuamprbiasa', 'QA-KETUA-BIASA'],
            ['rahasia', 'surat.ketuamprrahasia', 'QA-KETUA-RAHASIA'],
            ['sangat_rahasia', 'surat.ketuamprsangatrahasia', 'QA-KETUA-SANGAT-RAHASIA'],
        ];

        foreach ($categories as [$category, $routeName, $title]) {
            $addressed = $this->makeSurat([
                'kategori' => $category,
                'judul_surat' => $title,
                'penerima_id' => $ketua->id_users,
            ]);
            $this->makeSurat([
                'kategori' => $category,
                'judul_surat' => $title . '-OTHER',
            ]);

            $this->actingAs($ketua)->get(route($routeName))
                ->assertOk()
                ->assertViewHas('data_surat', fn ($data) => $data->contains('id_surats', $addressed->id_surats))
                ->assertSee($title)
                ->assertDontSee($title . '-OTHER');
        }
    }

    public function test_wakil_lists_only_surat_addressed_to_that_wakil(): void
    {
        $wakil = $this->makeUser('user', 'Ir. Bambang Wuryanto, M.B.A.', 'wakil@example.test');
        $slug = 'bambang-wuryanto-mba';
        $categories = [
            ['biasa', 'surat.wakilketuamprbiasa', 'QA-WAKIL-BIASA'],
            ['rahasia', 'surat.wakilketuamprrahasia', 'QA-WAKIL-RAHASIA'],
            ['sangat_rahasia', 'surat.wakilketuamprsangatrahasia', 'QA-WAKIL-SANGAT-RAHASIA'],
        ];

        foreach ($categories as [$category, $routeName, $title]) {
            $addressed = $this->makeSurat([
                'kategori' => $category,
                'judul_surat' => $title,
                'penerima_id' => $wakil->id_users,
            ]);
            $this->makeSurat([
                'kategori' => $category,
                'judul_surat' => $title . '-OTHER',
            ]);

            $this->actingAs($wakil)->get(route($routeName, ['slug' => $slug]))
                ->assertOk()
                ->assertViewHas('data_surat', fn ($data) => $data->contains('id_surats', $addressed->id_surats))
                ->assertSee($title)
                ->assertDontSee($title . '-OTHER');
        }
    }

    public function test_mail_failure_returns_error_but_keeps_the_saved_surat(): void
    {
        $sender = $this->makeUser('sekretariat', 'QA Sekretariat', 'sekretariat@example.test');
        $recipient = $this->makeUser('ketua', 'H. Ahmad Muzani', 'ketua@example.test');
        Mail::shouldReceive('to')->once()->with($recipient->email)
            ->andThrow(new \RuntimeException('Simulated mail transport failure'));

        $this->actingAs($sender)->post(route('surat.store'), [
            'judul_surat' => 'QA email gagal',
            'nomor_surat' => 'QA-MAIL-FAIL-001',
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
            'file_surat' => UploadedFile::fake()->create('qa-mail-fail.pdf', 10, 'application/pdf'),
            'perihal' => 'Uji penanganan mail gagal',
            'pengirim_nama' => 'QA Sekretariat',
            'penerima_id' => $recipient->id_users,
        ])->assertSessionHas('error', 'Surat disimpan, tetapi notifikasi email gagal dikirim.');

        $this->assertDatabaseHas('surats', ['nomor_surat' => 'QA-MAIL-FAIL-001']);
    }

    private function makeUser(string $roleName, string $name, string $email): User
    {
        $role = Role::query()->firstOrCreate(['nama_role' => $roleName]);

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => 'TestPassword123!',
            'role_id' => $role->id_roles,
        ]);
    }

    private function makeSurat(array $overrides = []): Surat
    {
        return Surat::query()->create(array_merge([
            'judul_surat' => 'QA surat fixture',
            'nomor_surat' => 'QA-' . uniqid(),
            'jenis_surat' => 'masuk',
            'kategori' => 'biasa',
            'status' => 'dikirim',
            'file_surat' => null,
            'perihal' => 'Data sintetis untuk test otomatis',
            'pengirim_nama' => 'QA Sekretariat',
        ], $overrides));
    }
}