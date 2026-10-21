<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LayananTableWidget;
use App\Filament\Widgets\ProgresStatsWidget;
use App\Filament\Widgets\SpbeIndexChartWidget;
use App\Filament\Widgets\UserOpdTableWidget;
use App\Filament\Widgets\VisitorStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Navigation\NavigationGroup;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $logoPath = public_path('assets/img/logo-pemkab.png');
        $faviconVersion = File::exists($logoPath)
            ? File::lastModified($logoPath)
            : config('app.version', '1.0');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset(\App\Filament\Pages\Auth\RequestPasswordReset::class)
            ->authGuard('admin')
            ->favicon(url('/favicon.png') . '?v=' . $faviconVersion)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationGroups([
                NavigationGroup::make('Data Layanan')
                    ->collapsed(false),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ProgresStatsWidget::class,
                SpbeIndexChartWidget::class,
                LayananTableWidget::class,
                UserOpdTableWidget::class,
                VisitorStatsWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\RefreshCsrfToken::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::STYLES_AFTER,
            fn(): string => '<style>
                .fi-user-opd-table-widget-scroll .fi-ta-content > div {
                    max-height: 500px;
                    overflow-y: auto;
                }
                .fi-user-opd-table-widget-scroll .fi-ta-table-wrapper {
                    max-height: 500px;
                    overflow-y: auto;
                }
                .fi-user-opd-table-widget-scroll .fi-ta-content {
                    max-height: 500px;
                    overflow-y: auto;
                }
                .fi-user-opd-table-widget-scroll .fi-ta-table thead {
                    position: sticky;
                    top: 0;
                    background-color: white;
                    z-index: 10;
                }
                .dark .fi-user-opd-table-widget-scroll .fi-ta-table thead {
                    background-color: rgb(17, 24, 39);
                }
            </style>'
        );
    }
}
