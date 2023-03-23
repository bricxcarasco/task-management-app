<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RioProfileEditRequest extends FormRequest
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
        return [
            'id' => 'required|integer',
        ];
    }

    /**
     * Handle a failed validation attempt.
    *
    * @param  \Illuminate\Contracts\Validation\Validator  $validator
    * @return void
    *
    * @throws \Illuminate\Validation\ValidationException
    */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->respondInvalidParameters($validator->errors());
        throw new HttpResponseException($response);
    }

    /**
     * Get Rio Profile - Update Attributes
     *
     * @return array
     */
    public function rioUpdateAttributes()
    {
        $update_fields = [];

        switch ($this->id) {
            case 1:
                $update_fields = [
                    'family_name',
                    'first_name',
                    'family_kana',
                    'first_kana',
                ];
                break;
            case 2:
                $update_fields = [
                    'birth_date',
                ];
                break;
            case 3:
                $update_fields = [
                    'gender',
                ];
                break;
            case 4:
                $update_fields = [
                    'present_address_prefecture',
                    'present_address_nationality',
                    'present_address_city',
                    'present_address',
                    'present_address_building',
                ];
                break;
            case 5:
                $update_fields = [
                    'present_address_nationality',
                ];
                break;
            case 6:
                $update_fields = [
                    'self_introduce',
                ];
                break;
            case 7:
                $update_fields = [
                    'family_kana',
                    'first_kana',
                ];
                break;
            case 8:
                $update_fields = [
                    'attribute_code',
                ];
                break;
            case 9:
                $update_fields = [
                    'family_kana',
                    'first_kana',
                ];
                break;
            case 11:
                $update_fields = [
                    'tel',
                ];
                break;
            // case 12:
            //     $update_fields = [
            //         'skills',
            //     ];
            //     break;
            // case 13:
            //     $update_fields = [
            //         'awards',
            //     ];
            //     break;
            // case 14:
            //     $update_fields = [
            //         'product_service_information',
            //     ];
            //     break;
            case 15:
                $update_fields = [
                    'business_use',
                ];
                break;

            default:
                # code...
                break;
        }

        return $this->only($update_fields);
    }
}
