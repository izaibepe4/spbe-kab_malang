<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserOpdResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserOpdResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen User';
    }

    public static function getNavigationLabel(): string
    {
        return 'User OPD';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'User OPD';
    }

    public static function getPluralLabel(): string
    {
        return 'User OPD';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUserOpd)
                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->autocomplete('new-password'),

                TextInput::make('nama_opd')
                    ->label('Nama OPD')
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_approved')
                    ->label('Disetujui')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_opd')
                    ->label('Nama OPD')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('is_approved')
                    ->label('Disetujui')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)
                            ->timezone('Asia/Jakarta')
                            ->format('d M Y H:i');
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Status Persetujuan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Disetujui')
                    ->falseLabel('Belum Disetujui')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_approved', true),
                        false: fn (Builder $query) => $query->where('is_approved', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui User OPD')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui user ini?')
                    ->action(function (User $record) {
                        $record->is_approved = true;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil disetujui')
                            ->body('User '.$record->name.' sekarang dapat login.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (User $record) => ! $record->is_approved),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak User OPD')
                    ->modalDescription('Apakah Anda yakin ingin menolak user ini?')
                    ->action(function (User $record) {
                        $record->update(['is_approved' => false]);
                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil ditolak')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (User $record) => $record->is_approved),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUserOpds::route('/'),
            'create' => Pages\CreateUserOpd::route('/create'),
            'edit' => Pages\EditUserOpd::route('/{record}/edit'),
        ];
    }
}
