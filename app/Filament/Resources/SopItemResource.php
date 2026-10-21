<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SopItemResource\Pages;
use App\Models\SopItem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class SopItemResource extends Resource
{
    protected static ?string $model = SopItem::class;

    public static function getNavigationGroup(): ?string
    {
        return 'SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'SOP SPBE';
    }

    public static function getModelLabel(): string
    {

        return 'SOP SPBE';
    }

    public static function getPluralLabel(): string
    {

        return 'SOP SPBE';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Slug untuk identifikasi unik item SOP'),

            Forms\Components\FileUpload::make('file_path')
                ->label('File (Gambar/PDF)')
                ->directory('sop')        // tersimpan di storage/app/public/sop
                ->visibility('public')
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->maxSize(10240)          // 10MB
                ->imagePreviewHeight('200')
                ->required(),

            Forms\Components\Select::make('tipe')
                ->label('Tipe Viewer')
                ->options(['image' => 'Gambar', 'pdf' => 'PDF'])
                ->required()
                ->helperText('Sesuaikan dengan jenis file yang diupload.'),

            Forms\Components\Textarea::make('ringkasan')
                ->label('Ringkasan')
                ->rows(3), ])->columns(2);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ringkasan')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('tipe')->badge(), Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update')->dateTime('d M Y H:i'),
            ])
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
            'index' => Pages\ListSopItems::route('/'),
            'create' => Pages\CreateSopItem::route('/create'),
            'edit' => Pages\EditSopItem::route('/{record}/edit'),
        ];
    }
}
