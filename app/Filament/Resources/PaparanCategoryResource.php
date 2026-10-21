<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaparanCategoryResource\Pages;
use App\Models\PaparanCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaparanCategoryResource extends Resource
{
    protected static ?string $model = PaparanCategory::class;

    // Slug & Navigasi
    protected static ?string $slug = 'paparan-categories';

    public static function getNavigationGroup(): ?string
    {
        return 'Paparan SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kategori Paparan';
    }

    public static function getModelLabel(): string
    {
        return 'Kategori Paparan';
    }

    public static function getPluralLabel(): string
    {
        return 'Kategori Paparan';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-presentation-chart-line';
    }

    // FORM (Create / Edit)
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')
                ->label('Nama Kategori')
                ->placeholder('Capaian Tahun 2023')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
        ]);
    }

    // TABLE (List)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Dokumen')
                    ->counts('items')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaparanCategories::route('/'),
            'create' => Pages\CreatePaparanCategory::route('/create'),
            'edit' => Pages\EditPaparanCategory::route('/{record}/edit'),
        ];
    }
}
