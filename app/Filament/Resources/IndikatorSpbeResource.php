<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndikatorSpbeResource\Pages;
use App\Models\IndikatorSpbe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IndikatorSpbeResource extends Resource
{
    protected static ?string $model = IndikatorSpbe::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Progres SPBE';

    protected static ?string $navigationLabel = 'Indikator SPBE';

    public static function getNavigationGroup(): ?string
    {
        return 'Progres SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Indikator SPBE';
    }

    public static function getModelLabel(): string
    {
        return 'Indikator SPBE';
    }

    public static function getPluralLabel(): string
    {
        return 'Indikator SPBE';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kategori')
                    ->label('Kategori Indikator')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Kebijakan SPBE, Tata Kelola SPBE, dll')
                    ->helperText('Masukkan nama kategori indikator'),
                Forms\Components\TextInput::make('nilai')
                    ->label('Nilai')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Masukkan nilai indikator (angka bulat)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kategori');
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
            'index' => Pages\ListIndikatorSpbes::route('/'),
            'create' => Pages\CreateIndikatorSpbe::route('/create'),
            'edit' => Pages\EditIndikatorSpbe::route('/{record}/edit'),
        ];
    }
}
