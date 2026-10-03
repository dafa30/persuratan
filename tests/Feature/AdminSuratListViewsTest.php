<?php

namespace Tests\Feature;

use App\Models\Surat;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class AdminSuratListViewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        View::share('errors', new ViewErrorBag());
    }

    public function test_dashboard_shows_only_summary_without_filter_or_list_sections(): void
    {
        $this->view('surat.dashboard', [
            'suratMasukCount' => 5,
            'suratKeluarCount' => 2,
            'years' => collect([2026]),
        ])
            ->assertSee('Dashboard Persuratan MPR RI')
            ->assertSee('7')
            ->assertDontSee('Filter')
            ->assertDontSee('Tambah Surat')
            ->assertDontSee('Daftar Surat');
    }

    public function test_incoming_table_renders_tracking_download_edit_and_delete_actions(): void
    {
        $surat = $this->makeSurat('masuk', 41, 'Sekretariat');

        $this->view('surat.surat_masuk', [
            'data' => collect([$surat]),
            'years' => collect([2026]),
        ])
            ->assertSee('Surat Masuk')
            ->assertSee(route('surat.file', ['surat' => 41]), false)
            ->assertSee(route('surat.detailsurat', ['kategori' => 'masuk', 'id_surats' => 41]), false)
            ->assertSee(route('surat.edit', ['id_surats' => 41]), false)
            ->assertSee(route('surat.destroy', 41), false);
    }

    public function test_outgoing_table_renders_caraka_edit_receipt_and_delete_actions(): void
    {
        $surat = $this->makeSurat('keluar', 42, 'Caraka');
        $surat->file_bukti_terima = 'storage/bukti_terima/receipt.jpg';

        $this->view('surat.surat_keluar', [
            'data' => collect([$surat]),
            'years' => collect([2026]),
        ])
            ->assertSee('Surat Keluar')
            ->assertSee(route('surat.file', ['surat' => 42]), false)
            ->assertSee(route('surat.detailsurat', ['kategori' => 'keluar', 'id_surats' => 42]), false)
            ->assertSee(route('surat.edit.caraka', ['surat' => 42]), false)
            ->assertSee(route('surat.file', ['surat' => 42, 'type' => 'receipt']), false)
            ->assertSee(route('surat.destroy', 42), false);
    }

    private function makeSurat(string $jenis, int $id, string $pengirim): Surat
    {
        $surat = new Surat();
        $surat->forceFill([
            'id_surats' => $id,
            'judul_surat' => 'Surat uji',
            'nomor_surat' => 'TEST-' . $id,
            'jenis_surat' => $jenis,
            'kategori' => 'biasa',
            'status' => 'dikirim',
            'pengirim_nama' => $pengirim,
            'penerima_eksternal' => $jenis === 'keluar' ? 'Penerima uji' : null,
            'perihal' => 'Keperluan uji',
            'file_surat' => 'storage/surats/test-' . $id . '.pdf',
            'created_at' => now(),
            'dibaca' => now(),
        ]);
        $surat->setRelation('user', null);
        $surat->setRelation('penerima', null);

        return $surat;
    }
}