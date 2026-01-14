<?php

namespace App\Filament\Widgets;

use App\ArsipDokumen;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tahunAktif = ArsipDokumen::tahunAktif();
        $today = today();
        $twoWeeksFromNow = today()->addDays(14);

        $projects = Project::with('documents')
            ->where('year', $tahunAktif)
            ->get();

        $totalProyek = $projects->count();

        $proyekAktif = $projects->filter(function ($project) use ($today) {
            return $project->end_date === null || $project->end_date->gte($today);
        })->count();

        $proyekMendekatiDeadline = $projects->filter(function ($project) use ($today, $twoWeeksFromNow) {
            return $project->end_date !== null
                && $project->end_date->gte($today)
                && $project->end_date->lte($twoWeeksFromNow);
        })->count();

        $proyekTerlambat = $projects->filter(function ($project) use ($today) {
            return $project->end_date !== null && $project->end_date->lt($today) && ! $project->is_complete;
        })->count();

        $proyekSelesai = $projects->filter(function ($project) use ($today) {
            return $project->end_date !== null && $project->end_date->lt($today) && $project->is_complete;
        })->count();

        return [
            // Stat::make('Total Proyek', $totalProyek)
            //     ->description('di Tahun '.$tahunAktif)
            //     ->descriptionIcon('heroicon-m-folder-open')
            //     ->color('primary'),

            Stat::make('Proyek Aktif', $proyekAktif)
                ->description('Sedang berjalan')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),

            Stat::make('Mendekati Deadline', $proyekMendekatiDeadline)
                ->description('Kurang dari 2 minggu')
                ->descriptionIcon('heroicon-m-clock')
                ->color($proyekMendekatiDeadline > 0 ? 'warning' : 'gray'),

            Stat::make('Terlambat', $proyekTerlambat)
                ->description('Lewat deadline')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($proyekTerlambat > 0 ? 'danger' : 'gray'),

            Stat::make('Selesai', $proyekSelesai)
                ->description('Semua dokumen complete')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
