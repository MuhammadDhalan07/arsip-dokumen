<?php

namespace App\Filament\Widgets;

use App\ArsipDokumen;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Rincian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DokumenWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tahunAktif = ArsipDokumen::tahunAktif();

        return [
            Stat::make('Total Dokumen', Document::where('year', $tahunAktif)->count())
                ->description('Tahun '.$tahunAktif)
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Proyek Aktif', Project::where('year', $tahunAktif)->count())
                ->description('Sedang berjalan')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),
            Stat::make('Instansi', Organization::count())
                ->description('Keseluruhan Instansi')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary'),
            // Stat::make('Dokumen Bulan Ini',
            // Document::where('year', $tahunAktif)
            //         ->whereMonth('created_at', now()->month)
            //         ->count()
            //     )
            //     ->description('Bulan ' . now()->format('F'))
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color('info'),

            // Stat::make('Total Rincian', Rincian::where('is_active', true)->count())
            //     ->description('Rincian aktif')
            //     ->descriptionIcon('heroicon-m-queue-list')
            //     ->color('primary'),
            Stat::make('Total Proyek', Project::where('year', $tahunAktif)->count())
                ->description('di Tahun '.$tahunAktif)
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary'),
        ];
    }
}
