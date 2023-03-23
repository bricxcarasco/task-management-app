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

    'accepted'             => ':attributeを承認してください。',
    'active_url'           => ':attributeは、有効なURLではありません。',
    'after'                => ':attributeには、:dateより後の日付を指定してください。',
    'after_or_equal'       => ':attributeには、:date以降の日付を指定してください。',
    'alpha'                => ':attributeには、アルファベッドのみ使用できます。',
    'alpha_dash'           => ':attributeには、英数字(\'A-Z\',\'a-z\',\'0-9\')とハイフンと下線(\'-\',\'_\')が使用できます。',
    'alpha_num'            => ':attributeには、英数字(\'A-Z\',\'a-z\',\'0-9\')が使用できます。',
    'array'                => ':attributeには、配列を指定してください。',
    'attached'             => 'This :attribute is already attached.',
    'before'               => ':attributeには、:dateより前の日付を指定してください。',
    'before_or_equal'      => ':attributeには、:date以前の日付を指定してください。',
    'between'              => [
        'array'   => ':attributeの項目は、:min個から:max個にしてください。',
        'file'    => ':attributeには、:min KBから:max KBまでのサイズのファイルを指定してください。',
        'numeric' => ':attributeには、:minから、:maxまでの数字を指定してください。',
        'string'  => ':attributeは、:min文字から:max文字にしてください。',
    ],
    'boolean'              => ':attributeには、\'true\'か\'false\'を指定してください。',
    'confirmed'            => '入力:attributeと一致していません。',
    'current_password'     => ':attributeが正しくありません。',
    'date'                 => ':attributeは、正しい日付ではありません。',
    'date_equals'          => ':attributeは:dateに等しい日付でなければなりません。',
    'date_format'          => ':attributeの形式は、\':format\'と合いません。',
    'different'            => ':attributeと:otherには、異なるものを指定してください。',
    'digits'               => ':attributeは、:digits桁にしてください。',
    'digits_between'       => ':attributeは、:min桁から:max桁にしてください。',
    'dimensions'           => ':attributeの画像サイズが無効です',
    'distinct'             => ':attributeの値が重複しています。',
    'email'                => ':attributeの形式に誤りがあります。',
    'email_alias'          => ':attributeはエイリアスを使用してはいけません',
    'ends_with'            => ':attributeは、次のうちのいずれかで終わらなければなりません。: :values',
    'exists'               => '選択された:attributeは、有効ではありません。',
    'file'                 => ':attributeはファイルでなければいけません。',
    'filled'               => ':attributeは必須です。',
    'gt'                   => [
        'array'   => ':attributeの項目数は、:value個より大きくなければなりません。',
        'file'    => ':attributeは、:value KBより大きくなければなりません。',
        'numeric' => ':attributeは、:valueより大きくなければなりません。',
        'string'  => ':attributeは、:value文字より大きくなければなりません。',
    ],
    'gte'                  => [
        'array'   => ':attributeの項目数は、:value個以上でなければなりません。',
        'file'    => ':attributeは、:value KB以上でなければなりません。',
        'numeric' => ':attributeは、:value以上でなければなりません。',
        'string'  => ':attributeは、:value文字以上でなければなりません。',
    ],
    'image'                => ':attributeには、画像を指定してください。',
    'in'                   => '選択された:attributeは、有効ではありません。',
    'in_array'             => ':attributeが:otherに存在しません。',
    'integer'              => ':attributeには、整数を指定してください。',
    'ip'                   => ':attributeには、有効なIPアドレスを指定してください。',
    'ipv4'                 => ':attributeはIPv4アドレスを指定してください。',
    'ipv6'                 => ':attributeはIPv6アドレスを指定してください。',
    'json'                 => ':attributeには、有効なJSON文字列を指定してください。',
    'lt'                   => [
        'array'   => ':attributeの項目数は、:value個より小さくなければなりません。',
        'file'    => ':attributeは、:value KBより小さくなければなりません。',
        'numeric' => ':attributeは、:valueより小さくなければなりません。',
        'string'  => ':attributeは、:value文字より小さくなければなりません。',
    ],
    'lte'                  => [
        'array'   => ':attributeの項目数は、:value個以下でなければなりません。',
        'file'    => ':attributeは、:value KB以下でなければなりません。',
        'numeric' => ':attributeは、:value以下でなければなりません。',
        'string'  => ':attributeは、:value文字以下でなければなりません。',
    ],
    'max'                  => [
        'array'   => ':attributeの項目は、:max個以下にしてください。',
        'file'    => ':attributeには、:max KB以下のファイルを指定してください。',
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'string'  => ':attributeは:max文字以内で入力してください。',
    ],
    'mimes'                => ':attributeには、:valuesタイプのファイルを指定してください。',
    'mimetypes'            => ':attributeには、:valuesタイプのファイルを指定してください。',
    'min'                  => [
        'array'   => ':attributeの項目は、:min個以上にしてください。',
        'file'    => ':attributeには、:min KB以上のファイルを指定してください。',
        'numeric' => ':attributeには、:min以上の数字を指定してください。',
        'string'  => ':attributeは、:min文字以上にしてください。',
    ],
    'multiple_of'          => 'The :attribute must be a multiple of :value',
    'not_in'               => '選択された:attributeは、有効ではありません。',
    'not_regex'            => ':attributeの形式が無効です。',
    'numeric'              => ':attributeには、数字を指定してください。',
    'password'             => 'パスワードが正しくありません。',
    'phone'                => '正しい携帯番号ではありません。',
    'present'              => ':attributeが存在している必要があります。',
    'regex'                => ':attributeには、有効な正規表現を指定してください。',
    'relatable'            => 'This :attribute may not be associated with this resource.',
    'required'             => ':attributeは必須です。',
    'required_if'          => ':otherが:valueの場合、:attributeを指定してください。',
    'required_unless'      => ':otherが:values以外の場合、:attributeを指定してください。',
    'required_with'        => ':valuesが指定されている場合、:attributeも指定してください。',
    'required_with_all'    => ':valuesが全て指定されている場合、:attributeも指定してください。',
    'required_without'     => ':valuesが指定されていない場合、:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを指定してください。',
    'same'                 => ':attributeと:otherが一致しません。',
    'size'                 => [
        'array'   => ':attributeの項目は、:size個にしてください。',
        'file'    => ':attributeには、:size KBのファイルを指定してください。',
        'numeric' => ':attributeには、:sizeを指定してください。',
        'string'  => ':attributeは、:size文字にしてください。',
    ],
    'starts_with'          => ':attributeは、次のいずれかで始まる必要があります。:values',
    'string'               => ':attributeには、文字を指定してください。',
    'timezone'             => ':attributeには、有効なタイムゾーンを指定してください。',
    'unique'               => '指定の:attributeは既に使用されています。',
    'uploaded'             => ':attributeのアップロードに失敗しました。',
    'url'                  => ':attributeは、有効なURL形式で指定してください。',
    'uuid'                 => ':attributeは、有効なUUIDでなければなりません。',
    'password'             => ':attributeは英数字の両方を含む必要があります。',

    'general' => '選択した:attributeは無効です。',

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
            'contain_alpha_numeric' => ':attributeは英数字の両方を含む必要があります。',
        ],
        'current_password' => [
            'contain_alpha_numeric' => ':attributeは英数字の両方を含む必要があります。',
            'current_password' => ':attributeが正しくありません。',
        ],
        'new_password' => [
            'contain_alpha_numeric' => ':attributeは英数字の両方を含む必要があります。',
        ],
        'new_password_confirmation' => [
            'contain_alpha_numeric' => ':attributeは英数字の両方を含む必要があります。',
            'same' => '新しいパスワードと一致していません。',
        ],
        'first_kana' => [
            'katakana' => '全角カタカナで入力してください。'
        ],
        'family_kana' => [
            'katakana' => '全角カタカナで入力してください。'
        ],
        'organization_kana' => [
            'katakana' => '全角カタカナで入力してください。'
        ],
        'present_address_nationality' => [
            'required_if' => ':attributeは必須です。'
        ],
        'home_address_nationality' => [
            'required_if' => ':attributeは必須です。'
        ],
        'organization_kana' => [
            'katakana' => '全角カタカナで入力してください。'
        ],
        'nationality' => [
            'required_if' => ':attributeは必須です。'
        ],
        'start_time' => [
            'date_format' => ':attributeは、正しい時刻ではありません。',
            'required_if' => '開始時刻を選択してください。'
        ],
        'end_time' => [
            'date_format' => ':attributeは、正しい時刻ではありません。',
            'required_if' => '終了時刻を選択してください。'
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
        'email'     => 'メールアドレス',
        'password'  => 'パスワード',
        'family_name' => '姓',
        'first_name' => '名',
        'family_kana' => '姓',
        'first_kana' => '名',
        'present_address_prefecture' => '居住都道府県',
        'present_address_nationality' => '国名',
        'gender' => '性別',
        'tel' => '電話番号',
        'profession' => '職業',
        'secret_question' => '秘密の質問',
        'birth_date' => '生年月日',
        'secret_answer' => '秘密の質問の回答',
        'organization_name' => '組織名',
        'organization_kana' => '組織名フリガナ',
        'organization_type' => '組織属性',
        'establishment_date' => '設立年月日',
        'site_url' => 'URL',
        'prefecture' => '所在地：都道府県',
        'nationality' => '国名',
        'city' => '所在地：市区町村',
        'address' => '所在地：それ以降の住所',
        'building' => 'ビル・アパート',
        'self_introduce' => '自己紹介',
        'industry' => '業種',
        'accept_connections' => '接続を受け入れる',
        'accept_belongs' => '所属を受け入れる',
        'school_name' => '学校名',
        'graduation_date' => '卒業年月',
        'present_address_nationality' => '国名',
        'present_address_prefecture' => '居住都道府県',
        'present_address_city' => '市区町村',
        'present_address' => '番地',
        'present_address_building' => 'ビル・アパート',
        'home_address_prefecture' => '出身都道府県',
        'home_address_nationality' => '国名',
        'home_address_city' => '市区町村',
        'tel' => '電話番号',
        'self_introduce' => '自己紹介',
        'school_name' => '学校名',
        'graduation_date' => '卒業年月',
        'award_year' => '表彰年',
        'content' => '内容',
        'email_address' => 'メールアドレス',
        'invite_message' => '招待メッセージ',
        'search' => 'キーワード',
        'document_id' => 'ドキュメントID',
        'document_name' => 'ドキュメント名',
        'storage_type_id' => 'ストレージタイプID',
        'storage_path' => 'ストレージパス',
        'schedule_title' => 'タイトル',
        'start_date' => '開始',
        'end_date' => '終了',
        'start_time' => '開始時刻',
        'end_time' => '終了時刻',
        'caption' => '説明',
        'limit_datetime' => '期日',
        'task_title' => 'タスクタイトル',
        'limit_date' => '期日',
        'limit_time' => '期時刻',
        'priority' => '優先度',
        'free_word' => 'フリーワード',
        'issue_start_date' => '発行開始',
        'issue_end_date' => '発行終了',
        'expiration_start_date' => '有効期限開始',
        'expiration_end_date' => '有効期限終了',
        'payment_start_date' => '支払期開始',
        'payment_end_date' => '支払期終了',
        'receipt_start_date' => '受領日開始',
        'receipt_end_date' => '受領日の終了',
        'delivery_start_date' => '納品開始',
        'delivery_end_date' => '納品終了',
        'amount_min' => '最低金額',
        'amount_max' => '最大金額',
        'comment' => 'コメント',
        'reaction' => '操作'
    ],
];
