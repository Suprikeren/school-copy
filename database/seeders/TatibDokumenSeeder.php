<?php

namespace Database\Seeders;

use App\Models\TatibDokumen;
use App\Models\User;
use Database\Seeders\Concerns\AssignsExplicitIds;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TatibDokumenSeeder extends Seeder
{
    use AssignsExplicitIds;

    public function run(): void
    {
        $path = 'tatib/dokumen-tata-tertib-contoh.pdf';

        if (! Storage::disk('public')->exists($path)) {
            $this->command?->warn(
                "TatibDokumenSeeder dilewati: file {$path} belum tersedia. "
                ."Letakkan file contoh di storage/app/public/{$path} terlebih dahulu."
            );

            return;
        }

        $uploader = User::query()->where('email', 'admin@example.com')->first()
            ?? User::role('admin')->first();

        if ($uploader === null) {
            $this->command?->warn('TatibDokumenSeeder dilewati: user pengunggah tidak ditemukan.');

            return;
        }

        $this->firstOrCreateWithId(
            TatibDokumen::class,
            ['path' => $path],
            [
                'nama_file' => 'Dokumen Tata Tertib Contoh',
                'diunggah_oleh' => $uploader->id,
                'tanggal' => now()->toDateString(),
                'created_by' => 'seeder',
                'updated_by' => 'seeder',
            ]
        );
    }
}
