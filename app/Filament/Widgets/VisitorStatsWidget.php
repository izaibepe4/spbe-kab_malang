<?php

namespace App\Filament\Widgets;

use App\Services\VisitorCounterService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $visitorService = app(VisitorCounterService::class);

        try {
            $totalVisitors = $visitorService->getTotalVisitors();
            $visitorsByGender = $visitorService->getVisitorsByGender();
            $todayVisitors = $visitorService->getTodayVisitorsByGender();

            return [
                Stat::make('Total Pengunjung', number_format($totalVisitors))
                    ->description('Semua pengunjung')
                    ->color('primary'),

                Stat::make('Pengunjung Pria', number_format($visitorsByGender['male']))
                    ->description('Hari ini: ' . number_format($todayVisitors['male']))
                    ->color('blue'),

                Stat::make('Pengunjung Wanita', number_format($visitorsByGender['female']))
                    ->description('Hari ini: ' . number_format($todayVisitors['female']))
                    ->color('pink'),
            ];
        } catch (\Exception $e) {
            // Fallback jika ada error
            return [
                Stat::make('Total Pengunjung', '0')
                    ->description('Data tidak tersedia')
                    ->color('gray'),

                Stat::make('Pengunjung Pria', '0')
                    ->description('Data tidak tersedia')
                    ->color('gray'),

                Stat::make('Pengunjung Wanita', '0')
                    ->description('Data tidak tersedia')
                    ->color('gray'),
            ];
        }
    }
}
