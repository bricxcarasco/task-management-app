<?php

namespace App\Console\Commands\Push;

use App\Models\User;
use App\Notifications\SimplePushFcmNotification;
use Illuminate\Console\Command;

class SendNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:notify {rio} {title} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification to a user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get inputs
        /** @var int */
        $rioId = $this->argument('rio');
        /** @var string */
        $title = $this->argument('title');
        /** @var string */
        $message = $this->argument('message');

        // Fetch target user
        $user = User::whereRioId($rioId)
            ->firstOrFail();

        // Send push notification
        $user->notify(new SimplePushFcmNotification($title, $message));
    }
}
