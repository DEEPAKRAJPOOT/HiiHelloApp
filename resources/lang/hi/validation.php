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

    'accepted' => ':attribute को स्वीकार किया जाना चाहिए',
    'active_url' => ':attribute एक मान्य URL नहीं है',
    'after' => ':attribute के बाद एक तारीख होनी चाहिए :date',
    'after_or_equal' => ':attribute तिथि के बाद या उसके बराबर की तारीख होनी चाहिए',
    'alpha' => ':attribute में केवल अक्षर हो सकते हैं',
    'alpha_dash' => ':attribute में केवल अक्षर, संख्या, डैश और अंडरस्कोर हो सकते हैं',
    'alpha_num' => ':attribute में केवल अक्षर और संख्याएँ हो सकती हैं',
    'array' => ':attribute एक सरणी होनी चाहिए',
    'before' => ':attribute तिथि से पहले की तारीख होनी चाहिए। ',
    'before_or_equal' => ':attribute: तिथि से पहले या उसके बराबर की तारीख होनी चाहिए',
    'between' => [
        'numeric' => ':attribute के बीच होना चाहिए :min और max',
        'file' => ':attribute के बीच होना चाहिए :min और max किलोबाइट',
        'string' => ':attribute के बीच होना चाहिए :min और max वर्ण',
        'array' => ':attribute के बीच होना चाहिए :min और max आइटम',
    ],
    'boolean' => ':attribute क्षेत्र सही या गलत होना चाहिए। ',
    'confirmed' => ':attribute पुष्टि मेल नहीं खाती',
    'date' => ':attribute मान्य दिनांक नहीं है',
    'date_equals' => ':attribute :date के बराबर दिनांक होनी चाहिए',
    'date_format' => ':attribute प्रारूप से मेल नहीं खाती :format',
    'different' => ':attribute और :other अलग होना चाहिए',
    'digits' => ':attribute होनी चाहिए :digits अंक',
    'digits_between' => ':attribute के बीच होना चाहिए :min और :max अंक',
    'dimensions' => ':attribute में अमान्य छवि आयाम हैं',
    'distinct' => ':attribute फ़ील्ड में एक डुप्लिकेट मान है',
    'email' => ':attribute एक मान्य ईमेल एड्रेस होना चाहिए',
    'ends_with' => ':attribute निम्नलिखित में से किसी एक के साथ समाप्त होनी चाहिए',
    'exists' => ' :attribute विशेषता अमान्य है',
    'file' => ':attribute एक फ़ाइल होनी चाहिए',
    'filled' => 'विशेषता फ़ील्ड में एक मान होना चाहिए ',
    'gt' => [
        'numeric' => ':attribute से अधिक होना चाहिए :value',
        'file' => ':attribute से अधिक होना चाहिए :value किलोबाइट',
        'string' => ':attribute मान :value वर्णों से अधिक होना चाहिए',
        'array' => ':attribute से अधिक होना चाहिए :value आइटम',
    ],
    'gte' => [
        'numeric' => ':value से अधिक या समान होना चाहिए :attribute',
        'file' => ':value से अधिक या बराबर होना चाहिए :attribute किलोबाइट',
        'string' => ':value से अधिक या बराबर होना चाहिए :attribute वर्ण.',
        'array' => ':value होना चाहिए :attribute मूल्य आइटम या अधिक',
    ],
    'image' => ':attribute एक छवि होनी चाहिए',
    'in' => ':attribute विशेषता अमान्य है.',
    'in_array' => ':attribute फ़ील्ड में मौजूद नहीं है :other.',
    'integer' => ':attribute पूर्णांक होनी चाहिए',
    'ip' => ':attribute का वैध IP पता होना चाहिए.',
    'ipv4' => ':attribute मान्य IPv4 पता होना चाहिए',
    'ipv6' => ':attribute मान्य IPv6 पता होना चाहिए',
    'json' => ':attribute एक वैध JSON स्ट्रिंग होनी चाहिए',
    'lt' => [
        'numeric' => ':attribute :value से कम होना चाहिए.',
        'file' => ':attribute :value किलोबाइट से कम होना चाहिए.',
        'string' => ':attribute :value वर्णों से कम होना चाहिए.',
        'array' => ':attribute से कम होना चाहिए :value आइटम',
    ],
    'lte' => [
        'numeric' => ':attribute से कम या बराबर होना चाहिए :value.',
        'file' => ':attribute कम या बराबर होना चाहिए: मान किलोबाइट',
        'string' => ':attribute कम या बराबर होना चाहिए: मान वर्ण',
        'array' => ':attribute से अधिक नहीं होना चाहिए :value आइटम.',
    ],
    'max' => [
        'numeric' => ':attribute :max से अधिक नहीं हो सकता है.',
        'file' => ':attribute: :max किलोबाइट से अधिक नहीं हो सकता.',
        'string' => ':attribute: :max वर्णों से अधिक नहीं हो सकता',
        'array' => ':attribute :max से अधिक नहीं हो सकता है',
    ],
    'mimes' => ':attribute एक प्रकार की फ़ाइल होनी चाहिए: :values',
    'mimetypes' => ':attribute प्रकार की एक फ़ाइल होनी चाहिए: :values',
    'min' => [
        'numeric' => ':attribute कम से कम होनी चाहिए :min.',
        'file' => ':attribute कम से कम होना चाहिए :min किलोबाइट.',
        'string' => ':attribute कम से कम होना चाहिए :min वर्ण',
        'array' => ':attribute में कम से कम :min आइटम होना चाहिए',
    ],
    'not_in' => ':attribute विशेषता अमान्य है.',
    'not_regex' => ':attribute प्रारूप अमान्य है',
    'numeric' => ':attribute एक संख्या होनी चाहिए.',
    'present' => ':attribute फ़ील्ड मौजूद होना चाहिए',
    'regex' => ':attribute विशेषता अमान्य है',
    'required' => ':attribute फ़ील्ड आवश्यक है',
    'required_if' => ':attribute फ़ील्ड की आवश्यकता तब होती है जब: अन्य है :values',
    'required_unless' => ':attribute फ़ील्ड की आवश्यकता तब तक होती है जब तक कि :other में नहीं है :values',
    'required_with' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद है',
    'required_with_all' => ':attribute फ़ील्ड की आवश्यकता तब होती है जब :values मौजूद होते हैं',
    'required_without' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद नहीं है',
    'required_without_all' => ':attribute फ़ील्ड की आवश्यकता तब होती है जब कोई भी :values मौजूद नहीं होता है',
    'same' => ':attribute और :other का मिलान होना चाहिए।',
    'size' => [
        'numeric' => ':attribute होनी चाहिए :size',
        'file' => ':attribute होना चाहिए।:size किलोबाइट',
        'string' => ':attribute होना चाहिए :size वर्ण',
        'array' => ':attribute में :size आइटम होना चाहिए.',
    ],
    'starts_with' => ':attribute निम्नलिखित में से किसी एक के साथ शुरू होना चाहिए. :values',
    'string' => ':attribute स्ट्रिंग होना चाहिए',
    'timezone' => ':attribute का एक वैध क्षेत्र होना चाहिए',
    'unique' => ':attribute पहले ही ली जा चुकी है',
    'uploaded' => ':attribute अपलोड करने में विफल',
    'url' => ':attribute प्रारूप अमान्य है',
    'uuid' => ':attribute मान्य UUID होनी चाहिए',

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
            'rule-name' => 'सीमा शुल्क संदेश',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader-friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'not_empty' => ":attribute केवल स्थान की अनुमति नहीं देती है.",
    'not_exists' => ':attribute मौजूद नहीं है.',
    'no_space' => ":attribute में स्थान नहीं होना चाहिए",
    'not_match' => ':attribute हमारे रिकॉर्ड्स से मेल नहीं खा रही है.',
    'not_equal' => ':attribute और :other समान नहीं होनी चाहिए',
    'equal_to' => ':attribute और :other समान होना चाहिए',
    'lettersonly' => ':attribute में केवल अक्षर और स्थान हो सकते हैं.',
    'alpha_numeric' => ':attribute में केवल अक्षर, संख्या और स्थान हो सकते हैं',
    'image_dimentions' => ': गुण: का होना चाहिए  :min px :max px आयाम',
    'emoji_found'   =>  ':attribute में Emojis अनुमति नहीं है',

    // 'duration'      =>  'वीडियो की अवधि केवल 1 से 60 सेकंड के बीच होनी चाहिए',
    'duration'      =>  'वीडियो की अवधि 60 सेकंड से अधिक नहीं हो सकती है',
    'video'         =>  [
        'portrait'  =>  'वीडियो पोर्ट्रेट मोड में होना चाहिए',
        'landscape' =>  'वीडियो लैंडस्केप मोड में होना चाहिए',
    ],

    'attributes' => [
        "field"         =>  "विशेषता",
        "password"      =>  "पासवर्ड",
        "name"          =>  "नाम",
        "username"      =>  "उपयोगकर्ता नाम",
        "contact"       =>  "संपर्क करें",
        "country_id"    =>  "देश",
        "country"       =>  "देश",

        "start_age"     =>  "प्रारंभ आयु",
        "end_age"       =>  "अंतिम आयु",
        "public"        =>  [
            "email"     =>  "सार्वजनिक-ईमेल",
            "contact"   =>  "सार्वजनिक संपर्क",
        ],
        "description"   =>  "विवरण",
        "link"          =>  "लिंक",
        "city"          =>  "शहर",
        "remove_profile"    =>  "प्रोफ़ाइल_हटाना",
        "birthday"          =>  "जन्मदिन",
        "profile"           =>  "प्रोफ़ाइल",
        "old_password"      =>  "पुराना पासवर्ड",
        "token"             =>  "टोकन",
        "device_type"       =>  "उपकरण का प्रकार",
        "checksum"          =>  "चेकसम",
        "limit"             =>  "सीमा",
        "offset"            =>  "ऑफसेट",
        "type"              =>  "प्रकार",
        "category"          =>  "वर्ग",
        "thumbnail"         =>  "थंबनेल",
        "video"             =>  "वीडियो",
        "duration"          =>  "समयांतराल",
        "hashtags"          =>  "हैशटैग",
        "cause"             =>  "कारण",
        "state"             =>  "स्थिति",
        "id"                =>  "आईडी",
        "keyword"           =>  "संकेत शब्द",
        "device"            =>  "उपकरण",
        "udid"              =>  "udid",
        "user"              =>  "उपयोगकर्ता",
        "reported_user"     =>  "रिपोर्ट किया गया उपयोगकर्ता",
        "message"           =>  "संदेश",
        "user_id"           =>  "उपयोगकर्ता",

        // Custom Api Validation
        "contact_no"            =>  "संपर्क",
        "security_token"        =>  "सुरक्षा टोकन",
        "first_name"            =>  "पहला नाम",
        "last_name"             =>  "अंतिम नाम",
        "email"                 =>  "ईमेल",
        "country_code"          =>  "देश कोड",
        "birth_date"            =>  "जन्म दिन",
        "gender"                =>  "लिंग",
        "interest"              =>  "रुचि",
        "location"              =>  "स्थान",
        "interests"             =>  "रूचियाँ",
        "interests.*"           =>  "रूचियाँ",
        "language"              =>  "भाषा",
        "profile_photo"         =>  "प्रोफाइल फोटो",
        "images"                =>  "तस्वीरें",
        "videos"                =>  "वीडियो",
        "videos.*"              =>  "वीडियो",
        "search"                =>  "तलाश",
        "image"                 =>  "छवि",
        "image_path"            =>  "छवि पथ",
        "Twilio Api Key"        =>  "ट्विलियो एपीआई कुंजी",
        "Twilio Access Token"   =>  "ट्विलियो एक्सेस टोकन",
        "api_key"               =>  "एपीआई कुंजी",
        "api_secret"            =>  "एपीआई सीक्रेट",
        "room_name"             =>  "कमरे का नाम",
        "identity"              =>  "पहचान",
        "time_line"             =>  "समय रेखा"
    ],
];
