<?php

namespace App\Plugins\Storage;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Plugin\AbstractPlugin;

class InitializeS3Stream extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'initializeS3Stream';
    }

    /**
     * Register stream wrapper for S3
     *
     * @param string $disk
     * @return mixed
     */
    public function handle($disk)
    {
        $adapter = Storage::disk($disk)->getAdapter();
        $adapter->getClient()->registerStreamWrapper();

        return $adapter;
    }
}
