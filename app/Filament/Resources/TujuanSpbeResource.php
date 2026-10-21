<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TujuanSpbeResource\Pages;
use App\Models\TujuanSpbe;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class TujuanSpbeResource extends Resource
{
    protected static ?string $model = TujuanSpbe::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Informasi SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tujuan SPBE';
    }

    public static function getModelLabel(): string
    {

        return 'Tujuan SPBE';
    }

    public static function getPluralLabel(): string
    {

        return 'Tujuan SPBE';
    }

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    // FORM
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('ikon')
                ->label('Ikon')
                ->options([
                    'heroicon-o-cog' => '⚙ Tata Kelola / Pengelolaan',
                    'heroicon-o-link' => '🔗 Integrasi / Keterpaduan',
                    'heroicon-o-users' => '👥 Pelayanan Publik',
                    'heroicon-o-server' => '🖥 Infrastruktur Digital',
                    'heroicon-o-chart-bar' => '📊 Kinerja / Evaluasi',
                    'heroicon-o-bolt' => '⚡ Efisiensi',
                ])
                ->required()
                ->helperText('Ikon akan tampil di kotak kecil di sebelah judul poin.'),

            Forms\Components\TextInput::make('judul')
                ->label('Judul Poin')
                ->required()
                ->maxLength(255)
                ->helperText('Contoh: "Tata Kelola Pemerintahan Digital"'),

            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi Singkat')
                ->required()
                ->rows(3)
                ->maxLength(1000)
                ->helperText('Contoh: "Mewujudkan pemerintahan yang bersih, efektif, transparan..."'), ])
            ->columns(2);
    }

    // TABLE
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('judul')
                ->label('Judul')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('ikon')
                    ->label('Ikon')
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->color('gray'), ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTujuanSpbes::route('/'),
            'create' => Pages\CreateTujuanSpbe::route('/create'),
            'edit' => Pages\EditTujuanSpbe::route('/{record}/edit'),
        ];
    }
}
