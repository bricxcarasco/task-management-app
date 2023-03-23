<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    /**
     * Fetch public S3 objects
     *
     * @param string $path
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function viewPublic($path)
    {
        try {
            // Initialize storage
            $disk = config('bphero.public_bucket');
            $storage = Storage::disk($disk);

            // Parse URL
            $url = urldecode($path);
            $parsedUrl = parse_url($url);

            // Handle non-existing parsed path
            // @phpstan-ignore-next-line
            if (empty($parsedUrl['path'])) {
                abort(400);
            }

            // Check if file is existing, throw not found if not existing
            if (!$storage->exists($parsedUrl['path'])) {
                abort(404);
            }

            // Get file and encode to base64
            $file = $storage->get($parsedUrl['path']);
            $mimeType = $storage->mimeType($parsedUrl['path']);
        } catch (\Exception $exception) {
            throw $exception;
        }

        // Respond file with proper header content-type
        return response($file)
            ->withHeaders(['Content-Type' => $mimeType]);
    }
}
