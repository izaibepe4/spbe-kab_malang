<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengetahuanCategoryResource\Pages;
use App\Models\PengetahuanCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengetahuanCategoryResource extends Resource
{
    protected static ?string $model = PengetahuanCategory::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Pengetahuan SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kategori Pengetahuan SPBE';
    }

    public static function getModelLabel(): string
    {

        return 'Kategori Pengetahuan SPBE';
    }

    public static function getPluralLabel(): string
    {

        return 'Kategori Pengetahuan SPBE';
    }

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    // FORM (Create/Edit)
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')
                ->label('Nama Kategori')
                ->required()
                ->maxLength(255), ]);
    }

    // TABLE (List)
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(), ])

            // tombol Edit / Delete per baris
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])

            // bulk action (hapus massal)
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengetahuanCategories::route('/'),
            'create' => Pages\CreatePengetahuanCategory::route('/create'),
            'edit' => Pages\EditPengetahuanCategory::route('/{record}/edit'),
        ];
    }
}
