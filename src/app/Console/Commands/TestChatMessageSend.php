<?php

namespace App\Console\Commands;

use App\Events\Chat\TestMessageSent;
use Illuminate\Console\Command;

class TestChatMessageSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:test-message {id=1} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test chat message';

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
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        $message = $this->argument('message') ?? 'Hello from Command!';

        TestMessageSent::dispatch($id, $message);
    }
}
