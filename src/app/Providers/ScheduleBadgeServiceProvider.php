<?php

namespace App\Providers;

use App\Models\ScheduleNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Session;

class ScheduleBadgeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Set composer views
        $views = [
            'layouts/components/main_header',
            'layouts/components/main_footer',
        ];

        View::composer($views, function ($view) {
            $service = json_decode(Session::get('ServiceSelected'));
            $invitationsCount = ScheduleNotification::notifications($service)->count();

            $view->with(compact('invitationsCount'));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
