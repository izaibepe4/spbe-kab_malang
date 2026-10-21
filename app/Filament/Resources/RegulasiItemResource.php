<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegulasiItemResource\Pages;
use App\Models\RegulasiItem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class RegulasiItemResource extends Resource
{
    protected static ?string $model = RegulasiItem::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Regulasi SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Isi Regulasi';
    }

    public static function getModelLabel(): string
    {

        return 'Isi Regulasi';
    }

    public static function getPluralLabel(): string
    {

        return 'Isi Regulasi';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('category_id')
                ->label('Kategori Regulasi')
                ->relationship('category', 'nama_kategori')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('judul_regulasi')
                ->label('Judul Regulasi')
                ->required()
                ->maxLength(255),

            Forms\Components\FileUpload::make('file_pdf_path')
                ->label('File Regulasi (PDF)')
                ->directory('regulasi')
                ->disk('public')
                ->acceptedFileTypes(['application/pdf'])
                ->required()
                ->enableOpen()
                ->enableDownload()
                ->preserveFilenames(), ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.nama_kategori')
                    ->label('Kategori Regulasi')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('judul_regulasi')
                    ->label('Judul Regulasi')
                    ->limit(60)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('file_pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'), ])
            ->defaultSort('category_id', 'asc')

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
            'index' => Pages\ListRegulasiItems::route('/'),
            'create' => Pages\CreateRegulasiItem::route('/create'),
            'edit' => Pages\EditRegulasiItem::route('/{record}/edit'),
        ];
    }
}
