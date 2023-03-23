<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts/components/main_header', function ($view) {
            // Get notifications list
            $notifications = Notification::allNotifications()
                ->limit(config('bphero.notification_dropdown_limit'))
                ->get();

            // Get total unread count
            $unreadNotifCount = Notification::unreadNotifications()
                ->count();

            $view->with(compact(
                'notifications',
                'unreadNotifCount',
            ));
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
