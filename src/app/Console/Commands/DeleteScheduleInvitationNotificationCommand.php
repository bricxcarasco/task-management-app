<?php

namespace App\Console\Commands;

use App\Models\ScheduleNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteScheduleInvitationNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitation:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete schedule notifications 72 hours after end date and time of the schedule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch all deletable notifications
        $invitations = ScheduleNotification::deletableNotifications()->get();

        if (!$invitations->isEmpty()) {
            try {
                // Delete invitations
                $invitations->each(function ($invitation) {
                    $invitation->delete();
                });

                $this->info('Successfully deleted schedule invitations');
                Log::info('Successfully executed invitation:delete command');
            } catch (\Exception $e) {
                $this->error('Failed to delete schedule invitations');
                Log::info('Failed to execute invitation:delete command');
                Log::info('Error: ' . $e);
            }
        } else {
            $this->info('No schedule invitations to delete');
        }
    }
}
