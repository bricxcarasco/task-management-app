<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Constant;
use App\Enums\AffiliateTypes;
use App\Enums\Rio\GenderType;
use App\Enums\Rio\SecretQuestionType;
use App\Enums\PrefectureTypes;
use App\Rules\ValidEmailFormat;

class RegistrationInputRequest extends FormRequest
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
            'registration_token' => 'required|string|exists:user_verifications,token|max:' . Constant::RANDOM_HASH_CHARACTERS,
            'family_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'family_kana' => 'required|katakana|max:255',
            'first_kana' => 'required|katakana|max:255',
            'present_address_prefecture' => [
                'required',
                Rule::in(PrefectureTypes::getValues())
            ],
            'present_address_nationality' => 'nullable|required_if:present_address_prefecture,0|string|max:255',
            'email' => [
                'required',
                'string',
                'email:filter',
                'max:255',
                'unique:users,email',
                new ValidEmailFormat()
            ],
            'password' => 'required|string|between:8,20|contain_alpha_numeric|alpha_numeric_symbol|confirmed',
            'gender' => [
                'required',
                Rule::in(GenderType::getValues())
            ],
            'tel' => 'required|phone:AUTO,JP|unique:rios,tel',
            'secret_question' => [
                'required',
                Rule::in(SecretQuestionType::getValues())
            ],
            'birth_date' => 'required|date|before:' . $now,
            'secret_answer' => 'required|string|max:255',
            'affiliate' => [
                'nullable',
                Rule::in(AffiliateTypes::getValues())
            ],
            'rd_code' => 'nullable|string|max:255',
            'a8' => 'nullable|string|max:255',
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
            'registration_token' =>  $this->registration_token,
            'family_name' =>  $this->family_name,
            'first_name' =>  $this->first_name,
            'family_kana' =>  $this->family_kana,
            'first_kana' =>  $this->first_kana,
            'present_address_prefecture' =>  $this->present_address_prefecture,
            'present_address_nationality' => $this->present_address_nationality ?? null,
            'email' => $this->email,
            'password' =>  $this->password,
            'gender' =>  $this->gender,
            'tel' => $this->tel,
            'secret_question' => $this->secret_question,
            'birth_date' => $this->birth_date,
            'secret_answer' => $this->secret_answer,
            'affiliate' => $this->affiliate,
            'rd_code' => $this->rd_code,
            'a8' => $this->a8,
        ]);
    }

    /**
     * return rio register info for server.
     *
     * @return array request
     */
    public function registerRioAttributes()
    {
        return $this->only([
            'family_name',
            'first_name',
            'family_kana',
            'first_kana',
            'birth_date',
            'gender',
            'tel',
            'referral_code',
            'referral_message_template'
        ]);
    }

    /**
     * return user register info for input.
     *
     * @return array request
     */
    public function registerUserAttributes()
    {
        return $this->only([
            'email',
            'password',
            'secret_question',
            'secret_answer',
            'affiliate',
        ]);
    }

    /**
     * return rio profile register info for input.
     *
     * @return array request
     */
    public function registerRioProfileAttributes()
    {
        return $this->only([
            'present_address_prefecture',
            'present_address_nationality',
        ]);
    }

    /**
     * Inject other attributes
     *
     * @return array request
     */
    public function registerOtherAttributes()
    {
        return $this->only([
            'registration_token',
            'password_confirmation',
            'rd_code',
            'a8',
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'present_address_nationality.required_if' => __('validation.required_if', [
                'value' => prefecture_value(PrefectureTypes::OTHER)
            ]),
            'tel.unique' => __('messages.validation_error_existing_phone_number')
        ];
    }
}
