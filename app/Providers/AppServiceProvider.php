<?php

namespace App\Providers;

use App\Models\AgendaImage;
use App\Models\Layanan;
use App\Models\PaparanItem;
use App\Models\PengetahuanCategory;
use App\Models\PengetahuanItem;
use App\Models\ProgresSpbe;
use App\Models\RegulasiItem;
use App\Models\SopItem;
use App\Models\User;
use App\Policies\AgendaImagePolicy;
use App\Policies\LayananPolicy;
use App\Policies\PaparanItemPolicy;
use App\Policies\PengetahuanItemPolicy;
use App\Policies\ProgresSpbePolicy;
use App\Policies\RegulasiItemPolicy;
use App\Policies\SopItemPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(Layanan::class, LayananPolicy::class);
        Gate::policy(ProgresSpbe::class, ProgresSpbePolicy::class);
        Gate::policy(RegulasiItem::class, RegulasiItemPolicy::class);
        Gate::policy(PengetahuanItem::class, PengetahuanItemPolicy::class);
        Gate::policy(PaparanItem::class, PaparanItemPolicy::class);
        Gate::policy(SopItem::class, SopItemPolicy::class);
        Gate::policy(AgendaImage::class, AgendaImagePolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // CDN Configuration - Set asset URL if CDN is configured
        if (env('CDN_URL')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(env('CDN_URL'));
        }

        // Share pengetahuan categories to footer
        View::composer('partials._footer', function ($view) {
            $pengetahuanCategories = PengetahuanCategory::query()
                ->with(['items' => fn ($q) => $q,
                ])
                ->get(['id', 'nama_kategori']);

            $view->with('pengetahuanCategories', $pengetahuanCategories);
        });
    }
}
