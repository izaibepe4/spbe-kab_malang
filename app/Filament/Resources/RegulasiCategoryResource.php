<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegulasiCategoryResource\Pages;
use App\Models\RegulasiCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class RegulasiCategoryResource extends Resource
{
    protected static ?string $model = RegulasiCategory::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Regulasi SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kategori Regulasi';
    }

    public static function getModelLabel(): string
    {

        return 'Kategori Regulasi';
    }

    public static function getPluralLabel(): string
    {

        return 'Kategori Regulasi';
    }

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')
                ->label('Nama Kategori Regulasi')
                ->required()
                ->maxLength(255), ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Kategori Regulasi')
                    ->sortable()
                    ->searchable(), ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegulasiCategories::route('/'),
            'create' => Pages\CreateRegulasiCategory::route('/create'),
            'edit' => Pages\EditRegulasiCategory::route('/{record}/edit'),
        ];
    }
}
