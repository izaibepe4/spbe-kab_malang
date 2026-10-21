<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaparanItemResource\Pages;
use App\Models\PaparanItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaparanItemResource extends Resource
{
    protected static ?string $model = PaparanItem::class;

    // Supaya route & URL konsisten
    protected static ?string $slug = 'paparan-items';

    public static function getNavigationGroup(): ?string
    {
        return 'Paparan SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Isi Paparan';
    }

    public static function getModelLabel(): string
    {
        return 'Isi Paparan';
    }

    public static function getPluralLabel(): string
    {
        return 'Isi Paparan';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-chart-bar';
    }

    // ============================
    // FORM (Create / Edit)
    // ============================
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Pilih Kategori (dengan inline create)
            Forms\Components\Select::make('category_id')
                ->label('Kategori')
                ->relationship('category', 'nama_kategori')
                ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama_kategori')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(255), ]),

            Forms\Components\TextInput::make('judul_item')
                ->label('Judul Dokumen')
                ->placeholder('Paparan Capaian 2023')
                ->required()
                ->maxLength(255),

            // Pilihan tipe dokumen (reactive)
            Forms\Components\Radio::make('tipe_embed')
                ->label('Tipe Dokumen')
                ->options([
                    'slides' => 'Google Slides / Drive (URL embed)',
                    'pdf' => 'PDF Upload',
                ])
                ->default('slides')
                ->inline()
                ->live()        // << penting agar field di bawahnya bereaksi
                ->required(),

            // URL Embed untuk Slides/Drive
            Forms\Components\TextInput::make('embed_url')
                ->label('URL Embed (Slides/Drive)')
                ->placeholder('https://docs.google.com/presentation/d/ID/embed')
                ->helperText('Gunakan URL /embed atau /preview dari Google Slides/Drive.')
                ->visible(fn (Get $get) => $get('tipe_embed') === 'slides')
                ->columnSpanFull(),

            // Upload PDF (hanya tampil jika tipe = pdf)
            Forms\Components\FileUpload::make('file_pdf_path')
                ->label('File PDF')
                ->disk('public')                 // disimpan di storage/app/public
                ->directory('paparan')           // subfolder: paparan
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(20480)                 // 20 MB
                ->enableOpen()
                ->enableDownload()
                ->preserveFilenames()
                ->required(fn (Get $get) => $get('tipe_embed') === 'pdf')
                ->visible(fn (Get $get) => $get('tipe_embed') === 'pdf')
                ->columnSpanFull(), ])->columns(2);
    }

    // ============================
    // TABLE (List)
    // ============================
    public static function table(Table $table): Table
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
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tipe_embed')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'slides',
                        'warning' => 'pdf',
                    ])
                    ->sortable(), ])
            ->defaultSort('category_id')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'nama_kategori'),

                Tables\Filters\SelectFilter::make('tipe_embed')
                    ->label('Tipe')
                    ->options([
                        'slides' => 'Slides/Drive',
                        'pdf' => 'PDF',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn ($record) => $record->tipe_embed === 'slides'
                            ? $record->embed_url
                            : ($record->file_pdf_path
                                ? asset('storage/'.$record->file_pdf_path)
                                : '#'
                            )
                    )
                    ->openUrlInNewTab()
                    ->visible(
                        fn ($record) => ($record->tipe_embed === 'slides' && filled($record->embed_url)) ||
                            ($record->tipe_embed === 'pdf' && filled($record->file_pdf_path))
                    ),

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
            'index' => Pages\ListPaparanItems::route('/'),
            'create' => Pages\CreatePaparanItem::route('/create'),
            'edit' => Pages\EditPaparanItem::route('/{record}/edit'),
        ];
    }
}
