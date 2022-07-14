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

    'accepted' => 'ದಿ :attribute ಒಪ್ಪಿಕೊಳ್ಳಬೇಕು.',
    'active_url' => 'ದಿ :attribute ಮಾನ್ಯವಾದ URL ಅಲ್ಲ.',
    'after' => 'ದಿ :attribute ನಂತರದ ದಿನಾಂಕವಾಗಿರಬೇಕು :date.',
    'after_or_equal' => 'ದಿ :attribute ದಿನಾಂಕದ ನಂತರ ಅಥವಾ ಅದಕ್ಕೆ ಸಮನಾಗಿರಬೇಕು :date.',
    'alpha' => 'ದಿ :attribute ಅಕ್ಷರಗಳನ್ನು ಮಾತ್ರ ಹೊಂದಿರಬಹುದು.',
    'alpha_dash' => 'ದಿ :attribute ಅಕ್ಷರಗಳು, ಸಂಖ್ಯೆಗಳು, ಡ್ಯಾಶ್ಗಳು ಮತ್ತು ಅಂಡರ್ಸ್ಕೋರ್ಗಳನ್ನು ಮಾತ್ರ ಒಳಗೊಂಡಿರಬಹುದು.',
    'alpha_num' => 'ದಿ :attribute ಅಕ್ಷರಗಳು ಮತ್ತು ಸಂಖ್ಯೆಗಳನ್ನು ಮಾತ್ರ ಹೊಂದಿರಬಹುದು.',
    'array' => 'ದಿ :attribute ಒಂದು ಶ್ರೇಣಿಯಾಗಿರಬೇಕು.',
    'before' => 'ದಿ :attribute ಮೊದಲು ದಿನಾಂಕವಾಗಿರಬೇಕು :date.',
    'before_or_equal' => 'ದಿ :attribute ದಿನಾಂಕದ ಮೊದಲು ಅಥವಾ ಸಮನಾಗಿರಬೇಕು :date.',
    'between' => [
        'numeric' => 'ದಿ :attribute ನಡುವೆ ಇರಬೇಕು :min ಮತ್ತು :max.',
        'file' => 'ದಿ :attribute ನಡುವೆ ಇರಬೇಕು :min ಮತ್ತು :max ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ನಡುವೆ ಇರಬೇಕು :min ಮತ್ತು :max ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ನಡುವೆ ಇರಬೇಕು :min ಮತ್ತು :max ವಸ್ತುಗಳು.',
    ],
    'boolean' => 'ದಿ :attribute ಕ್ಷೇತ್ರವು ನಿಜ ಅಥವಾ ಸುಳ್ಳಾಗಿರಬೇಕು.',
    'confirmed' => 'ದಿ :attribute ದೃಢೀಕರಣವು ಹೊಂದಿಕೆಯಾಗುವುದಿಲ್ಲ.',
    'date' => 'ದಿ :attribute ಮಾನ್ಯವಾದ ದಿನಾಂಕವಲ್ಲ.',
    'date_equals' => 'ದಿ :attribute ಗೆ ಸಮನಾದ ದಿನಾಂಕವಾಗಿರಬೇಕು :date.',
    'date_format' => 'ದಿ :attribute ಸ್ವರೂಪಕ್ಕೆ ಹೊಂದಿಕೆಯಾಗುವುದಿಲ್ಲ :format.',
    'different' => 'ದಿ :attribute ಮತ್ತು :other ವಿಭಿನ್ನವಾಗಿರಬೇಕು.',
    'digits' => 'ದಿ :attribute ಇರಬೇಕು :digits ಅಂಕೆಗಳು.',
    'digits_between' => 'ದಿ :attribute ನಡುವೆ ಇರಬೇಕು :min ಮತ್ತು :max ಅಂಕೆಗಳು.',
    'dimensions' => 'ದಿ :attribute ಅಮಾನ್ಯವಾದ ಚಿತ್ರದ ಆಯಾಮಗಳನ್ನು ಹೊಂದಿದೆ.',
    'distinct' => 'ದಿ :attribute ಕ್ಷೇತ್ರವು ನಕಲಿ ಮೌಲ್ಯವನ್ನು ಹೊಂದಿದೆ.',
    'email' => 'ದಿ :attribute ಮಾನ್ಯವಾದ ಇಮೇಲ್ ವಿಳಾಸವಾಗಿರಬೇಕು.',
    'ends_with' => 'ದಿ :attribute ಕೆಳಗಿನವುಗಳಲ್ಲಿ ಒಂದನ್ನು ಕೊನೆಗೊಳಿಸಬೇಕು: :values',
    'exists' => 'ದಿ ಆಯ್ಕೆ ಮಾಡಲಾಗಿದೆ :attribute ಅಸಿಂಧು.',
    'file' => 'ದಿ :attribute must be a file.',
    'filled' => 'ದಿ :attribute ಕ್ಷೇತ್ರವು ಮೌಲ್ಯವನ್ನು ಹೊಂದಿರಬೇಕು.',
    'gt' => [
        'numeric' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿರಬೇಕು :value.',
        'file' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿರಬೇಕು :value ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿರಬೇಕು :value ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿನದನ್ನು ಹೊಂದಿರಬೇಕು :value ವಸ್ತುಗಳು.',
    ],
    'gte' => [
        'numeric' => 'ದಿ :attribute ಹೆಚ್ಚು ಅಥವಾ ಸಮನಾಗಿರಬೇಕು :value.',
        'file' => 'ದಿ :attribute ಹೆಚ್ಚು ಅಥವಾ ಸಮನಾಗಿರಬೇಕು :value ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಹೆಚ್ಚು ಅಥವಾ ಸಮನಾಗಿರಬೇಕು :value ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಹೊಂದಿರಬೇಕು :value ವಸ್ತುಗಳು ಅಥವಾ ಹೆಚ್ಚು.',
    ],
    'image' => 'ದಿ :attribute ಒಂದು ಚಿತ್ರವಾಗಿರಬೇಕು.',
    'in' => 'ದಿ ಆಯ್ಕೆ ಮಾಡಲಾಗಿದೆ :attribute ಅಸಿಂಧು.',
    'in_array' => 'ದಿ :attribute ಕ್ಷೇತ್ರದಲ್ಲಿ ಅಸ್ತಿತ್ವದಲ್ಲಿಲ್ಲ :other.',
    'integer' => 'ದಿ :attribute ಒಂದು ಪೂರ್ಣಾಂಕವಾಗಿರಬೇಕು.',
    'ip' => 'ದಿ :attribute ಮಾನ್ಯವಾದ IP ವಿಳಾಸವಾಗಿರಬೇಕು.',
    'ipv4' => 'ದಿ :attribute ಮಾನ್ಯವಾದ IPv4 ವಿಳಾಸವಾಗಿರಬೇಕು.',
    'ipv6' => 'ದಿ :attribute ಮಾನ್ಯವಾದ IPv6 ವಿಳಾಸವಾಗಿರಬೇಕು.',
    'json' => 'ದಿ :attribute ಮಾನ್ಯವಾದ JSON ಸ್ಟ್ರಿಂಗ್ ಆಗಿರಬೇಕು.',
    'lt' => [
        'numeric' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆಯಿರಬೇಕು :value.',
        'file' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆಯಿರಬೇಕು :value ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆಯಿರಬೇಕು :value ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆ ಹೊಂದಿರಬೇಕು :value ವಸ್ತುಗಳು.',
    ],
    'lte' => [
        'numeric' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆ ಅಥವಾ ಸಮಾನವಾಗಿರಬೇಕು :value.',
        'file' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆ ಅಥವಾ ಸಮಾನವಾಗಿರಬೇಕು :value ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಗಿಂತ ಕಡಿಮೆ ಅಥವಾ ಸಮಾನವಾಗಿರಬೇಕು :value ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿನದನ್ನು ಹೊಂದಿರಬಾರದು :value ವಸ್ತುಗಳು.',
    ],
    'max' => [
        'numeric' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿಲ್ಲದಿರಬಹುದು :max.',
        'file' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿಲ್ಲದಿರಬಹುದು :max ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿಲ್ಲದಿರಬಹುದು :max ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಗಿಂತ ಹೆಚ್ಚಿಲ್ಲದಿರಬಹುದು :max ವಸ್ತುಗಳು.',
    ],
    'mimes' => 'ದಿ :attribute ಒಂದು ರೀತಿಯ ಫೈಲ್ ಆಗಿರಬೇಕು: :values.',
    'mimetypes' => 'ದಿ :attribute ಒಂದು ರೀತಿಯ ಫೈಲ್ ಆಗಿರಬೇಕು: :values.',
    'min' => [
        'numeric' => 'ದಿ :attribute ಕನಿಷ್ಠ ಇರಬೇಕು :min.',
        'file' => 'ದಿ :attribute ಕನಿಷ್ಠ ಇರಬೇಕು :min ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಕನಿಷ್ಠ ಇರಬೇಕು :min ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಕನಿಷ್ಠ ಹೊಂದಿರಬೇಕು :min ವಸ್ತುಗಳು.',
    ],
    'not_in' => 'ದಿ ಆಯ್ಕೆ ಮಾಡಲಾಗಿದೆ :attribute ಅಸಿಂಧು.',
    'not_regex' => 'ದಿ :attribute ಸ್ವರೂಪವು ಅಮಾನ್ಯವಾಗಿದೆ.',
    'numeric' => 'ದಿ :attribute ಒಂದು ಸಂಖ್ಯೆಯಾಗಿರಬೇಕು.',
    'present' => 'ದಿ :attribute ಕ್ಷೇತ್ರವು ಪ್ರಸ್ತುತವಾಗಿರಬೇಕು.',
    'regex' => 'ದಿ :attribute ಸ್ವರೂಪವು ಅಮಾನ್ಯವಾಗಿದೆ.',
    'required' => 'ದಿ :attribute ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ.',
    'required_if' => 'ದಿ :attribute ಯಾವಾಗ ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :other ಇದೆ :value.',
    'required_unless' => 'ದಿ :attribute ಹೊರತು ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :other ಒಳಗಿದೆ :values.',
    'required_with' => 'ದಿ :attribute ಯಾವಾಗ ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :values ಇರುತ್ತದೆ.',
    'required_with_all' => 'ದಿ :attribute ಯಾವಾಗ ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :values ಇರುತ್ತವೆ.',
    'required_without' => 'ದಿ :attribute ಯಾವಾಗ ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :values ಪ್ರಸ್ತುತ ಇಲ್ಲ.',
    'required_without_all' => 'ದಿ :attribute ಯಾವುದೂ ಇಲ್ಲದಿದ್ದಾಗ ಕ್ಷೇತ್ರ ಅಗತ್ಯವಿದೆ :values ಇರುತ್ತವೆ.',
    'same' => 'ದಿ :attribute ಮತ್ತು :other ಹೊಂದಲೇ ಬೇಕು.',
    'size' => [
        'numeric' => 'ದಿ :attribute ಇರಬೇಕು :size.',
        'file' => 'ದಿ :attribute ಇರಬೇಕು :size ಕಿಲೋಬೈಟ್ಗಳು.',
        'string' => 'ದಿ :attribute ಇರಬೇಕು :size ಪಾತ್ರಗಳು.',
        'array' => 'ದಿ :attribute ಒಳಗೊಂಡಿರಬೇಕು :size ವಸ್ತುಗಳು.',
    ],
    'starts_with' => 'ದಿ :attribute ಕೆಳಗಿನವುಗಳಲ್ಲಿ ಒಂದನ್ನು ಪ್ರಾರಂಭಿಸಬೇಕು: :values',
    'string' => 'ದಿ :attribute ಸ್ಟ್ರಿಂಗ್ ಆಗಿರಬೇಕು.',
    'timezone' => 'ದಿ :attribute ಮಾನ್ಯವಾದ ವಲಯವಾಗಿರಬೇಕು.',
    'unique' => 'ದಿ :attribute ಈಗಾಗಲೇ ತೆಗೆದುಕೊಳ್ಳಲಾಗಿದೆ.',
    'uploaded' => 'ದಿ :attribute ಅಪ್ಲೋಡ್ ಮಾಡಲು ವಿಫಲವಾಗಿದೆ.',
    'url' => 'ದಿ :attribute ಸ್ವರೂಪವು ಅಮಾನ್ಯವಾಗಿದೆ.',
    'uuid' => 'ದಿ :attribute ಮಾನ್ಯವಾದ UUID ಆಗಿರಬೇಕು.',

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

    'not_empty' => "ದಿ :attribute ಜಾಗವನ್ನು ಮಾತ್ರ ಅನುಮತಿಸುವುದಿಲ್ಲ.",
    'not_exists' => 'ದಿ :attribute ಅಸ್ತಿತ್ವದಲ್ಲಿಲ್ಲ.',
    'no_space' => "ದಿ :attribute ಜಾಗವನ್ನು ಹೊಂದಿರಬಾರದು.",
    'not_match' => 'ದಿ :attribute ನಮ್ಮ ದಾಖಲೆಗಳೊಂದಿಗೆ ಹೊಂದಾಣಿಕೆಯಾಗುತ್ತಿಲ್ಲ.',
    'not_equal' => 'ದಿ :attribute ಮತ್ತು :other ಒಂದೇ ಆಗಿರಬಾರದು',
    'equal_to' => 'ದಿ :attribute ಮತ್ತು :other ಒಂದೇ ಆಗಿರಬೇಕು',
    'lettersonly' => 'ದಿ :attribute ಅಕ್ಷರಗಳು ಮತ್ತು ಸ್ಥಳಗಳನ್ನು ಮಾತ್ರ ಹೊಂದಿರಬಹುದು.',
    'alpha_numeric' => 'ದಿ :attribute ಅಕ್ಷರಗಳು, ಸಂಖ್ಯೆಗಳು ಮತ್ತು ಸ್ಥಳಗಳನ್ನು ಮಾತ್ರ ಹೊಂದಿರಬಹುದು.',
    'image_dimentions' => 'ದಿ :attribute ನ ಇರಬೇಕು :min x :max px ಆಯಾಮಗಳು.',
    'emoji_found'   =>  'ಎಮೋಜಿಗಳನ್ನು ಒಳಗೆ ಅನುಮತಿಸಲಾಗುವುದಿಲ್ಲ :attribute',

    // 'duration'      =>  'The duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'ವೀಡಿಯೊದ ಅವಧಿಯು 60 ಸೆಕೆಂಡುಗಳಿಗಿಂತ ಹೆಚ್ಚಿರಬಾರದು.',
    'video'         =>  [
        'portrait'  =>  'ವೀಡಿಯೊ ಪೋರ್ಟ್ರೇಟ್ ಮೋಡ್ನಲ್ಲಿರಬೇಕು',
        'landscape' =>  'ವೀಡಿಯೊ ಲ್ಯಾಂಡ್ಸ್ಕೇಪ್ ಮೋಡ್ನಲ್ಲಿರಬೇಕು',
    ],

    'attributes' => [
        "field"         =>  "ಕ್ಷೇತ್ರ",
        "password"      =>  "ಗುಪ್ತಪದ",
        "name"          =>  "ಹೆಸರು",
        "username"      =>  "ಬಳಕೆದಾರ ಹೆಸರು",
        "contact"       =>  "ಸಂಪರ್ಕಿಸಿ",
        "country_id"    =>  "ದೇಶ",
        "country"       =>  "ದೇಶ",
        "start_age"     =>  "ವಯಸ್ಸು ಪ್ರಾರಂಭ",
        "end_age"       =>  "ಅಂತ್ಯ ವಯಸ್ಸು",
        "public"        =>  [
            "email"     =>  "ಇಮೇಲ್",
            "contact"   =>  "ಸಂಪರ್ಕಿಸಿ",
        ],
        "description"   =>  "ವಿವರಣೆ",
        "link"          =>  "ಲಿಂಕ್",
        "city"          =>  "ನಗರ",
        "remove_profile"    =>  "ಪ್ರೊಫೈಲ್ ತೆಗೆದುಹಾಕಿ",
        "birthday"          =>  "ಹುಟ್ಟುಹಬ್ಬ",
        "profile"           =>  "ಪ್ರೊಫೈಲ್",
        "old_password"      =>  "ಹಳೆಯ ಪಾಸ್ವರ್ಡ್",
        "token"             =>  "ಟೋಕನ್",
        "device_type"       =>  "ಸಾಧನದ ಪ್ರಕಾರ",
        "checksum"          =>  "ಚೆಕ್ಸಮ್",
        "limit"             =>  "ಮಿತಿ",
        "offset"            =>  "ಆಫ್ಸೆಟ್",
        "type"              =>  "ಮಾದರಿ",
        "category"          =>  "ವರ್ಗ",
        "thumbnail"         =>  "ಥಂಬ್ನೇಲ್",
        "video"             =>  "ವೀಡಿಯೊ",
        "duration"          =>  "ಅವಧಿ",
        "hashtags"          =>  "ಹ್ಯಾಶ್ಟ್ಯಾಗ್ಗಳು",
        "cause"             =>  "ಉಂಟು",
        "state"             =>  "ರಾಜ್ಯ",
        "id"                =>  "ಐಡಿ",
        "keyword"           =>  "ಕೀವರ್ಡ್",
        "device"            =>  "ಸಾಧನ",
        "udid"              =>  "udid",
        "user"              =>  "ಬಳಕೆದಾರ",
        "reported_user"     =>  "ವರದಿ ಮಾಡಿದ ಬಳಕೆದಾರ",
        "message"           =>  "ಸಂದೇಶ",
        "user_id"           =>  "ಬಳಕೆದಾರ",

        // Custom Resource Validation
        "contact_no"            =>  "ಸಂಪರ್ಕ ಸಂಖ್ಯೆ",
        "security_token"        =>  "ಭದ್ರತಾ ಟೋಕನ್",
        "first_name"            =>  "ಮೊದಲ ಹೆಸರು",
        "last_name"             =>  "ಕೊನೆಯ ಹೆಸರು",
        "full_name"             =>  "ಪೂರ್ಣ ಹೆಸರು",
        "email"                 =>  "ಇಮೇಲ್",
        "birth_date"            =>  "ಹುಟ್ಟಿದ ದಿನಾಂಕ",
        "gender"                =>  "ಲಿಂಗ",
        "interest"              =>  "ಆಸಕ್ತಿ",
        "location"              =>  "ಸ್ಥಳ",
        "location_id"           =>  "ಸ್ಥಳ",
        "language"              =>  "ಭಾಷೆ",
        "languages"             =>  "ಭಾಷೆಗಳು",
        "profile_photo"         =>  "ಪ್ರೊಫೈಲ್ ಫೋಟೋ",
        "country_code"          =>  "ದೇಶದ ಕೋಡ್",
        "room_name"             =>  "ಕೋಣೆಯ ಹೆಸರು",
        "room"                  =>  "ಕೊಠಡಿ",
        "room_id"               =>  "ಕೊಠಡಿ",
        "participant_id"        =>  "ಭಾಗವಹಿಸುವವರು",
        "search"                =>  "ಹುಡುಕಿ Kannada",
        "distance"              =>  "ದೂರ",
        "parent_id"             =>  "ಪೋಷಕ",
        "level"                 =>  "ಮಟ್ಟದ",
        "attribute"             =>  "ಗುಣಲಕ್ಷಣ",
        "interests"             =>  "ಆಸಕ್ತಿಗಳು",
        "interests.*"           =>  "ಆಸಕ್ತಿಗಳು",
        "images"                =>  "ಚಿತ್ರಗಳು",
        "videos"                =>  "ವೀಡಿಯೊಗಳು",
        "videos.*"              =>  "ವೀಡಿಯೊಗಳು",
        "image"                 =>  "ಚಿತ್ರ",
        "image_path"            =>  "ಚಿತ್ರ ಮಾರ್ಗ",
        "api_key"               =>  "api ಕೀ",
        "api_secret"            =>  "ಅಪಿ ರಹಸ್ಯ",
        "sid"                   =>  "ಸಿಡ್",
        "push_id"               =>  "ಪುಶ್ ಐಡಿ",
        "identity"              =>  "ಗುರುತು",
        "time_line"             =>  "ಸಮಯದ ಸಾಲು",
        "facebook_id"           =>  "ಫೇಸ್ಬುಕ್ ಐಡಿ",
        "google_id"             =>  "ಗೂಗಲ್ ಐಡಿ",
        "apple_id"              =>  "ಸೇಬು ಐಡಿ",
        "latitude"              =>  "ಅಕ್ಷಾಂಶ",
        "longitude"             =>  "ರೇಖಾಂಶ",
        "reason"                =>  "ಕಾರಣ",
        "start_time"            =>  "ಆರಂಭವಾಗುವ",
        "end_time"              =>  "ಅಂತಿಮ ಸಮಯ",
        "remaining_time"        =>  "ಉಳಿದ ಸಮಯ",
        "version"               =>  "ಆವೃತ್ತಿ",
        "os"                    =>  "os",
        "app_version"           =>  "ಅಪ್ಲಿಕೇಶನ್ ಆವೃತ್ತಿ",
        "status"                =>  "ಸ್ಥಿತಿ",
        "about_me"              =>  "ನನ್ನ ಬಗ್ಗೆ",
        "fav_movie"             =>  "ನೆಚ್ಚಿನ ಚಲನಚಿತ್ರ",
        "personalities"         =>  "ವ್ಯಕ್ತಿತ್ವಗಳು",
        "remove_personalities"  =>  "ವ್ಯಕ್ತಿತ್ವಗಳನ್ನು ತೆಗೆದುಹಾಕಿ",
        "education"             =>  "ಶಿಕ್ಷಣ",
        "university_college"    =>  "ವಿಶ್ವವಿದ್ಯಾಲಯ ಮತ್ತು ಕಾಲೇಜು",
        "profession"            =>  "ವೃತ್ತಿ",
        "religion"              =>  "ಧರ್ಮ",
        "relationship_status"   =>  "ಸಂಬಂಧದ ಸ್ಥಿತಿ",
        "i_am_here"             =>  "ನಾನು ಇಲ್ಲಿದ್ದೇನೆ",
        "food_preference"       =>  "ಆಹಾರ ಆದ್ಯತೆ",
        "drinking"              =>  "ಕುಡಿಯುವ",
        "smoking"               =>  "ಧೂಮಪಾನ",
        "star_sign"             =>  "ರಾಶಿ",
        "community"             =>  "ಸಮುದಾಯ",
        "old_profile_photo"     =>  "ಹಳೆಯ ಪ್ರೊಫೈಲ್ ಫೋಟೋ",
        "old_images"            =>  "ಹಳೆಯ ಚಿತ್ರಗಳು",
        "voice"                 =>  "ಧ್ವನಿ",
        "voice_answer"          =>  "ಧ್ವನಿ ಉತ್ತರ",
        "remove_voice"          =>  "ಧ್ವನಿಯನ್ನು ತೆಗೆದುಹಾಕಿ",
        "remove_video"          =>  "ವೀಡಿಯೊ ತೆಗೆದುಹಾಕಿ",
        "remove_image"          =>  "ಚಿತ್ರವನ್ನು ತೆಗೆದುಹಾಕಿ",
        "remove_interests"      =>  "ಆಸಕ್ತಿಗಳನ್ನು ತೆಗೆದುಹಾಕಿ",
        "image_sequence"        =>  "ಚಿತ್ರ ಅನುಕ್ರಮ",
        "file"                  =>  "ಕಡತ",
    ],
];
