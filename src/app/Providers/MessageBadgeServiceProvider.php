<?php

namespace App\Providers;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MessageBadgeServiceProvider extends ServiceProvider
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
            $messagesCount = ChatMessage::getAllUnreadMessagesCount();

            $view->with(compact('messagesCount'));
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
