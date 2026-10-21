<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionInfoSpbeResource\Pages;
use App\Models\SectionInfoSpbe;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class SectionInfoSpbeResource extends Resource
{
    protected static ?string $model = SectionInfoSpbe::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Informasi SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Bagian: Definisi & Tujuan';
    }

    public static function getModelLabel(): string
    {
        return 'Bagian: Definisi & Tujuan';
    }

    public static function getPluralLabel(): string
    {
        return 'Bagian: Definisi & Tujuan';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // FORM untuk Create/Edit
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('judul')
                ->label('Judul Bagian')
                ->required()
                ->maxLength(255)
                ->default('Definisi dan Tujuan SPBE'),

            Forms\Components\RichEditor::make('deskripsi_html')
                ->label('Paragraf Penjelasan')
                ->required()
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'h2',
                    'h3',
                    'bulletList',
                    'orderedList',
                    'link',
                    'blockquote',
                ])
                ->helperText('Isi teks paragraf di sisi kiri (penjelasan panjang).')
                ->columnSpanFull(), ])->columns(1);
    }

    // TABLE untuk List
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->sortable()
                    ->searchable(), Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y H:i'),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListSectionInfoSpbes::route('/'),
            'create' => Pages\CreateSectionInfoSpbe::route('/create'),
            'edit' => Pages\EditSectionInfoSpbe::route('/{record}/edit'),
        ];
    }
}
