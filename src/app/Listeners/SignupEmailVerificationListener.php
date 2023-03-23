<?php

namespace App\Listeners;

use App\Events\SignupEmailVerificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignupEmailVerificationListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param SignupEmailVerificationEvent $event
     * @return void
     */
    public function handle(SignupEmailVerificationEvent $event)
    {
        $event->user_verify->sendSignupEmailVerificationNotification($event->user_verify);
    }
}
