<?php

namespace App\Console\Commands;

use App\Models\Surat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProtectConfidentialSuratFiles extends Command
{
    protected $signature = 'surat:protect-confidential-files {--dry-run}';

    protected $description = 'Move confidential letter files from public to private storage';

    public function handle(): int
    {
        $moved = 0;
        $failed = 0;

        Surat::whereIn('kategori', ['rahasia', 'sangat_rahasia'])
            ->chunkById(100, function ($surats) use (&$moved, &$failed) {
                foreach ($surats as $surat) {
                    $pendingDeletes = [];
                    $changed = false;

                    foreach (['file_surat', 'file_bukti_terima'] as $column) {
                        $storedPath = $surat->{$column};

                        if (!$storedPath || Str::startsWith($storedPath, 'private/')) {
                            continue;
                        }

                        $sourcePath = Str::startsWith($storedPath, 'storage/')
                            ? Str::after($storedPath, 'storage/')
                            : $storedPath;

                        if (!Storage::disk('public')->exists($sourcePath)) {
                            $this->error("Missing public file for surat {$surat->getKey()} ({$column}).");
                            $failed++;
                            continue;
                        }

                        $destinationPath = 'confidential-surats/' . $surat->getKey()
                            . '/' . $column . '-' . basename($sourcePath);

                        if ($this->option('dry-run')) {
                            $this->line("Would move surat {$surat->getKey()} ({$column}).");
                            continue;
                        }

                        if (!Storage::disk('local')->exists($destinationPath)) {
                            $stream = Storage::disk('public')->readStream($sourcePath);
                            if (!is_resource($stream)) {
                                $this->error("Could not read public file for surat {$surat->getKey()} ({$column}).");
                                $failed++;
                                continue;
                            }

                            try {
                                $written = Storage::disk('local')->writeStream($destinationPath, $stream);
                            } finally {
                                fclose($stream);
                            }

                            if (!$written) {
                                $this->error("Could not write private file for surat {$surat->getKey()} ({$column}).");
                                $failed++;
                                continue;
                            }
                        }

                        $surat->{$column} = 'private/' . $destinationPath;
                        $pendingDeletes[] = $sourcePath;
                        $changed = true;
                        $moved++;
                    }

                    if ($changed) {
                        $surat->save();
                        foreach ($pendingDeletes as $sourcePath) {
                            Storage::disk('public')->delete($sourcePath);
                        }
                    }
                }
            }, 'id_surats');

        $this->info("Moved {$moved} confidential file(s); {$failed} file(s) need attention.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}