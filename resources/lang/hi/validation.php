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

    'accepted' => ':attribute का स्वीकार किया जाना चाहिए।',
    'active_url' => ':attribute मान्य यूआरएल नहीं है।',
    'after' => ':attribute के बाद की तारीख होनी चाहिए :date',
    'after_or_equal' => ':attribute के बाद या उसके बराबर की तारीख होनी चाहिए :date',
    'alpha' => ':attribute केवल अक्षर हो सकते हैं',
    'alpha_dash' => ':attribute केवल अक्षर, संख्याएं, डैश और अंडरस्कोर हो सकते हैं',
    'alpha_num' => ':attribute केवल अक्षर और संख्याएँ हो सकती हैं',
    'array' => ':attribute एक सरणी होना चाहिए।',
    'before' => ':attribute पहले की तारीख होनी चाहिए :date',
    'before_or_equal' => ':attribute तारीख से पहले या उसके बराबर होनी चाहिए :date',
    'between' => [
        'numeric' => ':attribute :min तथा :max के बीच होना चाहिए',
        'file' => ':attribute :min तथा :max किलोबाइट। के बीच होना चाहिए',
        'string' => ':attribute :min तथा :max पात्र के बीच होना चाहिए',
        'array' => ':attribute :min तथा :max आइटम के बीच होना चाहिए ',
    ],
    'boolean' => ':attribute फ़ील्ड सही या गलत होना चाहिए।',
    'confirmed' => ':attribute पुष्टि मेल नहीं खाता।',
    'date' => ':attribute मान्य तिथि नहीं है।',
    'date_equals' => ':attribute :date तारीख बराबर होनी चाहिए',
    'date_format' => ':attribute :format प्रारूप से मेल नहीं खाता',
    'different' => ':attribute तथा :other अलग होना चाहिए।',
    'digits' => ':attribute होना चाहिए :digits अंक',
    'digits_between' => ':attribute के बीच होना चाहिए :min तथा :max अंक',
    'dimensions' => ':attribute अमान्य छवि आयाम हैं',
    'distinct' => ':attribute फ़ील्ड में डुप्लिकेट मान है',
    'email' => ':attribute एक वैध ई-मेल होना चाहिए।',
    'ends_with' => ':attribute इनमें से किसी एक के साथ समाप्त होना चाहिए :values',
    'exists' => 'चुना हुआ :attribute अमान्य है',
    'file' => ':attribute एक फाइल होनी चाहिए',
    'filled' => ':attribute फ़ील्ड का मान होना चाहिए',
    'gt' => [
        'numeric' => ':attribute :value से बड़ा होना चाहिए',
        'file' => ':attribute :value किलोबाइट से बड़ा होना चाहिए',
        'string' => ':attribute :value पात्र से बड़ा होना चाहिए',
        'array' => ':attribute :value आइटम से अधिक होना चाहिए',
    ],
    'gte' => [
        'numeric' => ':attribute से बड़ा या बराबर होना चाहिए :value',
        'file' => ':attribute :value किलोबाइट से बड़ा या बराबर होना चाहिए',
        'string' => ':attribute :value पात्र से बड़ा या बराबर होना चाहिए',
        'array' => ':attribute :max आइटम या अधिक होना आवश्यक है',
    ],
    'image' => ':attribute एक छवि होनी चाहिए',
    'in' => 'चुना हुआ :attribute अमान्य है',
    'in_array' => ':attribute मे :other फ़ील्ड मौजूद नहीं है',
    'integer' => ':attribute पूर्णांक होना चाहिए',
    'ip' => ':attribute एक वैध आईपी पता होना चाहिए',
    'ipv4' => ':attribute एक मान्य IPv4 पता होना चाहिए',
    'ipv6' => ':attribute एक मान्य IPv6 पता होना चाहिए',
    'json' => ':attribute एक वैध JSON स्ट्रिंग होना चाहिए',
    'lt' => [
        'numeric' => ':attribute :value से कम होना चाहिए',
        'file' => ':attribute :value किलोबाइट से कम होना चाहिए',
        'string' => ':attribute :value पात्र से कम होना चाहिए',
        'array' => ':attribute :value आइटम से कम होना चाहिए',
    ],
    'lte' => [
        'numeric' => ':attribute :value से कम या बराबर होना चाहिए',
        'file' => ':attribute :value किलोबाइट से कम या बराबर होना चाहिए',
        'string' => ':attribute :value पात्र से कम या बराबर होना चाहिए',
        'array' => ':attribute :value आइटम से अधिक नहीं होना चाहिए',
    ],
    'max' => [
        'numeric' => ':attribute :max से बड़ा नहीं हो सकता',
        'file' => ':attribute :max किलोबाइट से बड़ा नहीं हो सकता',
        'string' => ':attribute :max पात्र से बड़ा नहीं हो सकता',
        'array' => ':attribute :max आइटम से अधिक नहीं हो सकता है',
    ],
    'mimes' => ':attribute :values प्रकार की एक फ़ाइल होनी चाहिए',
    'mimetypes' => ':attribute :values प्रकार की एक फ़ाइल होनी चाहिए',
    'min' => [
        'numeric' => ':attribute कम से कम होना चाहिए :min',
        'file' => ':attribute :min किलोबाइट कम से कम होना चाहिए',
        'string' => ':attribute :min पात्र कम से कम होना चाहिए',
        'array' => ':attribute :min आइटम कम से कम होना चाहिए',
    ],
    'not_in' => 'चुना हुआ:attribute अमान्य है',
    'not_regex' => ':attribute का प्रारूप अमान्य है',
    'numeric' => ':attribute एक संख्या होनी चाहिए',
    'password' => 'पासवर्ड गलत है',
    'present' => ':attribute क्षेत्र मौजूद होना चाहिए',
    'regex' => ':attribute प्रारूप अमान्य है',
    'required' => ':attribute का स्थान आवश्यक है',
    'required_if' => ':attribute फ़ील्ड की आवश्यकता होती है जब :other :value हो',
    'required_unless' => ':attribute फ़ील्ड आवश्यक है जब तक :other :values में है',
    'required_with' => ':attribute फ़ील्ड की आवश्यकता होती है जब :values उपस्थित है',
    'required_with_all' => ':attribute फ़ील्ड की आवश्यकता होती है जब :values मौजूद हैं',
    'required_without' => ':attribute फ़ील्ड की आवश्यकता होती है जब :values मौजूद नहीं है',
    'required_without_all' => ':attribute फ़ील्ड की आवश्यकता होती है जब इनमें से कोई :values नहीं मौजूद हैं',
    'same' => ':attribute तथा :other मेल खाना चाहिए',
    'size' => [
        'numeric' => ':attribute :size होना चाहिए',
        'file' => ':attribute :size किलोबाइट होना चाहिए',
        'string' => ':attribute :size पात्र होना चाहिए',
        'array' => ':attribute :size आइटम शामिल होना चाहिए',
    ],
    'starts_with' => ':attribute निम्नलिखित में से किसी एक से शुरू होना चाहिए: :values',
    'string' => ':attribute एक स्ट्रिंग होना चाहिए',
    'timezone' => ':attribute एक वैध क्षेत्र होना चाहिए',
    'unique' => ':attribute पहले से ही लिया जा चुका है',
    'uploaded' => ':attribute अपलोड करने में विफल',
    'url' => ':attribute प्रारूप अमान्य है',
    'uuid' => ':attribute एक वैध यूयूआईडी होना चाहिए',

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

    'attributes' => [],

    'not_empty' => ":attribute केवल स्थान की अनुमति नहीं देता है",
    'not_exists' => ':attribute मौजूद नहीं है',
    'no_space' => ":attribute जगह नहीं होनी चाहिए",
    'not_match' => ':attribute हमारे रिकॉर्ड से मेल नहीं खा रहा है',
    'not_equal' => ':attribute तथा :other समान नहीं होना चाहिए',
    'lettersonly' => ':attribute इसमें केवल अक्षर और स्थान हो सकते हैं',
    'alpha_numeric' => ':attribute इसमें केवल अक्षर, संख्याएं और रिक्त स्थान हो सकते हैं',
    'image_dimentions' => ':attribute :width x :height px आयाम का होना चाहिए',

];
