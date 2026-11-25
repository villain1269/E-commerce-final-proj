<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\StarterKitService;
use App\Services\XenditService;
use App\View\Composers\CartCountComposer;
use App\View\Composers\CategoriesComposer;
use App\View\Composers\StarterKitComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, fn () => new CartService());
        $this->app->singleton(StarterKitService::class, fn () => new StarterKitService());
        $this->app->singleton(XenditService::class, function ($app) {
            return new XenditService(env('XENDIT_SECRET_KEY', ''));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
	URL::forceRootUrl(config('app.url'));
	URL::forceScheme('https');
        View::composer('partials.header', CartCountComposer::class);
        View::composer('partials.header', CategoriesComposer::class);
        View::composer('partials.footer', CategoriesComposer::class);
        View::composer('pages.starter-kit', StarterKitComposer::class);

        // Share cart count with every view that needs it
        View::composer(['partials.header', 'layouts.app', 'layouts.checkout'], function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
