<?php

namespace App\Providers;

use App\Plugins\Storage\GetStreamPath;
use App\Plugins\Storage\InitializeS3Stream;
use App\Plugins\Storage\StorageCheckS3Adapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class ExtendedStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Attach custom plugins for local driver
        Storage::extend('local', function ($app, $config) {
            return Storage::createLocalDriver($config)
                ->addPlugin(new StorageCheckS3Adapter())
                ->addPlugin(new GetStreamPath());
        });

        // Attach custom plugins for s3 driver
        Storage::extend('s3', function ($app, $config) {
            return Storage::createS3Driver($config)
                ->addPlugin(new StorageCheckS3Adapter())
                ->addPlugin(new InitializeS3Stream())
                ->addPlugin(new GetStreamPath());
        });
    }
}
