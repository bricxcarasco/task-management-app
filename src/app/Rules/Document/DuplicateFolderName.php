<?php

namespace App\Rules\Document;

use Illuminate\Contracts\Validation\Rule;
use App\Enums\Document\DocumentTypes;
use App\Models\Document;
use Session;

class DuplicateFolderName implements Rule
{
    /** @var object */
    private $document;

    /**
     * Create a new rule instance.
     *
     * @param \App\Http\Requests\Document\CreateFolderRequest $document
     *
     * @return void
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        if (!$service) {
            return false;
        }

        // Check if directory_id exists
        if (!isset($this->document->directory_id)) {
            $this->document->directory_id = null;
        }

        $document = Document::whereDirectoryId($this->document->directory_id)
            ->whereDocumentType(DocumentTypes::FOLDER)
            ->whereDocumentName($value)
            ->first();

        if ($document) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.duplicate_folder_name');
    }
}
