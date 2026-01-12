<?php

namespace App\Actions\Fetching;

use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Skpd
{
    public function fetch(int $tahun, int $daerah): int
    {
        $url = config('sipd.base_url');
        $apiKey = config('sipd.key');
        $endpoint = config('sipd.endpoint.data_skpd');

        $fullUrl = "{$url}{$endpoint}?tahun={$tahun}&daerah={$daerah}";

        $response = Http::withToken($apiKey)->get($fullUrl);

        if ($response->failed()) {
            Log::error('Gagal fetch SKPD', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $fullUrl,
            ]);

            return 0;
        }

        $data = $response->json();

        if (empty($data) || ! isset($data['data'])) {
            Log::warning('API SKPD tidak mengembalikan data', ['url' => $fullUrl]);

            return 0;
        }

        $items = $data['data'];
        $imported = 0;

        foreach ($items as $item) {
            Organization::updateOrCreate(
                [
                    'id_organization' => $item['id_skpd'],
                    'tahun' => $item['tahun'],
                ],
                [
                    'id_induk' => $item['id_induk'] ?? null,
                    'kode_organization' => $item['kode_skpd'] ?? null,
                    'nama_organization' => $item['nama_skpd'] ?? $item['nama_organization'] ?? null,
                    'jenis_organization' => $item['jenis_skpd'] ?? null,
                ]
            );
            $imported++;
        }

        Log::info("Berhasil import {$imported} SKPD untuk tahun {$tahun} daerah {$daerah}");

        return $imported;
    }
}
