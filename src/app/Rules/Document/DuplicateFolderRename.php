<?php

namespace App\Rules\Document;

use Illuminate\Contracts\Validation\Rule;
use App\Enums\Document\DocumentTypes;
use App\Enums\ServiceSelectionTypes;
use App\Models\Document;
use Session;

class DuplicateFolderRename implements Rule
{
    /** @var object */
    private $request;

    /**
     * Create a new rule instance.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
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
        // Service selection session state values
        $service = json_decode(Session::get('ServiceSelected'));

        if (empty($service)) {
            return false;
        }

        // Get document using ID from request
        $document = Document::whereId($this->request->id)->first();

        if (empty($document)) {
            return false;
        }

        if ($document->document_type === DocumentTypes::FOLDER) {
            $documentDirectories = Document::whereDirectoryId($document->directory_id)
                ->whereDocumentType(DocumentTypes::FOLDER)
                ->whereDocumentName($value);

            if ($service->type === ServiceSelectionTypes::RIO) {
                $documentDirectories = $documentDirectories->whereOwnerRioId($service->data->id);
            } else {
                $documentDirectories = $documentDirectories->whereOwnerNeoId($service->data->id);
            }

            return !($documentDirectories->exists());
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
