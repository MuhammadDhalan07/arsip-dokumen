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
                'bobot' => 10.00,
                'is_active' => true,
            ],
            [
                'name' => 'Laporan',
                'type' => JenisRincian::INTERNAL,
                'bobot' => 15.00,
                'is_active' => true,
            ],
            [
                'name' => 'Panduan',
                'type' => JenisRincian::INTERNAL,
                'bobot' => 10.00,
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Pemeriksaan dan Serah Terima',
                'type' => JenisRincian::INTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Pembayaran',
                'type' => JenisRincian::INTERNAL,
                'bobot' => 8.00,
                'is_active' => true,
            ],
            [
                'name' => 'RAB',
                'type' => JenisRincian::INTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ],
            [
                'name' => 'KAK',
                'type' => JenisRincian::EKSTERNAL,
                'bobot' => 15.00,
                'is_active' => true,
            ],
            [
                'name' => 'HPS',
                'type' => JenisRincian::EKSTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ],
            [
                'name' => 'SPK',
                'type' => JenisRincian::EKSTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ],
            [
                'name' => 'SPMK',
                'type' => JenisRincian::EKSTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ],
            [
                'name' => 'BAST',
                'type' => JenisRincian::EKSTERNAL,
                'bobot' => 7.00,
                'is_active' => true,
            ]
        ];
        foreach ($data as $item) {
            Rincian::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
