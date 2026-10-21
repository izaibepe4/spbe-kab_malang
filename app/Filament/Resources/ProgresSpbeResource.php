<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgresSpbeResource\Pages;
use App\Models\ProgresSpbe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgresSpbeResource extends Resource
{
    protected static ?string $model = ProgresSpbe::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Progres SPBE';

    protected static ?string $navigationLabel = 'Progres';

    public static function getNavigationGroup(): ?string
    {
        return 'Progres SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Progres';
    }

    public static function getModelLabel(): string
    {
        return 'Progres';
    }

    public static function getPluralLabel(): string
    {
        return 'Progres';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('gambar')
                    ->label('Gambar Thumbnail')
                    ->directory('progres')
                    ->disk('public')
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(5120) // 5MB
                    ->imagePreviewHeight('200')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Gambar thumbnail yang akan ditampilkan di halaman beranda'),

                Forms\Components\FileUpload::make('file_pdf_path')
                    ->label('File PDF')
                    ->directory('progres')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(20480) // 20MB
                    ->enableOpen()
                    ->enableDownload()
                    ->preserveFilenames()
                    ->columnSpanFull()
                    ->helperText('File PDF untuk detail progres (opsional)'),

                Forms\Components\TextInput::make('nilai')
                    ->label('Nilai')
                    ->numeric()
                    ->step(0.01)
                    ->placeholder('3.75')
                    ->columnSpan(1)
                    ->helperText('Nilai progres dalam bentuk desimal (contoh: 85.50, 90.25)'),

                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required()
                    ->placeholder('Contoh: 2024')
                    ->columnSpan(1)
                    ->helperText('Tahun capaian indeks SPBE.'), ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'), Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgresSpbes::route('/'),
            'create' => Pages\CreateProgresSpbe::route('/create'),
            'edit' => Pages\EditProgresSpbe::route('/{record}/edit'),
        ];
    }
}
