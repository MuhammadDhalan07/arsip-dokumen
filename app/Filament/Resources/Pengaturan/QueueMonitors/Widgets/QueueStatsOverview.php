<?php

namespace App\Filament\Resources\Pengaturan\QueueMonitors\Widgets;

use App\Models\QueueMonitor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class QueueStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $aggregationColumns = [
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(finished_at - started_at) as total_time_elapsed'),
            DB::raw('AVG(finished_at - started_at) as average_time_elapsed'),
        ];

        $aggregatedInfo = QueueMonitor::query()
            ->select($aggregationColumns)
            ->first();

        $queueSize = collect(config('filament-jobs-monitor.queues') ?? ['default'])
            ->map(fn (string $queue): int => Queue::size($queue))
            ->sum();

        return [
            Stat::make('Total Jobs Executed', $aggregatedInfo->count ?? 0),
            Stat::make('Pending Jobs', $queueSize),
            Stat::make('Total Execution Time', ($aggregatedInfo->total_time_elapsed ?? 0).'s'),
            Stat::make('Average Execution Time', ceil((float) $aggregatedInfo->average_time_elapsed).'s' ?? 0),
        ];
    }
}
