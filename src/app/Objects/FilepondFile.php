<?php

namespace App\Objects;

use App\Helpers\CommonHelper;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FilepondFile
{
    /**
     * Temporary path
     *
     * @var string
     */
    protected $path;

    /**
     * Patch Files
     *
     * @var array
     */
    protected $patchFiles = [];

    /**
     * Patch file prefix file name
     *
     * @var string
     */
    protected $tempFilePrefix = 'tmp.patch.';

    /**
     * File Metadata
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * Storage disk used.
     *
     * @var string
     */
    protected $disk = 'private_s3';

    /**
     * Storage instance
     *
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $storage;

    /**
     * Cache
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Create a new service instance.
     *
     * @param string $path
     * @param bool $encrypted
     * @param string|null $disk
     * @return void
     */
    public function __construct($path, $encrypted = false, $disk = null)
    {
        $this->path = $encrypted
            ? self::getPathFromCode($path)
            : $path;

        // Set disk
        $this->disk = $disk ?? $this->disk;

        // Setup disk instance
        $this->storage = Storage::disk($this->disk);
    }

    /**
     * Getter method for path
     *
     * @return string
     */
    public function setDisks()
    {
        return $this->path;
    }

    /**
     * Getter method for path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Getter method for file path
     *
     * @param string|null $filename
     * @return string|null
     */
    public function getFilePath($filename = null)
    {
        // Get cached filename
        if (!empty($this->cache['file_path'])) {
            return $this->cache['file_path'];
        }

        // Get path with specified filename
        if (!empty($filename)) {
            $this->cache['file_path'] = "{$this->path}/{$filename}";

            return $this->cache['file_path'];
        }

        // Get file paths from temporary directory
        $files = $this->storage->files($this->path);

        // Return when no files found
        if (empty($files)) {
            return null;
        }

        // Get first file as target file
        $this->cache['file_path'] = $files[0] ?? null;

        return $this->cache['file_path'];
    }

    /**
     * Converts the given path into a filepond server id/code
     *
     * @param string $path
     * @return string
     */
    public static function toServerCode($path)
    {
        return Crypt::encryptString($path);
    }

    /**
     * Store file to a temporary directory
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $disk
     * @return string|false
     */
    public static function storeTemporaryFile(UploadedFile $file, $disk = null)
    {
        $storageDisk = $disk ?? config('bphero.private_bucket');

        // Generate temporary directory
        $tempPath = static::generateTemporaryDirectory();

        // Get filename
        $fileName = $file->getClientOriginalName();

        // Store file in temporary directory
        $result = $file->storeAs($tempPath, $fileName, $storageDisk);

        // Return false on store error
        if ($result === false) {
            return false;
        }

        // Generate server code from path
        return static::toServerCode($tempPath);
    }

    /**
     * Generate random string to avoid conflicting file names
     *
     * @param string $disk  Disk storage to used
     * @return string
     */
    public static function generateTemporaryDirectory($disk = 'private_s3')
    {
        // Get date for future temporary file removal
        $date = date('Ymd');

        // Generate random string to avoid conflicting file names
        $randomString = Str::random(32);
        $tempPath = "temp/upload/{$date}/{$randomString}";

        // Recurse generation of temp directory
        if (Storage::disk($disk)->exists($tempPath)) {
            return static::generateTemporaryDirectory();
        }

        // Return path
        return $tempPath;
    }

    /**
     * Generate random string to avoid conflicting file names
     *
     * @param string $destination
     * @param string $fileName
     * @param string $disk  Disk storage to used
     * @return string
     */
    public static function generateUniqueDestination($destination, $fileName, $disk = 'private_s3')
    {
        // Generate random string to avoid conflicting file names
        $randomString = Str::random(32);
        $uniquePath = "{$destination}/{$randomString}/{$fileName}";

        // Recurse generation of temp directory
        if (Storage::disk($disk)->exists($uniquePath)) {
            return static::generateUniqueDestination($destination, $fileName);
        }

        // Return path
        return $uniquePath;
    }

    /**
     * Create a temporary patch file for chunk uploads
     *
     * @param int $offset
     * @return bool
     */
    public function createTemporaryPatchFile(int $offset)
    {
        $tempPatchPath = "{$this->path}/{$this->tempFilePrefix}{$offset}";

        // When retry and patch file is already existing, remove patch
        if ($this->storage->exists($tempPatchPath)) {
            $this->storage->delete($tempPatchPath);
        }

        // Initialize stream
        $handle = fopen('php://input', 'rb');

        // Guard clause for invalid stream
        if ($handle === false) {
            return false;
        }

        return $this->storage->writeStream($tempPatchPath, $handle);
    }

    /**
     * Check if patch/chunk upload is completed
     *
     * @param int $length
     * @return bool
     */
    public function isPatchUploadComplete(int $length)
    {
        // Calculate total file size of current files in directory
        $size = 0;
        $patchFiles = $this->storage->files($this->path);

        foreach ($patchFiles as $patchFile) {
            if (!$this->isPatchFile($patchFile)) {
                continue;
            }

            $this->patchFiles[] = $patchFile;
            $size += $this->storage->size($patchFile);
        }

        // Return true when total size is equal or greater than expected file length
        return ($size >= $length);
    }

    /**
     * Compile/merge all patch file chunks into a single file
     *
     * @param string $fileName
     * @return string Stream path used for output file
     */
    public function compilePatchFiles($fileName)
    {
        // Setup output file
        $filePath = "{$this->path}/{$fileName}";

        // Setup stream wrapper for s3 adapter
        if ($this->storage->isS3Adapter($this->disk)) {
            $this->storage->initializeS3Stream($this->disk);
        }

        // Start file streaming
        $streamPath = $this->storage->getStreamPath($filePath, $this->disk);
        $outputHandle = fopen($streamPath, 'wb');

        // Guard clause for invalid stream
        if ($outputHandle === false) {
            throw new \Exception('Invalid file stream.');
        }

        // Guard clause for empty patch files
        if (empty($this->patchFiles)) {
            throw new \Exception('No patch files to compile.');
        }

        // Write patches to output file
        foreach ($this->patchFiles as $patchFile) {
            // Get offset from filename
            list($fullPath, $offset) = explode($this->tempFilePrefix, $patchFile, 2);

            // Get contents of patch file
            $patchContents = $this->storage->get($patchFile);

            // Apply patch
            fseek($outputHandle, (int) $offset);
            fwrite($outputHandle, $patchContents);

            // Remove patch file
            $this->storage->delete($patchFile);
        }

        // Close output instance
        fclose($outputHandle);

        return $streamPath;
    }

    /**
     * Move file from temporary directory to permanent
     *
     * @param string $destination
     * @param string|null $filename - Pass filename to change name to a specified filename
     * @param bool $isUnique - Boolean for encapsulating file with a unique directory
     * @return array
     */
    public function transferFile($destination, $filename = null, $isUnique = true)
    {
        // Get file paths from temporary directory
        $files = $this->storage->files($this->path);

        if (empty($files)) {
            $this->removeDirectory();
            throw new \Exception('No file to transfer: ' . $this->path);
        }

        // Get first file as target file
        $targetFile = $files[0];
        $targetFileName = $filename ?? CommonHelper::getBasename($targetFile);
        $fileSize = $this->storage->size($targetFile);
        $mimeType = $this->storage->mimeType($targetFile);

        // Remove directory separator edges
        $destination = trim($destination, '/');

        // Get transfer path
        $transferPath = $isUnique
            ? self::generateUniqueDestination($destination, $targetFileName)
            : "{$destination}/{$targetFileName}";

        // Move file to permanent destination
        $result = $this->storage->move($targetFile, $transferPath);

        // Get directory path
        $directoryPath = dirname($transferPath);

        // Throw error when unable to transfer
        if (!$result) {
            throw new \Exception('Unable to transfer file: ' . $targetFile);
        }

        // Remove temporary directory
        $this->removeDirectory();

        return [
            'filename' => $targetFileName,
            'path' => $transferPath,
            'file_size' => $fileSize,
            'directory_path' => $directoryPath,
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Remove directory and its contents
     *
     * @return bool
     */
    public function removeDirectory()
    {
        return $this->storage->deleteDirectory($this->path);
    }

    /**
     * Clear directory
     *
     * @return void
     */
    public function clearDirectory()
    {
        $files = $this->storage->allFiles($this->path);

        foreach ($files as $file) {
            $this->storage->delete($file);
        }
    }

    /**
     * Identifies path from encryted code passed
     *
     * @param  string $code
     * @return string
     */
    public static function getPathFromCode($code)
    {
        if (!trim($code)) {
            throw new Exception('Invalid path.');
        }

        return Crypt::decryptString($code);
    }

    /**
     * Check if file path leads to a patch file
     *
     * @param  string $filePath
     * @return bool
     */
    private function isPatchFile($filePath)
    {
        $fileName = basename($filePath);
        $pattern = '/^' . $this->tempFilePrefix . '\d+$/';

        return (bool) preg_match($pattern, $fileName);
    }

    /**
     * Check if temporary file uploaded exists
     *
     * @param string $filename
     * @return bool
     */
    public function exists($filename = null)
    {
        $path = $this->path;

        if (!empty($filename)) {
            $path = $path . '/' . $filename;
        }

        return $this->storage->exists($path);
    }

    /**
     * Set file metadata from filepond request
     *
     * @param string $json
     * @return mixed
     */
    public function setFileMetadata($json)
    {
        $this->metadata = json_decode($json, true);
    }

    /**
     * Get metadata key value
     *
     * @param string $key
     * @return mixed
     */
    public function getFileMetadata($key)
    {
        return $this->metadata[$key] ?? null;
    }

    /**
     * Get filename of temporary file
     *
     * @return string|null
     */
    public function getFileName()
    {
        // Get file path from temporary directory
        $filePath = $this->getFilePath();

        // Return when no file found
        if (empty($filePath)) {
            return null;
        }

        return CommonHelper::getBasename($filePath);
    }

    /**
     * Get mimetype of temporary file
     *
     * @return string|null
     */
    public function getMimeType()
    {
        // Get file path from temporary directory
        $filePath = $this->getFilePath();

        // Return when no file found
        if (empty($filePath)) {
            return null;
        }

        // Get mimetype
        $mimetype = $this->storage->mimeType($filePath);

        // Guard clause for error mimetype fetch
        if ($mimetype === false) {
            return null;
        }

        return $mimetype;
    }

    /**
     * Get temporary file and return as response
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function respondFile()
    {
        // Get file path from temporary directory
        $filePath = $this->getFilePath();

        // Return when no file found
        if (empty($filePath)) {
            return response()->respondNotFound();
        }

        // Prepare information
        $file = $this->storage->get($filePath);
        $mimeType = $this->storage->mimeType($filePath);
        $fileName = CommonHelper::getBasename($filePath);

        return response($file)
            ->withHeaders([
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
    }

    /**
     * Check if server code is valid
     *
     * @param string  $value
     * @return bool
     */
    public static function isValidCode($value)
    {
        try {
            // Decrypt code
            $code = Crypt::decryptString($value);

            // Get configured temporary upload location
            $tempDir = config('bphero.temp_upload_directory');

            // Check if path given is in temp upload
            if (substr($code, 0, strlen($tempDir)) !== $tempDir) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get stream path
     *
     * @return string
     */
    public function getStreamPath()
    {
        $filePath = $this->getFilePath();

        return $this->storage->getStreamPath($filePath, $this->disk);
    }

    /**
     * Get file size
     *
     * @return int|string
     */
    public function getFileSize()
    {
        // Get file paths from temporary directory
        $files = $this->storage->files($this->path);

        if (empty($files)) {
            $this->removeDirectory();
            throw new \Exception('No file to measure: ' . $this->path);
        }

        // Get first file as target file
        $targetFile = $files[0];
        $fileSize = $this->storage->size($targetFile);

        return $fileSize;
    }
}
