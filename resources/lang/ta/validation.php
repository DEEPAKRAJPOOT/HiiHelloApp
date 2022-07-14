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

    'accepted' => 'தி :attribute ஏற்றுக்கொள்ளப்பட வேண்டும்.',
    'active_url' => 'தி :attribute சரியான URL அல்ல.',
    'after' => 'தி :attribute அதற்குப் பின் தேதியாக இருக்க வேண்டும் :date.',
    'after_or_equal' => 'தி :attribute அதற்குப் பின் அல்லது அதற்கு சமமான தேதியாக இருக்க வேண்டும் :date.',
    'alpha' => 'தி :attribute எழுத்துக்கள் மட்டுமே இருக்கலாம்.',
    'alpha_dash' => 'தி :attribute எழுத்துக்கள், எண்கள், கோடுகள் மற்றும் அடிக்கோடுகள் மட்டுமே இருக்கலாம்.',
    'alpha_num' => 'தி :attribute எழுத்துக்கள் மற்றும் எண்கள் மட்டுமே இருக்கலாம்.',
    'array' => 'தி :attribute ஒரு வரிசையாக இருக்க வேண்டும்.',
    'before' => 'தி :attribute முன் தேதியாக இருக்க வேண்டும் :date.',
    'before_or_equal' => 'தி :attribute அதற்கு முன் அல்லது அதற்கு சமமான தேதியாக இருக்க வேண்டும் :date.',
    'between' => [
        'numeric' => 'தி :attribute இடையே இருக்க வேண்டும் :min மற்றும் :max.',
        'file' => 'தி :attribute இடையே இருக்க வேண்டும் :min மற்றும் :max கிலோபைட்டுகள்.',
        'string' => 'தி :attribute இடையே இருக்க வேண்டும் :min மற்றும் :max பாத்திரங்கள்.',
        'array' => 'தி :attribute இடையே இருக்க வேண்டும் :min மற்றும் :max பொருட்களை.',
    ],
    'boolean' => 'தி :attribute புலம் உண்மையாகவோ அல்லது பொய்யாகவோ இருக்க வேண்டும்.',
    'confirmed' => 'தி :attribute உறுதிப்படுத்தல் பொருந்தவில்லை.',
    'date' => 'தி :attribute சரியான தேதி அல்ல.',
    'date_equals' => 'தி :attribute சமமான தேதியாக இருக்க வேண்டும் :date.',
    'date_format' => 'தி :attribute வடிவத்துடன் பொருந்தவில்லை :format.',
    'different' => 'தி :attribute மற்றும் :other வித்தியாசமாக இருக்க வேண்டும்.',
    'digits' => 'தி :attribute இருக்க வேண்டும் :digits இலக்கங்கள்.',
    'digits_between' => 'தி :attribute இடையே இருக்க வேண்டும் :min மற்றும் :max இலக்கங்கள்.',
    'dimensions' => 'தி :attribute தவறான பட பரிமாணங்களைக் கொண்டுள்ளது.',
    'distinct' => 'தி :attribute புலத்திற்கு நகல் மதிப்பு உள்ளது.',
    'email' => 'தி :attribute ஒரு செல்லுபடியாகும் மின்னஞ்சல் முகவரியை இருக்க வேண்டும்.',
    'ends_with' => 'தி :attribute பின்வருவனவற்றில் ஒன்றோடு முடிக்க வேண்டும்: :values',
    'exists' => 'தேர்ந்தெடுக்கப்பட்ட :attribute செல்லாது.',
    'file' => 'தி :attribute ஒரு கோப்பாக இருக்க வேண்டும்',
    'filled' => 'தி :attribute புலத்திற்கு மதிப்பு இருக்க வேண்டும்.',
    'gt' => [
        'numeric' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value.',
        'file' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value கிலோபைட்டுகள்.',
        'string' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value பாத்திரங்கள்.',
        'array' => 'தி :attribute அதிகமாக இருக்க வேண்டும் :value பொருட்களை.',
    ],
    'gte' => [
        'numeric' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value.',
        'file' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value கிலோபைட்டுகள்.',
        'string' => 'தி :attribute விட அதிகமாக இருக்க வேண்டும் :value பாத்திரங்கள்.',
        'array' => 'தி :attribute அதிகமாக இருக்க வேண்டும் :value பொருட்களை.',
    ],
    'image' => 'தி :attribute ஒரு படமாக இருக்க வேண்டும்.',
    'in' => 'தேர்ந்தெடுக்கப்பட்ட :attribute செல்லாது.',
    'in_array' => 'தி :attribute புலத்தில் இல்லை :other.',
    'integer' => 'தி :attribute ஒரு முழு எண்ணாக இருக்க வேண்டும்.',
    'ip' => 'தி :attribute சரியான IP முகவரியாக இருக்க வேண்டும்.',
    'ipv4' => 'தி :attribute சரியான IPv4 முகவரியாக இருக்க வேண்டும்.',
    'ipv6' => 'தி :attribute சரியான IPv6 முகவரியாக இருக்க வேண்டும்.',
    'json' => 'தி :attribute சரியான JSON சரமாக இருக்க வேண்டும்.',
    'lt' => [
        'numeric' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் :value.',
        'file' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் :value கிலோபைட்டுகள்.',
        'string' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் :value பாத்திரங்கள்.',
        'array' => 'தி :attribute குறைவாக இருக்க வேண்டும் :value பொருட்களை.',
    ],
    'lte' => [
        'numeric' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் அல்லது சமம் :value.',
        'file' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் அல்லது சமம் :value கிலோபைட்டுகள்.',
        'string' => 'தி :attribute விட குறைவாக இருக்க வேண்டும் அல்லது சமம் :value பாத்திரங்கள்.',
        'array' => 'தி :attribute அதிகமாக இருக்கக்கூடாது :value பொருட்களை.',
    ],
    'max' => [
        'numeric' => 'தி :attribute விட அதிகமாக இருக்காது :max.',
        'file' => 'தி :attribute விட அதிகமாக இருக்காது :max கிலோபைட்டுகள்.',
        'string' => 'தி :attribute விட அதிகமாக இருக்காது :max பாத்திரங்கள்.',
        'array' => 'தி :attribute அதிகமாக இல்லாமல் இருக்கலாம் :max பொருட்களை.',
    ],
    'mimes' => 'தி :attribute ஒரு வகை கோப்பாக இருக்க வேண்டும்: :values.',
    'mimetypes' => 'தி :attribute ஒரு வகை கோப்பாக இருக்க வேண்டும்: :values.',
    'min' => [
        'numeric' => 'தி :attribute குறைந்தபட்சம் இருக்க வேண்டும் :min.',
        'file' => 'தி :attribute குறைந்தபட்சம் இருக்க வேண்டும் :min கிலோபைட்டுகள்.',
        'string' => 'தி :attribute குறைந்தபட்சம் இருக்க வேண்டும் :min பாத்திரங்கள்.',
        'array' => 'தி :attribute குறைந்தபட்சம் இருக்க வேண்டும் :min பொருட்களை.',
    ],
    'not_in' => 'தேர்ந்தெடுக்கப்பட்ட :attribute செல்லாது.',
    'not_regex' => 'தி :attribute வடிவம் தவறானது.',
    'numeric' => 'தி :attribute ஒரு எண்ணாக இருக்க வேண்டும்.',
    'present' => 'தி :attribute புலம் இருக்க வேண்டும்.',
    'regex' => 'தி :attribute வடிவம் தவறானது.',
    'required' => 'தி :attribute இந்த பகுதி அவசியமானது.',
    'required_if' => 'தி :attribute புலம் எப்போது தேவைப்படுகிறது :other இருக்கிறது :value.',
    'required_unless' => 'தி :attribute தவிர புலம் தேவைப்படுகிறது :other உள்ளது :values.',
    'required_with' => 'தி :attribute புலம் எப்போது தேவைப்படுகிறது :values உள்ளது.',
    'required_with_all' => 'தி :attribute புலம் எப்போது தேவைப்படுகிறது :values உள்ளன.',
    'required_without' => 'தி :attribute புலம் எப்போது தேவைப்படுகிறது :values தற்போது இல்லை.',
    'required_without_all' => 'தி :attribute எதுவும் இல்லாத போது புலம் தேவைப்படுகிறது :values உள்ளன.',
    'same' => 'தி :attribute மற்றும் :other பொருந்தியாக வேண்டும்.',
    'size' => [
        'numeric' => 'தி :attribute இருக்க வேண்டும் :size.',
        'file' => 'தி :attribute இருக்க வேண்டும் :size கிலோபைட்டுகள்.',
        'string' => 'தி :attribute இருக்க வேண்டும் :size பாத்திரங்கள்.',
        'array' => 'தி :attribute கொண்டிருக்க வேண்டும் :size பொருட்களை.',
    ],
    'starts_with' => 'தி :attribute பின்வருவனவற்றில் ஒன்றைத் தொடங்க வேண்டும்: :values',
    'string' => 'தி :attribute ஒரு சரமாக இருக்க வேண்டும்.',
    'timezone' => 'தி :attribute சரியான மண்டலமாக இருக்க வேண்டும்.',
    'unique' => 'தி :attribute ஏற்கனவே எடுத்துக்கொள்ளப்பட்டது.',
    'uploaded' => 'தி :attribute பதிவேற்றம் செய்ய முடியவில்லை.',
    'url' => 'தி :attribute வடிவம் தவறானது.',
    'uuid' => 'தி :attribute சரியான UUID ஆக இருக்க வேண்டும்.',

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

    'not_empty' => "தி :attribute இடத்தை மட்டும் அனுமதிப்பதில்லை.",
    'not_exists' => 'தி :attribute இல்லை.',
    'no_space' => "தி :attribute இடம் இருக்கக்கூடாது.",
    'not_match' => 'தி :attribute எங்கள் பதிவுகளுடன் பொருந்தவில்லை.',
    'not_equal' => 'தி :attribute மற்றும் :other ஒரே மாதிரியாக இருக்கக்கூடாது',
    'equal_to' => 'தி :attribute மற்றும் :other அதே இருக்க வேண்டும்',
    'lettersonly' => 'தி :attribute எழுத்துக்கள் மற்றும் இடைவெளிகள் மட்டுமே இருக்கலாம்.',
    'alpha_numeric' => 'தி :attribute எழுத்துக்கள், எண்கள் மற்றும் இடைவெளிகள் மட்டுமே இருக்கலாம்.',
    'image_dimentions' => 'தி :attribute இருக்க வேண்டும் :min x :max px பரிமாணங்கள்.',
    'emoji_found'   =>  'எமோஜிகள் உள்ளே அனுமதிக்கப்படவில்லை :attribute',

    // 'duration'      =>  'The duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'வீடியோவின் கால அளவு 60 வினாடிகளுக்கு மேல் இருக்கக்கூடாது.',
    'video'         =>  [
        'portrait'  =>  'வீடியோ போர்ட்ரெய்ட் பயன்முறையில் இருக்க வேண்டும்',
        'landscape' =>  'வீடியோ லேண்ட்ஸ்கேப் பயன்முறையில் இருக்க வேண்டும்',
    ],

    'attributes' => [
        "field"         =>  "களம்",
        "password"      =>  "கடவுச்சொல்",
        "name"          =>  "பெயர்",
        "username"      =>  "பயனர் பெயர்",
        "contact"       =>  "தொடர்பு",
        "country_id"    =>  "நாடு",
        "country"       =>  "நாடு",
        "start_age"     =>  "தொடக்க வயது",
        "end_age"       =>  "முடிவு வயது",
        "public"        =>  [
            "email"     =>  "மின்னஞ்சல்",
            "contact"   =>  "தொடர்பு",
        ],
        "description"   =>  "விளக்கம்",
        "link"          =>  "இணைப்பு",
        "city"          =>  "நகரம்",
        "remove_profile"    =>  "சுயவிவரத்தை அகற்று",
        "birthday"          =>  "பிறந்தநாள்",
        "profile"           =>  "சுயவிவரம்",
        "old_password"      =>  "பழைய கடவுச்சொல்",
        "token"             =>  "டோக்கன்",
        "device_type"       =>  "சாதன வகை",
        "checksum"          =>  "செக்சம்",
        "limit"             =>  "வரம்பு",
        "offset"            =>  "ஆஃப்செட்",
        "type"              =>  "வகை",
        "category"          =>  "வகை",
        "thumbnail"         =>  "சிறுபடம்",
        "video"             =>  "வீடியோ",
        "duration"          =>  "காலம்",
        "hashtags"          =>  "ஹேஷ்டேக்குகள்",
        "cause"             =>  "காரணம்",
        "state"             =>  "நிலை",
        "id"                =>  "id",
        "keyword"           =>  "திறவுச்சொல்",
        "device"            =>  "சாதனம்",
        "udid"              =>  "udid",
        "user"              =>  "பயனர்",
        "reported_user"     =>  "அறிக்கையிடப்பட்ட பயனர்",
        "message"           =>  "செய்தி",
        "user_id"           =>  "பயனர்",

        // Custom Resource Validation
        "contact_no"            =>  "தொடர்பு எண்",
        "security_token"        =>  "பாதுகாப்பு டோக்கன்",
        "first_name"            =>  "முதல் பெயர்",
        "last_name"             =>  "கடைசி பெயர்",
        "full_name"             =>  "முழு பெயர்",
        "email"                 =>  "மின்னஞ்சல்",
        "birth_date"            =>  "பிறந்த தேதி",
        "gender"                =>  "பாலினம்",
        "interest"              =>  "வட்டி",
        "location"              =>  "இடம்",
        "location_id"           =>  "இடம்",
        "language"              =>  "மொழி",
        "languages"             =>  "மொழிகள்",
        "profile_photo"         =>  "சுயவிவர புகைப்படம்",
        "country_code"          =>  "நாட்டின் குறியீடு",
        "room_name"             =>  "அறையின் பெயர்",
        "room"                  =>  "அறை",
        "room_id"               =>  "அறை",
        "participant_id"        =>  "பங்கேற்பாளர்",
        "search"                =>  "தேடல்",
        "distance"              =>  "தொலைவு",
        "parent_id"             =>  "பெற்றோர்",
        "level"                 =>  "நிலை",
        "attribute"             =>  "பண்பு",
        "interests"             =>  "ஆர்வங்கள்",
        "interests.*"           =>  "ஆர்வங்கள்",
        "images"                =>  "படங்கள்",
        "videos"                =>  "வீடியோக்கள்",
        "videos.*"              =>  "வீடியோக்கள்",
        "image"                 =>  "படம்",
        "image_path"            =>  "பட பாதை",
        "api_key"               =>  "api விசை",
        "api_secret"            =>  "api ரகசியம்",
        "sid"                   =>  "sid",
        "push_id"               =>  "புஷ் ஐடி",
        "identity"              =>  "அடையாளம்",
        "time_line"             =>  "நேரக் கோடு",
        "facebook_id"           =>  "facebook id",
        "google_id"             =>  "google id",
        "apple_id"              =>  "ஆப்பிள் ஐடி",
        "latitude"              =>  "அட்சரேகை",
        "longitude"             =>  "தீர்க்கரேகை",
        "reason"                =>  "காரணம்",
        "start_time"            =>  "தொடக்க நேரம்",
        "end_time"              =>  "முடிவு நேரம்",
        "remaining_time"        =>  "மீதமுள்ள நேரம்",
        "version"               =>  "பதிப்பு",
        "os"                    =>  "os",
        "app_version"           =>  "பயன்பாட்டு பதிப்பு",
        "status"                =>  "நிலை",
        "about_me"              =>  "என்னைப் பற்றி",
        "fav_movie"             =>  "பிடித்த திரைப்படம்",
        "personalities"         =>  "ஆளுமைகள்",
        "remove_personalities"  =>  "ஆளுமைகளை அகற்று",
        "education"             =>  "கல்வி",
        "university_college"    =>  "பல்கலைக்கழகம் மற்றும் கல்லூரி",
        "profession"            =>  "தொழில்",
        "religion"              =>  "மதம்",
        "relationship_status"   =>  "உறவு நிலை",
        "i_am_here"             =>  "நான் இங்கே இருக்கிறேன்",
        "food_preference"       =>  "உணவு விருப்பம்",
        "drinking"              =>  "குடி",
        "smoking"               =>  "புகைபிடித்தல்",
        "star_sign"             =>  "நட்சத்திர அடையாளம்",
        "community"             =>  "சமூகம்",
        "old_profile_photo"     =>  "பழைய சுயவிவர புகைப்படம்",
        "old_images"            =>  "பழைய படங்கள்",
        "voice"                 =>  "குரல்",
        "voice_answer"          =>  "குரல் பதில்",
        "remove_voice"          =>  "குரலை அகற்று",
        "remove_video"          =>  "வீடியோவை அகற்று",
        "remove_image"          =>  "படத்தை அகற்று",
        "remove_interests"      =>  "ஆர்வங்களை அகற்று",
        "image_sequence"        =>  "பட வரிசை",
        "file"                  =>  "கோப்பு",
    ],
];
