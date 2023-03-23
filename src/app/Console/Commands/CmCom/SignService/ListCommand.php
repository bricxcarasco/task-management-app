<?php

namespace App\Console\Commands\CmCom\SignService;

use App\Services\CmCom\SignService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm-sign:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all webhooks registered';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Fetch webhooks
        $signService = new SignService();
        $response = $signService->fetchWebhooks();

        // Prepare data
        $collection = collect($response)->transform(function ($item, $key) {
            return [
                'id' => $item['id'] ?? '-',
                'url' => $item['url'] ?? '-',
            ];
        });

        // Display table
        $this->table(
            [
                'id',
                'url'
            ],
            $collection->toArray()
        );
    }
}
