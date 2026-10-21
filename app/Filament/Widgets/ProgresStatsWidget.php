<?php

namespace App\Filament\Widgets;

use App\Models\ProgresSpbe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProgresStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Ambil SEMUA data tahun yang aktif untuk membuat map lengkap
        // Ini diperlukan untuk mengecek data tahun sebelumnya
        $allProgresData = ProgresSpbe::query()
            ->whereNotNull('tahun')
            ->whereNotNull('nilai')
            ->get()
            ->unique('tahun');

        // Buat map tahun ke nilai dari SEMUA data (untuk cek tahun sebelumnya)
        $yearToNilai = [];
        foreach ($allProgresData as $progres) {
            $yearToNilai[$progres->tahun] = (float) $progres->nilai;
        }

        // Ambil 4 tahun terbaru untuk ditampilkan di widget
        $progresData = $allProgresData
            ->sortByDesc('tahun')
            ->take(4)
            ->sortBy('tahun')
            ->values();

        $stats = [];

        foreach ($progresData as $index => $progres) {
            $year = $progres->tahun;
            $nilai = (float) $progres->nilai;
            $formattedNilai = number_format($nilai, 2);

            // Cek apakah ada data tahun sebelumnya (tahun - 1)
            $previousYear = $year - 1;
            $previousNilai = $yearToNilai[$previousYear] ?? null;

            // Hitung persentase perubahan dari tahun sebelumnya
            $description = '';
            $icon = 'heroicon-o-chart-bar';

            if ($previousNilai !== null && $previousNilai > 0) {
                // Ada data tahun sebelumnya, hitung persentase perubahan
                $change = (($nilai - $previousNilai) / $previousNilai) * 100;
                $changeFormatted = number_format(abs($change), 1);

                if ($change > 0) {
                    $description = "meningkat {$changeFormatted}%";
                    $icon = 'heroicon-o-arrow-trending-up';
                } elseif ($change < 0) {
                    $description = "menurun {$changeFormatted}%";
                    $icon = 'heroicon-o-arrow-trending-down';
                } else {
                    $description = 'tidak berubah';
                }
            } else {
                // Tidak ada data tahun sebelumnya, gunakan default
                $description = "tahun {$year}";
            }

            // Tentukan warna berdasarkan urutan
            $colors = ['gray', 'warning', 'info', 'success'];
            $color = $colors[$index] ?? 'gray';

            $stats[] = Stat::make("Indeks SPBE {$year}", $formattedNilai)
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart([$nilai]);
        }

        return $stats;
    }
}
