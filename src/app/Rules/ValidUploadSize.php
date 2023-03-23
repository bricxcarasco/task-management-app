<?php

namespace App\Rules;

use App\Objects\FilepondFile;
use App\Objects\ServiceSelected;
use Illuminate\Contracts\Validation\Rule;
use Session;

class ValidUploadSize implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Service selection session state values
        $total = 0;
        $service = ServiceSelected::getSelected();

        // Get total upload size
        foreach ($value as $file) {
            $filepond = new FilepondFile($file, true);
            $total = $total + $filepond->getFileSize();
        }

        return $total <= $service->storage_info->available;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('Insufficient free space');
    }
}
