<?php

namespace App\Filament\Widgets;

use App\Exports\LayananExport;
use App\Models\Layanan;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Maatwebsite\Excel\Facades\Excel;

class LayananTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    // Setting tinggi widget (dalam px)
    protected static ?int $height = 700;

    public function getHeading(): string
    {
        return 'Data Layanan Tahun '.date('Y');
    }

    public static function getHeight(): int
    {
        return static::$height ?? 700;
    }

    public static function setHeight(int $height): void
    {
        static::$height = $height;
    }

    protected function getExtraWidgetAttributes(): array
    {
        return [
            'x-data' => '{ height: '.static::getHeight().' }',
            'style' => 'max-height: '.static::getHeight().'px; overflow: hidden;',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Layanan::query()
                    ->with('user')
                    ->where('tahun', date('Y'))
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('unit_pelaksana')
                    ->label('Unit Pelaksana')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('bidang_bagian')
                    ->label('Bidang/Bagian')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status_layanan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'berjalan' => 'success',
                        'direncanakan' => 'warning',
                        'dihentikan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('didukung_aplikasi')
                    ->label('Aplikasi')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('target_pengguna')
                    ->label('Target')
                    ->limit(20)
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->description('Data layanan tahun '.date('Y'))
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $layanans = Layanan::with('user')
                            ->where('tahun', date('Y'))
                            ->latest()
                            ->get();

                        $filename = 'data_layanan_'.date('Y').'_'.date('Y-m-d_His').'.xlsx';

                        return Excel::download(new LayananExport($layanans), $filename);
                    }),
            ])
            ->defaultPaginationPageOption(10);
    }
}
