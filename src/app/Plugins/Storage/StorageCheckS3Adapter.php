<?php

namespace App\Plugins\Storage;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Plugin\AbstractPlugin;

class StorageCheckS3Adapter extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'isS3Adapter';
    }

    /**
     * Checks if storage adapter used is for S3 storage
     *
     * @param string $disk
     * @return bool
     */
    public function handle($disk)
    {
        $adapter = Storage::disk($disk)->getAdapter();

        return ($adapter instanceof \League\Flysystem\AwsS3v3\AwsS3Adapter);
    }
}
