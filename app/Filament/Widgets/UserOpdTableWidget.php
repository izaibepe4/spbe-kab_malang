<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserOpdTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    // Setting tinggi widget (dalam px) - diselaraskan dengan SpbeIndexChartWidget
    protected static ?int $height = 700;

    public function getHeading(): string
    {
        return 'User OPD - Persetujuan Pendaftaran';
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
            'class' => 'fi-user-opd-table-widget-scroll',
            'x-data' => '{ height: '.static::getHeight().' }',
            'style' => 'min-height: '.static::getHeight().'px;',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(50) // Limit untuk performa, tapi tetap bisa scroll
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_opd')
                    ->label('Nama OPD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->description('Daftar user OPD yang menunggu persetujuan')
            ->searchable(false)
            ->paginated(false)
            ->headerActions([
                Action::make('approveAll')
                    ->label('Setujui Semua')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Semua User')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui semua user yang belum disetujui?')
                    ->action(function () {
                        $count = User::where('is_approved', false)
                            ->update(['is_approved' => true]);

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->body($count.' user berhasil disetujui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => User::where('is_approved', false)->exists()),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui User')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui user ini?')
                    ->action(function (User $record) {
                        $record->is_approved = true;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil disetujui')
                            ->body('User '.$record->name.' sekarang dapat login.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (User $record) => ! $record->is_approved),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak User')
                    ->modalDescription('Apakah Anda yakin ingin menolak user ini?')
                    ->action(function (User $record) {
                        $record->is_approved = false;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil ditolak')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (User $record) => $record->is_approved),
            ]);
    }
}
