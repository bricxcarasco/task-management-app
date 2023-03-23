<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignupEmailVerificationEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var object
     */
    public $user_verify;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\UserVerification $user_verify
     */
    public function __construct($user_verify)
    {
        $this->user_verify = $user_verify;
    }
}
