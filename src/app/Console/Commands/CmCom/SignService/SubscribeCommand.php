<?php

namespace App\Console\Commands\CmCom\SignService;

use App\Services\CmCom\SignService;
use Illuminate\Console\Command;

class SubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm-sign:subscribe {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to event';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get input
        /** @var string|null */
        $url = $this->argument('url');

        // Subscribe and register webhook
        $signService = new SignService();
        $response = $signService->subscribeEvent($url);

        // Display subscribe status
        if (empty($response['id'])) {
            $this->error('Unable to subscribe to event.');
        } else {
            $this->info('Webhook subscription successful.');
            $this->line('ID: ' . $response['id']);
        }

        // Display response
        $this->newLine();
        $this->line('Response: ' . json_encode($response));
    }
}
