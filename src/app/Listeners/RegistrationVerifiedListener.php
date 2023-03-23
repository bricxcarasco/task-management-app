<?php

namespace App\Listeners;

use App\Events\RegistrationVerifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegistrationVerifiedListener
{
    /**
     * Handle the event.
     *
     * @param  RegistrationVerifiedEvent  $event
     * @return void
     */
    public function handle(RegistrationVerifiedEvent $event)
    {
        $event->user->sendRegistrationVerifiedNotification($event->user);
    }
}
