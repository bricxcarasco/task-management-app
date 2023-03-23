<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // @phpstan-ignore-next-line
        foreach (glob(app_path().'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
    }
}
