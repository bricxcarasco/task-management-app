<?php

/**
 * Image Helper
 *
 * @author yns
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

/**
 * App\Helpers\ImageHelper
 */
class ImageHelper
{
    /**
     * Check if target entity is connected to authenticated session
     *
     * @param string $source
     * @param array $options
     * @return void
     */
    public static function downsize($source, $options = [])
    {
        try {
            // Initialize default
            $default = [
                'file_name' => 'image.png',
                'target_size' => config('image.default_target_downsize'),
                'disk' => config('bphero.private_bucket'),
            ];

            // Overwrite defaults with custom options
            $data = array_merge($default, $options);

            // Initialize storage disk
            $storage = Storage::disk($data['disk']);

            // Get storage size if no file size set
            if (empty($data['file_size'])) {
                $data['file_size'] = $storage->size($source);
            }

            // Disregard when file size is same with target size
            if ($data['file_size'] <= $data['target_size']) {
                return;
            }

            // Setup stream wrapper for s3 adapter
            if ($storage->isS3Adapter($data['disk'])) {
                $storage->initializeS3Stream($data['disk']);
            }

            // Get stream path
            $streamPath = $storage->getStreamPath($source, $data['disk']);

            // Initialize intervention image
            $image = Image::make($storage->get($source));

            // Resize image when file size is too large
            $multiplier = self::computeDownsizeRatio($data['file_size'], $data['target_size']);
            $targetDimension = $image->width() * $multiplier;

            $image->widen((int) $targetDimension, function ($constraint) {
                $constraint->upsize();
            });

            // Remove meta information
            $image->getCore()->stripImage();

            // Resize existing image
            $storage->put($source, $image->stream());

            // Optimize existing image
            ImageOptimizer::optimize($streamPath);
        } catch (\Throwable $exception) {
            Log::error("Image downsizing failed: {$exception->getMessage()}", $options);
        }
    }

    /**
     * Compute dimension multiplier
     *
     * @param int $current
     * @param int $target
     * @return float
     */
    private static function computeDownsizeRatio($current, $target)
    {
        $areaRatio = $target / $current;

        return sqrt($areaRatio);
    }
}
