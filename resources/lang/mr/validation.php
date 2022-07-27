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

    'accepted' => ':attribute स्वीकारणे आवश्यक आहे',
    'active_url' => ':attribute ही वैध URL नाही.',
    'after' => ':attribute :date नंतरची तारीख असणे आवश्यक आहे.',
    'after_or_equal' => ':attribute ही :date नंतरची किंवा बरोबरीची तारीख असणे आवश्यक आहे.',
    'alpha' => ':attribute मध्ये फक्त अक्षरे असू शकतात.',
    'alpha_dash' => ':attribute मध्ये फक्त अक्षरे, संख्या, डॅश आणि अंडरस्कोअर असू शकतात.',
    'alpha_num' => ':attribute मध्ये फक्त अक्षरे आणि संख्या असू शकतात.',
    'array' => ':attribute अॅरे असणे आवश्यक आहे.',
    'before' => ':attribute :date पूर्वीची तारीख असणे आवश्यक आहे.',
    'before_or_equal' => ':attribute ही :date च्या आधी किंवा समान तारीख असणे आवश्यक आहे.',
    'between' => [
        'numeric' => ':attribute :min आणि :max दरम्यान असणे आवश्यक आहे.',
        'file' => ':attribute :min आणि :max kilobytes मधील असणे आवश्यक आहे.',
        'string' => ':attribute :min आणि :max वर्णांमध्ये असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये :min आणि :max आयटम दरम्यान असणे आवश्यक आहे.',
    ],
    'boolean' => ':attribute फील्ड सत्य किंवा असत्य असणे आवश्यक आहे.',
    'confirmed' => ':attribute पुष्टीकरण जुळत नाही.',
    'date' => ':attribute ही वैध तारीख नाही.',
    'date_equals' => ':attribute ही :date सारखी तारीख असणे आवश्यक आहे.',
    'date_format' => ':attribute format :format शी जुळत नाही.',
    'different' => ':attribute आणि :other वेगळे असणे आवश्यक आहे.',
    'digits' => ':attribute :digits अंक असणे आवश्यक आहे.',
    'digits_between' => ':attribute :min आणि :max अंकांमध्ये असणे आवश्यक आहे.',
    'dimensions' => ':attribute मध्ये अवैध प्रतिमा परिमाण आहेत.',
    'distinct' => ':attribute फील्डमध्ये डुप्लिकेट मूल्य आहे.',
    'email' => ':attribute एक वैध ईमेल पत्ता असणे आवश्यक आहे.',
    'ends_with' => ':attribute खालीलपैकी एकाने समाप्त होणे आवश्यक आहे: :values',
    'exists' => 'निवडलेले :attribute अवैध आहे.',
    'file' => ':attribute फाइल असणे आवश्यक आहे.',
    'filled' => ':attribute फील्डमध्ये मूल्य असणे आवश्यक आहे.',
    'gt' => [
        'numeric' => ':attribute :value पेक्षा मोठी असणे आवश्यक आहे.',
        'file' => ':attribute :value kilobytes पेक्षा जास्त असणे आवश्यक आहे.',
        'string' => ':attribute :value वर्णांपेक्षा मोठी असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये :value आयटम पेक्षा जास्त असणे आवश्यक आहे.',
    ],
    'gte' => [
        'numeric' => ':attribute :value जास्त किंवा समान असणे आवश्यक आहे.',
        'file' => ':attribute :value किलोबाइट्सपेक्षा जास्त किंवा समान असणे आवश्यक आहे.',
        'string' => ':attribute :value वर्णांपेक्षा मोठे किंवा समान असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये :value आयटम किंवा अधिक असणे आवश्यक आहे.',
    ],
    'image' => ':attribute ही प्रतिमा असणे आवश्यक आहे.',
    'in' => 'निवडलेले :attribute अवैध आहे.',
    'in_array' => ':attribute फील्ड :other मध्ये अस्तित्वात नाही.',
    'integer' => ':attribute पूर्णांक असणे आवश्यक आहे.',
    'ip' => ':attribute हा वैध IP पत्ता असणे आवश्यक आहे.',
    'ipv4' => ':attribute हा वैध IPv4 पत्ता असणे आवश्यक आहे.',
    'ipv6' => ':attribute हा वैध IPv6 पत्ता असणे आवश्यक आहे.',
    'json' => ':attribute एक वैध JSON स्ट्रिंग असणे आवश्यक आहे.',
    'lt' => [
        'numeric' => ':attribute :value पेक्षा कमी असणे आवश्यक आहे.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => ':attribute :value kilobytes पेक्षा कमी असणे आवश्यक आहे.',
        'file' => ':attribute :value किलोबाइट पेक्षा कमी किंवा समान असणे आवश्यक आहे.',
        'string' => ':attribute :value वर्णांपेक्षा कमी किंवा समान असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये :value आयटम पेक्षा जास्त नसावेत.',
    ],
    'max' => [
        'numeric' => ':attribute :max पेक्षा जास्त असू शकत नाही.',
        'file' => ':attribute :max kilobytes पेक्षा जास्त असू शकत नाही.',
        'string' => ':attribute :max वर्णांपेक्षा जास्त असू शकत नाही.',
        'array' => ':attribute मध्ये :अधिकतम आयटम असू शकत नाहीत.',
    ],
    'mimes' => ':attribute ही प्रकारची फाइल असणे आवश्यक आहे: :values.',
    'mimetypes' => ':attribute ही प्रकारची फाइल असणे आवश्यक आहे: :values.',
    'min' => [
        'numeric' => ':attribute किमान :min असणे आवश्यक आहे.',
        'file' => ':attribute किमान :min किलोबाइट्स असणे आवश्यक आहे.',
        'string' => ':attribute किमान :min वर्णांची असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये किमान :min आयटम असणे आवश्यक आहे.',
    ],
    'not_in' => 'निवडलेले :attribute अवैध आहे.',
    'not_regex' => ':attribute स्वरूप अवैध आहे.',
    'numeric' => ':attribute ही संख्या असणे आवश्यक आहे.',
    'present' => ':attribute फील्ड उपस्थित असणे आवश्यक आहे.',
    'regex' => ':attribute स्वरूप अवैध आहे.',
    'required' => ':attribute फील्ड आवश्यक आहे.',
    'required_if' => 'जेव्हा :other :value असेल तेव्हा :attribute फील्ड आवश्यक आहे.',
    'required_unless' => ':attribute फील्ड आवश्यक आहे जोपर्यंत :other :values मध्ये नाही.',
    'required_with' => 'जेव्हा :values असते तेव्हा :attribute फील्ड आवश्यक असते.',
    'required_with_all' => 'जेव्हा :values असतात तेव्हा :attribute फील्ड आवश्यक असते.',
    'required_without' => 'जेव्हा :values नसतात तेव्हा :attribute फील्ड आवश्यक असते.',
    'required_without_all' => ':attribute फील्ड आवश्यक असते जेव्हा :values काहीही उपस्थित नसते.',
    'same' => ':attribute आणि :इतर जुळले पाहिजेत.',
    'size' => [
        'numeric' => ':attribute :आकार असणे आवश्यक आहे.',
        'file' => ':attribute :आकार किलोबाइट्स असणे आवश्यक आहे.',
        'string' => ':attribute :आकाराचे वर्ण असणे आवश्यक आहे.',
        'array' => ':attribute मध्ये :आकार आयटम असणे आवश्यक आहे.',
    ],
    'starts_with' => ':attribute खालीलपैकी एकाने सुरू होणे आवश्यक आहे: :values',
    'string' => ':attribute ही स्ट्रिंग असणे आवश्यक आहे.',
    'timezone' => ':attribute एक वैध झोन असणे आवश्यक आहे.',
    'unique' => ':attribute आधीच घेतली गेली आहे.',
    'uploaded' => ':attribute अपलोड करण्यात अयशस्वी.',
    'url' => ':attribute स्वरूप अवैध आहे.',
    'uuid' => ':attribute वैध UUID असणे आवश्यक आहे.',

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

    'not_empty' => ":attribute फक्त स्पेसला परवानगी देत नाही.",
    'not_exists' => ':attribute अस्तित्वात नाही.',
    'no_space' => ":attribute मध्ये जागा नसावी.",
    'not_match' => ':attribute आमच्या रेकॉर्डशी जुळत नाही.',
    'not_equal' => ':attribute आणि :इतर समान नसावेत',
    'equal_to' => ':attribute आणि :इतर समान असणे आवश्यक आहे',
    'lettersonly' => ':attribute मध्ये फक्त अक्षरे आणि स्पेस असू शकतात.',
    'alpha_numeric' => ':attribute मध्ये फक्त अक्षरे, संख्या आणि स्पेस असू शकतात.',
    'image_dimentions' => ':attribute :min px :max px परिमाणे असणे आवश्यक आहे.',
    'emoji_found'   =>  ':attribute मध्ये इमोजींना परवानगी नाही',
    
    // 'duration'      =>  'The duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'व्हिडिओचा कालावधी 60 सेकंदांपेक्षा जास्त नसावा.',
    'video'         =>  [
        'portrait'  =>  'व्हिडिओ पोर्ट्रेट मोडमध्ये असणे आवश्यक आहे',
        'landscape' =>  'व्हिडिओ लँडस्केप मोडमध्ये असणे आवश्यक आहे',
    ],

    'attributes' => [
        "field"         =>  "फील्ड",
        "password"      =>  "पासवर्ड",
        "name"          =>  "नाव",
        "username"      =>  "वापरकर्तानाव",
        "contact"       =>  "संपर्क",
        "country_id"    =>  "देश",
        "country"       =>  "देश",
        "start_age"     =>  "सुरुवातीचे वय",
        "end_age"       =>  "शेवटचे वय",
        "public"        =>  [
            "email"     =>  "ईमेल",
            "contact"   =>  "संपर्क",
        ],
        "description"   =>  "वर्णन",
        "link"          =>  "दुवा",
        "city"          =>  "शहर",
        "remove_profile"    =>  "प्रोफाइल काढा",
        "birthday"          =>  "वाढदिवस",
        "profile"           =>  "प्रोफाइल",
        "old_password"      =>  "जुना पासवर्ड",
        "token"             =>  "टोकन",
        "device_type"       =>  "डिव्हाइस प्रकार",
        "checksum"          =>  "चेकसम",
        "limit"             =>  "मर्यादा",
        "offset"            =>  "ऑफसेट",
        "type"              =>  "प्रकार",
        "category"          =>  "श्रेणी",
        "thumbnail"         =>  "लघुप्रतिमा",
        "video"             =>  "व्हिडिओ",
        "duration"          =>  "कालावधी",
        "hashtags"          =>  "हॅशटॅग",
        "cause"             =>  "कारण",
        "state"             =>  "राज्य",
        "id"                =>  "आयडी",
        "keyword"           =>  "कीवर्ड",
        "device"            =>  "साधन",
        "udid"              =>  "udid",
        "user"              =>  "वापरकर्ता",
        "reported_user"     =>  "नोंदवलेला वापरकर्ता",
        "message"           =>  "संदेश",
        "user_id"           =>  "वापरकर्ता",

        // Custom Resource Validation
        "contact_no"            =>  "संपर्क क्रमांक",
        "security_token"        =>  "सुरक्षा टोकन",
        "first_name"            =>  "पहिले नाव",
        "last_name"             =>  "आडनाव",
        "full_name"             =>  "पूर्ण नाव",
        "email"                 =>  "ईमेल",
        "birth_date"            =>  "जन्मदिनांक",
        "gender"                =>  "लिंग",
        "interest"              =>  "व्याज",
        "location"              =>  "स्थान",
        "location_id"           =>  "स्थान",
        "language"              =>  "इंग्रजी",
        "languages"             =>  "भाषा",
        "profile_photo"         =>  "प्रोफाइल फोटो",
        "country_code"          =>  "राष्ट्र संकेतांक",
        "room_name"             =>  "खोलीचे नाव",
        "room"                  =>  "खोली",
        "room_id"               =>  "खोली",
        "participant_id"        =>  "सहभागी",
        "search"                =>  "शोध",
        "distance"              =>  "अंतर",
        "parent_id"             =>  "पालक",
        "level"                 =>  "पातळी",
        "attribute"             =>  "विशेषता",
        "interests"             =>  "स्वारस्ये",
        "interests.*"           =>  "स्वारस्ये",
        "images"                =>  "प्रतिमा",
        "videos"                =>  "व्हिडिओ",
        "videos.*"              =>  "व्हिडिओ",
        "image"                 =>  "प्रतिमा",
        "image_path"            =>  "प्रतिमा मार्ग",
        "api_key"               =>  "api की",
        "api_secret"            =>  "api गुप्त",
        "sid"                   =>  "sid",
        "push_id"               =>  "पुश आयडी",
        "identity"              =>  "ओळख",
        "time_line"             =>  "वेळ ओळ",
        "facebook_id"           =>  "फेसबुक आयडी",
        "google_id"             =>  "गुगल आयडी",
        "apple_id"              =>  "ऍपल आयडी",
        "latitude"              =>  "अक्षांश",
        "longitude"             =>  "रेखांश",
        "reason"                =>  "कारण",
        "start_time"            =>  "सुरवातीची वेळ",
        "end_time"              =>  "समाप्तीचा कालावधी",
        "remaining_time"        =>  "उर्वरित वेळ",
        "version"               =>  "आवृत्ती",
        "os"                    =>  "os",
        "app_version"           =>  "अॅप आवृत्ती",
        "status"                =>  "स्थिती",
        "about_me"              =>  "माझ्याबद्दल",
        "fav_movie"             =>  "आवडता चित्रपट",
        "personalities"         =>  "व्यक्तिमत्त्वे",
        "remove_personalities"  =>  "व्यक्तिमत्त्व काढून टाका",
        "education"             =>  "शिक्षण",
        "university_college"    =>  "विद्यापीठ आणि महाविद्यालय",
        "profession"            =>  "व्यवसाय",
        "religion"              =>  "धर्म",
        "relationship_status"   =>  "नातेसंबंधाची सद्यस्थिती",
        "i_am_here"             =>  "मी इथे आहे",
        "food_preference"       =>  "अन्न प्राधान्य",
        "drinking"              =>  "मद्यपान",
        "smoking"               =>  "धूम्रपान",
        "star_sign"             =>  "स्टार चिन्ह",
        "community"             =>  "समुदाय",
        "old_profile_photo"     =>  "जुना प्रोफाईल फोटो",
        "old_images"            =>  "जुन्या प्रतिमा",
        "voice"                 =>  "आवाज",
        "voice_answer"          =>  "आवाज उत्तर",
        "remove_voice"          =>  "आवाज काढा",
        "remove_video"          =>  "व्हिडिओ काढा",
        "remove_image"          =>  "प्रतिमा काढा",
        "remove_interests"      =>  "स्वारस्ये काढून टाका",
        "image_sequence"        =>  "प्रतिमा क्रम",
        "file"                  =>  "फाइल",
        "message_id"            =>  "संदेश",
    ],
];
