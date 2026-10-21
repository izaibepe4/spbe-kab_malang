<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaImageResource\Pages;
use App\Models\AgendaImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgendaImageResource extends Resource
{
    protected static ?string $model = AgendaImage::class;

    protected static ?string $slug = 'agenda-images';

    public static function getNavigationGroup(): ?string
    {
        return 'SPBE';
    }

    public static function getNavigationLabel(): string
    {
        return 'Agenda';
    }

    public static function getModelLabel(): string
    {
        return 'Agenda';
    }

    public static function getPluralLabel(): string
    {
        return 'Agenda';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image_path')
                ->label('Gambar Agenda')
                ->disk('public')
                ->directory('agenda') // disimpan di storage/app/public/agenda
                ->image()
                ->imageEditor()       // crop/rotate bila perlu
                ->required()
                ->enableOpen()
                ->enableDownload()
                ->preserveFilenames()
                ->columnSpanFull(), ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->square()
                    ->height(80), ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Buka')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => asset('storage/'.$record->image_path))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListAgendaImages::route('/'),
            'create' => Pages\CreateAgendaImage::route('/create'),
            'edit' => Pages\EditAgendaImage::route('/{record}/edit'),
        ];
    }
}
