<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StartMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start maintenance mode';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // Prepare parameters
            $parameters = [
                '--secret' => config('bphero.maintenance.secret'),
                '--render' => config('bphero.maintenance.render'),
                '--redirect' => config('bphero.maintenance.redirect'),
            ];

            // Set refresh when available
            $refreshTime = config('bphero.maintenance.refresh');
            if (!empty($refreshTime)) {
                $parameters['--refresh'] = $refreshTime;
            }

            // Start maintenance mode
            $exitCode = Artisan::call('down', $parameters);

            if ($exitCode === 0) {
                $this->warn('Application is now in maintenance mode.');

                return;
            }
        } catch (\Exception $exception) {
            report($exception);
        }

        $this->error('Unable to start maintenance mode');
    }
}
