<?php

namespace App\Objects;

use App\Enums\FilepondFileTypes;
use App\Helpers\CommonHelper;
use App\Models\ClassifiedSale;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassifiedImages
{
    /**
     * Get classified product main photo
     *
     * @param string|object $images
     *      JSON format:
     *          [
     *              {
     *                  "filename": "profile.png",
     *                  "mimetype": "image/png",
     *                  "image_path": "public/classifieds/rio/1/2_profile.png",
     *              }
     *          ]
     * @return string
     */
    public static function getMainPhotoUrl($images = null)
    {
        $defaultImg = url(config('bphero.default_product_image'));

        // Return default product image
        if (empty($images)) {
            return $defaultImg;
        }

        // Convert string to JSON data
        if (is_string($images)) {
            $images = json_decode($images);
        }

        // Set first photo as main photo
        $mainPhotoObject = $images[0] ?? null;

        // Return default product image
        if (empty($mainPhotoObject)) {
            return $defaultImg;
        }

        // Initialize storage
        $targetDisk = config('bphero.public_bucket');
        $storage = Storage::disk($targetDisk);

        // Prepare path
        $targetFilePath = CommonHelper::removeMainDirectoryPath($mainPhotoObject->image_path);
        $encodedPath = CommonHelper::urlencodePath($targetFilePath);

        return $storage->url($encodedPath);
    }

    /**
     * Get classified product images paths
     *
     * @param string|object|null $images
     *      JSON format:
     *          [
     *              {
     *                  "filename": "profile.png",
     *                  "mimetype": "image/png",
     *                  "image_path": "public/classifieds/rio/1/2_profile.png",
     *              }
     *          ]
     * @param callable|null $callback Callback function
     * @return array
     */
    public static function getImagePaths($images, ?callable $callback = null)
    {
        $imagePaths = [];

        // Return no images
        if (empty($images)) {
            return [];
        }

        // Convert string to JSON data
        if (is_string($images)) {
            $images = json_decode($images);
        }

        // Get and alter image paths
        foreach ($images as $image) {
            $imagePaths[] = (is_null($callback))
                ? $image->image_path
                : $callback($image->image_path);
        }

        return $imagePaths;
    }

    /**
     * Get classified product images url list
     *
     * @param string|object|null $images
     *      JSON format:
     *          [
     *              {
     *                  "filename": "profile.png",
     *                  "mimetype": "image/png",
     *                  "image_path": "public/classifieds/rio/1/2_profile.png",
     *              }
     *          ]
     * @return array
     */
    public static function getImageUrls($images)
    {
        // Initialize storage instance
        $targetDisk = config('bphero.public_bucket');
        $storage = Storage::disk($targetDisk);

        return static::getImagePaths($images, function ($path) use (&$storage) {
            $targetFilePath = CommonHelper::removeMainDirectoryPath($path);
            $encodedPath = CommonHelper::urlencodePath($targetFilePath);

            return $storage->url($encodedPath);
        });
    }

    /**
     * Save images
     *
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @param array $images
     *
     * @return bool
     */
    public static function saveImages(ClassifiedSale $classifiedSale, $images)
    {
        $result = [];
        $localFiles = [];
        $currentImages = static::getImagePaths($classifiedSale->images);

        foreach ($images as $image) {
            $imageInfo = null;
            switch ($image['type']) {
                case FilepondFileTypes::LIMBO:
                    $imageInfo = static::saveTempImage($classifiedSale, $image['value']);

                    break;

                case FilepondFileTypes::LOCAL:
                    $imageInfo = static::saveLocalImage($image['value']);
                    $localFiles[] = $image['value'];

                    break;
            }

            if (!empty($imageInfo)) {
                $result[] = $imageInfo;
            }
        }

        if (!empty($result)) {
            // Encode image information
            $classifiedSale->images = json_encode($result, JSON_UNESCAPED_SLASHES) ?: null;
        } else {
            $classifiedSale->images = null;
        }

        // Save database record
        $classifiedSale->save();

        // Get removed images via existing local and current
        $pendingRemovalImages = array_diff($currentImages, $localFiles);

        // Delete images in storage
        foreach ($pendingRemovalImages as $deletedImage) {
            static::deleteLocalImage($deletedImage);
        }

        return true;
    }

    /**
     * Save temporary image
     *
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @param string $code
     *
     * @return array|null
     */
    public static function saveTempImage(ClassifiedSale $classifiedSale, $code)
    {
        if (empty($code)) {
            return null;
        }

        try {
            // Initialize filepond file
            $targetDisk = config('bphero.public_bucket');
            $filepond = new FilepondFile($code, true, $targetDisk);

            // Get temporary file name
            $fileName = $filepond->getFileName();

            // Handle non-existing temp upload file
            if (empty($fileName)) {
                return null;
            }

            // Set target directory based on user type
            $targetDirectory = null;
            if (!empty($classifiedSale->selling_neo_id)) {
                $targetDirectory = config('bphero.neo_classified_storage_path') . $classifiedSale->selling_neo_id;
            } else {
                $targetDirectory = config('bphero.rio_classified_storage_path') . $classifiedSale->selling_rio_id;
            }

            // Generate filename
            $targetFilename = static::generatePrefixedFilename($targetDirectory, $classifiedSale->id, $fileName);

            // Transfer temporary file to permanent directory
            $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

            // Prepare image information
            return [
                'filename' => $fileName,
                'mimetype' => $fileinfo['mime_type'],
                'image_path' => config('bphero.public_directory') . '/'
                    . $targetDirectory . '/'
                    . $targetFilename,
            ];
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Save local image
     *
     * @param string $path
     *
     * @return array|null
     */
    public static function saveLocalImage($path)
    {
        if (empty($path)) {
            return null;
        }

        try {
            // Initialize filepond file
            $targetDisk = config('bphero.public_bucket');
            $storage = Storage::disk($targetDisk);
            $targetPath = CommonHelper::removeMainDirectoryPath($path);


            // Check if file is existing
            if (!$storage->exists($targetPath)) {
                return null;
            }

            // Prepare information
            $mimeType = $storage->mimeType($targetPath);
            $fileName = CommonHelper::getBasename($targetPath);

            // Prepare filename
            $targetFilename = ClassifiedImages::removePrefix($fileName);
            if (!empty($targetFilename)) {
                $fileName = $targetFilename;
            }

            // Prepare image information
            return [
                'filename' => $fileName,
                'mimetype' => $mimeType,
                'image_path' => $path
            ];
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Delete local image
     *
     * @param string $path
     *
     * @return bool
     */
    public static function deleteLocalImage($path)
    {
        try {
            // Initialize filepond file
            $targetDisk = config('bphero.public_bucket');
            $storage = Storage::disk($targetDisk);
            $targetPath = CommonHelper::removeMainDirectoryPath($path);


            // Check if file is existing
            if (!$storage->exists($targetPath)) {
                return false;
            }

            return $storage->delete($targetPath);
        } catch (\Exception $exception) {
            report($exception);
            return false;
        }
    }

    /**
     * Get classified product images list
     *
     * @param string|object $images
     *      JSON format:
     *          {
     *              "0":"public\/classifieds\/messages\/9\/20220421152250_image1.PNG",
     *              "1":"public\/classifieds\/messages\/9\/20220421152251_image2.PNG",
     *              "2":"public\/classifieds\/messages\/9\/20220421152251_image3.png"
     *          }
     * @return array
     */
    public static function getChatImages($images = null)
    {
        // Initialize storage instance
        $targetDisk = config('bphero.public_bucket');
        $storage = Storage::disk($targetDisk);
        $imageUrls = [];

        // Return no images
        if (empty($images)) {
            return [];
        }

        // Convert string to JSON data
        if (is_string($images)) {
            $images = json_decode($images);
        }

        foreach ($images as $image) {
            $targetFilePath = CommonHelper::removeMainDirectoryPath($image);
            $encodedPath = CommonHelper::urlencodePath($targetFilePath);

            $imageUrls[] = $storage->url($encodedPath);
        }

        return $imageUrls;
    }

    /**
     * Generate prefixed filename
     *
     * @param string $destination
     * @param int $id
     * @param string $filename
     *
     * @return string
     */
    public static function generatePrefixedFilename($destination, $id, $filename)
    {
        $targetDisk = config('bphero.public_bucket');

        // Generate random string to avoid conflicting file names
        $randomString = Str::random(6);
        $uniqueFilename = $id . '_' . $randomString . '_' . $filename;
        $uniquePath = $destination . '/' . $uniqueFilename;

        // Recurse generation of temp directory
        if (Storage::disk($targetDisk)->exists($uniquePath)) {
            return static::generatePrefixedFilename($destination, $id, $filename);
        }

        // Return path
        return $uniqueFilename;
    }

    /**
     * Remove filename prefix
     *
     * @param string $filename
     *
     * @return string|null
     */
    public static function removePrefix($filename)
    {
        /** @var string|null */
        return preg_replace('/\d+_\w+_/', '', $filename);
    }

    /**
     * Parse form inputs
     *
     * @param array $uploadFiles
     * @param array $localFiles
     *
     * @return array
     */
    public static function parseFileInputs($uploadFiles, $localFiles = [])
    {
        $images = [];

        foreach ($uploadFiles as $key => $value) {
            $images[$key] = [
                'type' => FilepondFileTypes::LIMBO,
                'value' => $value,
            ];
        }

        foreach ($localFiles as $key => $value) {
            $images[$key] = [
                'type' => FilepondFileTypes::LOCAL,
                'value' => $value,
            ];
        }

        // Sort by key
        ksort($images, SORT_NUMERIC);

        return $images;
    }
}
