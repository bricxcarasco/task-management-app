<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrationVerifiedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var object
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
