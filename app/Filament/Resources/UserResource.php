<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // Ikon & grouping di sidebar
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Sistem';
    }

    public static function getNavigationLabel(): string
    {
        return 'Manajemen User (Lama)';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide resource lama
    }

    public static function getModelLabel(): string
    {
        return 'User';
    }

    public static function getPluralLabel(): string
    {
        return 'User';
    }

    // =========================
    // FORM (Create / Edit User)
    // =========================
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
                    // saat Create: wajib isi password
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    // hash sebelum simpan
                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                    // kalau Edit dan input password dikosongkan, jangan overwrite password lama
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->autocomplete('new-password'),

                TextInput::make('nama_opd')
                    ->label('Nama OPD')
                    ->maxLength(255),

                Toggle::make('is_approved')
                    ->label('Disetujui')
                    ->default(false),
            ]);
    }

    // =========================
    // TABLE (List Users)
    // =========================
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
                    ->toggleable(),

                BooleanColumn::make('is_approved')
                    ->label('Disetujui')
                    ->toggleable(),

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
                    ->modalHeading('Setujui User')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui user ini?')
                    ->action(function (User $record) {
                        $record->is_approved = true;
                        $record->save();

                        // Log approval action
                        \Log::info('User approved', [
                            'user_id' => $record->id,
                            'user_email' => $record->email,
                            'user_name' => $record->name,
                        ]);

                        // Kirim email notifikasi ke user
                        try {
                            \Mail::to($record->email)->send(new \App\Mail\UserApprovedNotification($record));
                            \Log::info('Approval email sent successfully', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to send approval notification email', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil disetujui')
                            ->body('User '.$record->name.' sekarang dapat login. Email notifikasi telah dikirim.')
                            ->success()
                            ->send();
                    })
                    ->after(function () {
                        // Refresh table setelah approve
                    })
                    ->visible(fn (User $record) => ! $record->is_approved),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak User')
                    ->modalDescription('Apakah Anda yakin ingin menolak user ini?')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Jelaskan alasan penolakan (opsional)')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update(['is_approved' => false]);

                        // Log rejection action
                        \Log::info('User rejected', [
                            'user_id' => $record->id,
                            'user_email' => $record->email,
                            'user_name' => $record->name,
                            'reason' => $data['reason'] ?? null,
                        ]);

                        // Kirim email notifikasi ke user dengan alasan
                        try {
                            \Mail::to($record->email)->send(
                                new \App\Mail\UserRejectedNotification($record, $data['reason'] ?? null)
                            );
                            \Log::info('Rejection email sent successfully', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to send rejection notification email', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('User berhasil ditolak')
                            ->body('Email notifikasi telah dikirim ke user.')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (User $record) => $record->is_approved),

                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus yang dipilih'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Routing halaman Filament-nya
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
