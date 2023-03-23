<?php

namespace App\Providers;

use App\Events\SignupEmailVerificationEvent;
use App\Events\RegistrationVerifiedEvent;
use App\Listeners\SignupEmailVerificationListener;
use App\Listeners\RegistrationVerifiedListener;
use App\Models\ChatParticipant;
use App\Models\ElectronicContract;
use App\Models\NeoBelong;
use App\Observers\ChatParticipantObserver;
use App\Observers\ElectronicContractObserver;
use App\Observers\NeoBelongObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SignupEmailVerificationEvent::class => [
            SignupEmailVerificationListener::class,
        ],
        RegistrationVerifiedEvent::class => [
            RegistrationVerifiedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        ChatParticipant::observe(ChatParticipantObserver::class);
        ElectronicContract::observe(ElectronicContractObserver::class);
        NeoBelong::observe(NeoBelongObserver::class);
    }
}
