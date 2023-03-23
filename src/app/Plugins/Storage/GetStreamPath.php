<?php

namespace App\Plugins\Storage;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Plugin\AbstractPlugin;

class GetStreamPath extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'getStreamPath';
    }

    /**
     * Get stream-based path of given path
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function handle($path, $disk)
    {
        $adapter = Storage::disk($disk)->getAdapter();

        return Storage::disk($disk)->isS3Adapter($disk)
            ? "s3://{$adapter->getBucket()}/{$path}"
            : Storage::disk($disk)->path($path);
    }
}
