<?php

namespace App\Filament\Widgets;

use App\Models\ProgresSpbe;
use Filament\Widgets\ChartWidget;

class SpbeIndexChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Indeks SPBE';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    // Setting tinggi widget (dalam px) - disesuaikan agar sesuai dengan tinggi agenda + tabel
    protected static ?int $height = 700;

    public function getDescription(): ?string
    {
        return 'Grafik perkembangan Indeks SPBE Kabupaten Malang';
    }

    protected function getData(): array
    {
        $dataset = $this->buildChartData();

        return [
            'labels' => $dataset['labels'],
            'datasets' => [
                [
                    'label' => 'Indeks SPBE',
                    'data' => $dataset['values'],
                    'borderColor' => '#1C2B78',
                    'backgroundColor' => 'rgba(28, 43, 120, 0.2)',
                    'tension' => 0.4,
                    'borderWidth' => 3,
                    'fill' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 5,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getContentHeight(): ?string
    {
        return static::$height.'px';
    }

    public static function getHeight(): int
    {
        return static::$height ?? 700;
    }

    public static function setHeight(int $height): void
    {
        static::$height = $height;
    }

    protected function buildChartData(): array
    {
        $progresData = ProgresSpbe::query()
            ->orderBy('tahun')
            ->orderByDesc('created_at')
            ->get(['nilai', 'tahun', 'created_at']);

        $years = $progresData
            ->map(function ($item) {
                $year = $item->tahun ?? optional($item->created_at)->format('Y');

                return $year ? (string) $year : 'Tanpa Tahun';
            })
            ->toArray();

        if (empty($years)) {
            $years = ['2021', '2022', '2023', '2024', '2025'];
        }

        $values = $progresData
            ->map(fn ($progres) => $progres && $progres->nilai ? (float) $progres->nilai : null)
            ->toArray();

        if (empty($values)) {
            $values = array_fill(0, count($years), null);
        }

        return [
            'labels' => $years,
            'values' => $values,
        ];
    }
}
