<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute must only contain letters.',
    'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute must only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'email_alias' => 'The :attribute must not use an alias',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'phone' => 'The :attribute field contains an invalid number.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL.',
    'uuid' => 'The :attribute must be a valid UUID.',

    'general' => 'The selected :attribute is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'password' => [
            'contain_alpha_numeric' => 'The :attribute field must contain both alphanumeric characters.',
        ],
        'current_password' => [
            'contain_alpha_numeric' => 'The :attribute field must contain both alphanumeric characters.',
            'current_password' => 'The :attribute is incorrect.',
        ],
        'new_password' => [
            'contain_alpha_numeric' => 'The :attribute field must contain both alphanumeric characters.',
        ],
        'new_password_confirmation' => [
            'contain_alpha_numeric' => 'The :attribute field must contain both alphanumeric characters.',
            'same' => 'The :attribute does not match the new password.',
        ],
        'first_kana' => [
            'katakana' => 'Please enter in the full-width katakana.'
        ],
        'family_kana' => [
            'katakana' => 'Please enter in the full-width katakana.'
        ],
        'organization_kana' => [
            'katakana' => 'Please enter in the full-width katakana.'
        ],
        'present_address_nationality' => [
            'required_if' => 'The :attribute field is required.'
        ],
        'home_address_nationality' => [
            'required_if' => 'The :attribute field is required.'
        ],
        'organization_kana' => [
            'katakana' => 'Please enter in the full-width katakana.'
        ],
        'nationality' => [
            'required_if' => 'The :attribute field is required.'
        ],
        'start_time' => [
            'date_format' => 'The :attribute field is not a valid time.',
            'required_if' => 'Please select an start time.'
        ],
        'end_time' => [
            'date_format' => 'The :attribute field is not a valid time.',
            'required_if' => 'Please select an end time.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email'     => 'Email address',
        'password'  => 'Password',
        'family_name' => 'Family name',
        'first_name' => 'First name',
        'family_kana' => 'Family name furigana',
        'first_kana' => 'First name furigana',
        'present_address_prefecture' => 'Prefecture of Residences',
        'present_address_nationality' => 'Nationality',
        'gender' => 'Gender',
        'tel' => 'Telephone No.',
        'profession' => 'Profession',
        'secret_question' => 'Secret Question',
        'birth_date' => 'Birthday',
        'secret_answer' => 'Answer to Secret Question',
        'organization_name' => 'Organization name',
        'organization_kana' => 'Organization name furigana',
        'organization_type' => 'Organizational attributes',
        'establishment_date' => 'Date of Establishment',
        'site_url' => 'URL',
        'prefecture' => 'Prefecture',
        'nationality' => 'Country',
        'city' => 'City',
        'address' => 'Address',
        'building' => 'Building',
        'self_introduce' => 'Self-introduction',
        'industry' => 'Industry',
        'accept_connections' => 'Accept connections',
        'accept_belongs' => 'Accept belongs',
        'present_address_nationality' => 'Country',
        'present_address_prefecture' => 'Prefecture',
        'present_address_city' => 'Municipality/City',
        'present_address' => 'Address',
        'present_address_building' => 'Building',
        'home_address_prefecture' => 'Prefecture',
        'home_address_nationality' => 'Country',
        'home_address_city' => 'Municipality',
        'tel' => 'Telephone',
        'self_introduce' => 'self introduction',
        'organization_name' => 'Organization name',
        'organization_kana' => 'Organization name furigana',
        'organization_type' => 'Organizational attributes',
        'invite_message' => 'Invitation Message',
        'search' => 'keyword',
        'document_id' => 'Document ID',
        'document_name' => 'Document Name',
        'storage_type_id' => 'Storage Type ID',
        'storage_path' => 'Storage Path',
        'schedule_title' => 'Title',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'caption' => 'Description',
        'limit_datetime' => 'Duedate',
        'task_title' => 'Task title',
        'limit_date' => 'Due date',
        'limit_time' => 'Due time',
        'priority' => 'Priority',
        'free_word' => 'Free word',
        'issue_start_date' => 'Issue start date',
        'issue_end_date' => 'Issue end date',
        'expiration_start_date' => 'Expiration start date',
        'expiration_end_date' => 'Expiration end date',
        'payment_start_date' => 'Payment start date',
        'payment_end_date' => 'Payment end date',
        'receipt_start_date' => 'Receipt start date',
        'receipt_end_date' => 'Receipt end date',
        'delivery_start_date' => 'Delivery start date',
        'delivery_end_date' => 'Delivery end date',
        'amount_min' => 'Minimum Amount',
        'amount_max' => 'Maximum Amount',
        'comment' => 'Comment',
        'reaction' => 'Reaction'
    ],
];
