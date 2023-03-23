<?php

namespace App\Rules\Document;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Requests\Document\SaveSettingRequest;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Document\DocumentShareType;
use App\Models\Rio;
use App\Models\Neo;
use App\Models\NeoGroup;
use App\Models\DocumentAccess;
use Session;

class CheckShareTypeCondition implements Rule
{
    /** @var SaveSettingRequest */
    private $setting;

    /**
     * Create a new rule instance.
     * SaveSettingRequest
     * @param SaveSettingRequest $setting
     *
     * @return void
     */
    public function __construct($setting)
    {
        $this->setting = $setting;
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

        if ($service->type === ServiceSelectionTypes::NEO) {
            if ($service->data->is_member) {
                return false;
            }
        }

        $model = (object) null;
        $ifAlreadyShared = DocumentAccess::whereDocumentId($this->setting->document_id);

        switch ($value) {
            case DocumentShareType::RIO:
                $model = Rio::find($this->setting->id);
                $ifAlreadyShared = $ifAlreadyShared->whereRioId($this->setting->id)->first();
                break;

            case DocumentShareType::NEO:
                $model = Neo::find($this->setting->id);
                $ifAlreadyShared = $ifAlreadyShared->whereNeoId($this->setting->id)->first();
                break;

            case DocumentShareType::NEO_GROUP:
                $model = NeoGroup::find($this->setting->id);
                $ifAlreadyShared = $ifAlreadyShared->whereNeoGroupId($this->setting->id)->first();
                break;
        }

        if (!$model || $ifAlreadyShared) {
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
        return trans('messages.unauthorized_document_sharing');
    }
}
