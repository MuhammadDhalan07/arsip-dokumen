<?php

namespace Database\Seeders\Pendukung;

use App\Enums\JenisRincian;
use App\Models\Rincian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RincianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Dokumen penawaran',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'Laporan',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'Panduan',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Pemeriksaan dan Serah Terima',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Pembayaran',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'RAB',
                'type' => JenisRincian::INTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'KAK',
                'type' => JenisRincian::EKSTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'HPS',
                'type' => JenisRincian::EKSTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'SPK',
                'type' => JenisRincian::EKSTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'SPMK',
                'type' => JenisRincian::EKSTERNAL,
                'is_active' => true,
            ],
            [
                'name' => 'BAST',
                'type' => JenisRincian::EKSTERNAL,
                'is_active' => true,
            ]
        ];
        foreach ($data as $item) {
            Rincian::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
