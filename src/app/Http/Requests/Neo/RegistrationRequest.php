<?php

namespace App\Http\Requests\Neo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PrefectureTypes;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\OverseasSupportType;
use App\Enums\Neo\RestrictConnectionType;
use App\Rules\ValidEmailFormat;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $now = date('Y-m-d');

        return [
            'organization_name' => 'required|string|max:255',
            'organization_kana' => 'required|katakana|max:255',
            'organization_type' => [
                'required',
                Rule::in(OrganizationAttributeType::getValues())
            ],
            'establishment_date' => 'required|date|before:' . $now,
            'prefecture' => [
                'required',
                Rule::in(PrefectureTypes::getValues())
            ],
            'nationality' => 'nullable|required_if:prefecture,0|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email:filter',
                'max:255',
                new ValidEmailFormat()
            ],
            'tel' => 'nullable|numeric|digits_between:1,15',
            'site_url' => 'nullable|url',
            'self_introduce' => 'nullable|string|max:500',
            'accept_connections' => [
                'required',
                Rule::in(RestrictConnectionType::getValues())
            ],
            'accept_belongs' => [
                'required',
                Rule::in(AcceptParticipationType::getValues())
            ],
            'overseas_support' => ['required', Rule::in(OverseasSupportType::getValues())],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Merge route parameters before validation
        $this->merge([
            'organization_name' => $this->organization_name,
            'organization_kana' => $this->organization_kana,
            'organization_type' => $this->organization_type,
            'establishment_date' => $this->establishment_date,
            'prefecture' => $this->prefecture,
            'nationality' => $this->nationality ?? null,
            'city' => $this->city ?? null,
            'address' => $this->address ?? null,
            'building' => $this->building ?? null,
            'email' => $this->email ?? null,
            'tel' => $this->tel ?? null,
            'site_url' => $this->site_url ?? null,
            'self_introduce' => $this->self_introduce ?? null,
            'accept_connections' => $this->accept_connections,
            'accept_belongs' => $this->accept_belongs,
            'overseas_support' => $this->overseas_support,
        ]);
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nationality.required_if' => __('messages.nationality_required_if_prefecture_is_other')
        ];
    }

    /**
     * return only attributes for neo
     *
     * @return array request
     */
    public function neoAttributes()
    {
        return $this->only([
            'organization_name',
            'organization_kana',
            'organization_type',
            'establishment_date',
            'tel',
        ]);
    }

    /**
     * return only attributes for neo profile
     *
     * @return array request
     */
    public function neoProfileAttributes()
    {
        return $this->only([
            'prefecture',
            'nationality',
            'city',
            'address',
            'building',
            'self_introduce',
            'overseas_support'
        ]);
    }

    /**
     * return only attributes for neo privacy
     *
     * @return array request
     */
    public function neoPrivacyAttributes()
    {
        return $this->only([
            'accept_connections',
            'accept_belongs',
        ]);
    }

    /**
     * return only attributes for neo privacy
     *
     * @return array request
     */
    public function neoExpertEmail()
    {
        return $this->only([
            'email',
        ]);
    }

    /**
     * return only attributes for neo privacy
     *
     * @return array request
     */
    public function neoExpertUrl()
    {
        return $this->only([
            'organization_name',
            'site_url',
        ]);
    }
}
