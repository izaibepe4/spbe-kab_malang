<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengetahuanItemResource\Pages;
use App\Models\PengetahuanItem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class PengetahuanItemResource extends Resource
{
    protected static ?string $model = PengetahuanItem::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Pengetahuan SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Isi Pengetahuan SPBE';
    }

    public static function getModelLabel(): string
    {

        return 'Isi Pengetahuan SPBE';
    }

    public static function getPluralLabel(): string
    {

        return 'Isi Pengetahuan SPBE';
    }

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('category_id')
                ->label('Kategori')
                ->relationship('category', 'nama_kategori')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('judul_item')
                ->label('Judul Poin / Dokumen')
                ->required()
                ->maxLength(255),

            Forms\Components\FileUpload::make('file_pdf_path')
                ->label('Lampiran PDF')
                ->directory('pengetahuan')
                ->disk('public')
                ->acceptedFileTypes(['application/pdf'])
                ->enableOpen()
                ->enableDownload()
                ->preserveFilenames()
                ->helperText('Kosongkan jika hanya teks tanpa file.'), ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.nama_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('judul_item')
                    ->label('Judul')
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
            'index' => Pages\ListPengetahuanItems::route('/'),
            'create' => Pages\CreatePengetahuanItem::route('/create'),
            'edit' => Pages\EditPengetahuanItem::route('/{record}/edit'),
        ];
    }
}
