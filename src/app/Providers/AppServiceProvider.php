<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'staging' || config('app.env') === 'production') {
            \URL::forceRootUrl(config('app.url'));
        }

        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        Paginator::useBootstrap();
    }
}
