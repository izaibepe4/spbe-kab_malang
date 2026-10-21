<?php

namespace App\Filament\Resources;

use App\Exports\LayananExport;
use App\Filament\Resources\LayananResource\Pages;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Semua Data Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?string $navigationGroup = 'Data Layanan';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas OPD & PIC')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('OPD')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $user = \App\Models\User::find($state);
                                    if ($user) {
                                        $set('unit_pelaksana', $user->nama_opd);
                                        $set('nama_pic', $user->name);
                                        $set('email_pic', $user->email);
                                        $set('hp_telp_pic', $user->no_hp);
                                    }
                                } else {
                                    $set('unit_pelaksana', '');
                                    $set('nama_pic', '');
                                    $set('email_pic', '');
                                    $set('hp_telp_pic', '');
                                }
                            }),
                        Forms\Components\TextInput::make('tahun')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->default(date('Y')),
                        Forms\Components\TextInput::make('unit_pelaksana')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_pic')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_pic')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hp_telp_pic')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\TextInput::make('bidang_bagian')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status_layanan')
                            ->required()
                            ->options([
                                'berjalan' => 'Berjalan',
                                'direncanakan' => 'Direncanakan',
                                'dihentikan' => 'Dihentikan',
                            ]),
                        Forms\Components\TextInput::make('nama_layanan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi_layanan')
                            ->rows(4),
                        Forms\Components\Toggle::make('didukung_aplikasi')
                            ->label('Didukung oleh aplikasi?'),
                        Forms\Components\Select::make('target_pengguna')
                            ->required()
                            ->options([
                                'Publik/Masyarakat' => 'Publik/Masyarakat',
                                'Internal Pemerintahan' => 'Internal Pemerintahan',
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Data & Keterkaitan')
                    ->schema([
                        Forms\Components\Textarea::make('kementerian_lembaga_terkait')
                            ->rows(2),
                        Forms\Components\Textarea::make('supplier_data')
                            ->rows(2),
                        Forms\Components\Textarea::make('data_masuk')
                            ->rows(2),
                        Forms\Components\Textarea::make('data_hasil')
                            ->rows(2),
                        Forms\Components\Select::make('keterbukaan_data')
                            ->options([
                                'terbuka' => 'Terbuka',
                                'terbatas' => 'Terbatas',
                                'tertutup' => 'Tertutup',
                            ]),
                        Forms\Components\TextInput::make('jenis_data')
                            ->maxLength(255),
                        Forms\Components\Select::make('frekuensi_pembaruan')
                            ->options([
                                'Realtime' => 'Realtime',
                                'Harian' => 'Harian',
                                'Mingguan' => 'Mingguan',
                                'Bulanan' => 'Bulanan',
                                'Tiga Bulanan' => 'Tiga Bulanan',
                                'Enam Bulanan' => 'Enam Bulanan',
                                'Tahunan' => 'Tahunan',
                                'Dua Tahunan' => 'Dua Tahunan',
                                'Tiga Tahunan' => 'Tiga Tahunan',
                                'Lima Tahunan' => 'Lima Tahunan',
                            ]),
                        Forms\Components\Toggle::make('terhubung_sistem_lain')
                            ->label('Bisa terhubung ke sistem lain?'),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Aplikasi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_aplikasi')
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis_aplikasi')
                            ->options([
                                'Web' => 'Web',
                                'Mobile' => 'Mobile',
                                'Desktop' => 'Desktop',
                                'Others' => 'Lainnya',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('jenis_aplikasi_lainnya', $state === 'Others' ? null : '')),
                        Forms\Components\TextInput::make('jenis_aplikasi_lainnya')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => $get('jenis_aplikasi') === 'Others'),
                        Forms\Components\Textarea::make('url_akses')
                            ->rows(2),
                        Forms\Components\Textarea::make('hasil_output_aplikasi')
                            ->rows(3),
                        Forms\Components\TextInput::make('lokasi_penyimpanan')
                            ->maxLength(255),
                    ])->columns(2)
                    ->visible(fn(Forms\Get $get) => $get('didukung_aplikasi')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('OPD')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_pelaksana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidang_bagian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_layanan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'berjalan' => 'success',
                        'direncanakan' => 'warning',
                        'dihentikan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('didukung_aplikasi')
                    ->label('Aplikasi')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Ya' : 'Tidak')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('target_pengguna'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(function () {
                        return Layanan::query()
                            ->distinct()
                            ->pluck('tahun', 'tahun')
                            ->sortKeysDesc()
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('OPD')
                    ->relationship('user', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status_layanan')
                    ->options([
                        'berjalan' => 'Berjalan',
                        'direncanakan' => 'Direncanakan',
                        'dihentikan' => 'Dihentikan',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Export Data Layanan')
                    ->modalDescription('Data akan diekspor ke format Excel. Proses ini mungkin memakan waktu beberapa saat.')
                    ->action(function () {
                        // Rate limiting check
                        $key = 'export_layanan_' . auth()->id();
                        $maxAttempts = 10;
                        $decayMinutes = 1;

                        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
                            throw new \Illuminate\Http\Exceptions\ThrottleRequestsException(
                                "Terlalu banyak request export. Silakan coba lagi dalam {$seconds} detik."
                            );
                        }

                        \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

                        // Log export activity
                        $activityService = app(\App\Services\ActivityLogService::class);
                        $activityService->logExport('Layanan', [], auth()->user());

                        $layanans = Layanan::with('user')
                            ->latest()
                            ->get();

                        $filename = 'data_layanan_' . date('Y-m-d_His') . '.xlsx';

                        return Excel::download(new LayananExport($layanans), $filename);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tahun', 'desc');
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
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }
}
