<?php

namespace App\Console\Commands\CmCom\SignService;

use App\Services\CmCom\SignService;
use Illuminate\Console\Command;

class UnsubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm-sign:unsubscribe {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unsubscribe webhook registered';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get input
        /** @var string */
        $webhookId = $this->argument('id');

        // Unsubscribe and remove webhook
        $signService = new SignService();
        $response = $signService->unsubscribeEvent($webhookId);

        // Display action status
        if (empty($response['id'])) {
            $this->error('Unable to unsubscribe webhook.');
        } else {
            $this->info('Webhook unsubscribe action was successful.');
            $this->line('ID: ' . $response['id']);
        }

        // Display response
        $this->newLine();
        $this->line('Response: ' . json_encode($response));
    }
}
