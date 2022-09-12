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

    'accepted' => 'આ :attribute સ્વિકાર્વમા આવ્યુ.',
    'active_url' => 'આ :attribute માન્ય URL નથી.',
    'after' => 'આ :attribute :date પછીની તારીખ હોવી જોઈએ.',
    'after_or_equal' => 'આ :attribute :date તારીખ પછીની અથવા તેની સમાન હોવી જોઈએ.',
    'alpha' => 'આ :attribute માત્ર અક્ષરો સમાવી શકે છે.',
    'alpha_dash' => 'આ :attribute તેમાં માત્ર અક્ષરો, સંખ્યાઓ, ડેશ અને અન્ડરસ્કોર હોઈ શકે છે.',
    'alpha_num' => 'આ :attribute માત્ર અક્ષરો અને સંખ્યાઓ સમાવી શકે છે.',
    'array' => 'આ :attribute એરે હોવી જોઈએ.',
    'before' => 'આ :attribute :date પહેલાની તારીખ હોવી જોઈએ.',
    'before_or_equal' => 'આ :attribute :date તેની પહેલા અથવા તેની સમાન તારીખ હોવી આવશ્યક છે.',
    'between' => [
        'numeric' => 'આ :attribute :min અને :max વચ્ચે હોવું જોઈએ.',
        'file' => 'આ :attribute :min અને :max કિલોબાઈટ વચ્ચે હોવું જોઈએ.',
        'string' => 'આ :attribute :min અને :max સ્ટ્રિંગ વચ્ચે હોવું જોઈએ.',
        'array' => 'આ :attribute :min અને :max એરે વચ્ચે હોવું જોઈએ .',
    ],
    'boolean' => 'આ :attribute ક્ષેત્ર સાચું કે ખોટું હોવું જોઈએ.',
    'confirmed' => 'આ :attribute પુષ્ટિ મેળ ખાતી નથી.',
    'date' => 'આ :attribute માન્ય તારીખ નથી.',
    'date_equals' => 'આ :attribute :date ની સમાન તારીખ હોવી જોઈએ.',
    'date_format' => 'આ :attribute :format ફોર્મેટ સાથે મેળ ખાતું નથી.',
    'different' => 'આ :attribute અને :other અલગ હોવું જોઈએ.',
    'digits' => 'આ :attribute :digits અંકો સાથે ચોક્ક્સ હોવુ જોઈએ.',
    'digits_between' => 'આ :attribute :min અને :max અંકો વચ્ચે હોવું જોઈએ.',
    'dimensions' => 'આ :attribute અમાન્ય છબી પરિમાણો છે.',
    'distinct' => 'આ :attribute ફીલ્ડમાં ડુપ્લિકેટ મૂલ્ય છે.',
    'email' => 'આ :attribute માન્ય ઇમેઇલ સરનામું હોવું આવશ્યક છે.',
    'ends_with' => 'આ :attribute નીચેનામાંથી એક સાથે સમાપ્ત થવું જોઈએ: :values',
    'exists' => 'પસંદ કરેલ :attribute અમાન્ય છે.',
    'file' => 'આ :attribute ફાઇલ હોવી જોઈએ.',
    'filled' => 'આ :attribute ફીલ્ડનું મૂલ્ય હોવું આવશ્યક છે.',
    'gt' => [
        'numeric' => 'આ :attribute :value કરતાં વધુ હોવી જોઈએ.',
        'file' => 'આ :attribute :value કિલોબાઈટ કરતાં વધુ હોવી જોઈએ.',
        'string' => 'આ :attribute :value સ્ટ્રિંગ કરતાં વધુ હોવી જોઈએ.',
        'array' => 'આ :attribute :value એરે કરતાં વધુ હોવી જોઈએ.',
    ],
    'gte' => [
        'numeric' => 'આ :attribute :value કરતાં વધુ અથવા સમાન હોવું જોઈએ.',
        'file' => 'આ :attribute :value કિલોબાઈટ કરતાં વધુ અથવા સમાન હોવું જોઈએ.',
        'string' => 'આ :attribute :value સ્ટ્રિંગ કરતાં વધુ અથવા સમાન હોવું જોઈએ.',
        'array' => 'આ :attribute :value એરે અથવા વધારે હોવી જ જોઈએ.',
    ],
    'image' => 'આ :attribute છબી હોવી જોઈએ.',
    'in' => 'પસંદ કરેલ :attribute અમાન્ય છે.',
    'in_array' => 'આ :attribute :other માં ક્ષેત્ર અસ્તિત્વમાં નથી.',
    'integer' => 'આ :attribute પૂર્ણાંક હોવો જોઈએ.',
    'ip' => 'આ :attribute માન્ય IP સરનામું હોવું આવશ્યક છે.',
    'ipv4' => 'આ :attribute માન્ય IPv4 સરનામું હોવું આવશ્યક છે.',
    'ipv6' => 'આ :attribute માન્ય IPv6 સરનામું હોવું આવશ્યક છે.',
    'json' => 'આ :attribute માન્ય JSON સ્ટ્રિંગ હોવી આવશ્યક છે.',
    'lt' => [
        'numeric' => 'આ :attribute :value કરતાં ઓછી હોવી જોઈએ.',
        'file' => 'આ :attribute :value કિલોબાઈટ કરતાં ઓછી હોવી જોઈએ.',
        'string' => 'આ :attribute :value સ્ટ્રિંગ કરતાં ઓછી હોવી જોઈએ.',
        'array' => 'આ :attribute :value એરે કરતાં ઓછી હોવી જોઈએ.',
    ],
    'lte' => [
        'numeric' => 'આ :attribute :value કરતાં ઓછી અથવા સમાન હોવી જોઈએ.',
        'file' => 'આ :attribute :value કિલોબાઈટ કરતાં ઓછી અથવા સમાન હોવી જોઈએ.',
        'string' => 'આ :attribute :value સ્ટ્રિંગ કરતાં ઓછી અથવા સમાન હોવી જોઈએ.',
        'array' => 'આ :attribute :value એરે કરતાં વધુ ન હોવી જોઈએ.',
    ],
    'max' => [
        'numeric' => 'આ :attribute :max કરતાં વધુ ન હોઈ શકે.',
        'file' => 'આ :attribute :max કિલોબાઈટ કરતાં વધુ ન હોઈ શકે.',
        'string' => 'આ :attribute :max સ્ટ્રિંગ કરતાં વધુ ન હોઈ શક.',
        'array' => 'આ :attribute :max એરે કરતાં વધુ ન હોઈ શકે.',
    ],
    'mimes' => 'આ :attribute પ્રકારની ફાઇલ હોવી આવશ્યક છે: :values.',
    'mimetypes' => 'આ :attribute પ્રકારની ફાઇલ હોવી આવશ્યક છે: :values.',
    'min' => [
        'numeric' => 'આ :attribute ઓછામાં ઓછું :min હોવું જોઈએ.',
        'file' => 'આ :attribute ઓછામાં ઓછું :min કિલોબાઈટ હોવું જોઈએ.',
        'string' => 'આ :attribute ઓછામાં ઓછું :min સ્ટ્રિંગ હોવું જોઈએ.',
        'array' => 'આ :attribute ઓછામાં ઓછું :min એરે હોવું જોઈએ.',
    ],
    'not_in' => 'પસંદ કરેલ :attribute અમાન્ય છે.',
    'not_regex' => 'આ :attribute ફોર્મેટ અમાન્ય છે.',
    'numeric' => 'આ :attribute સંખ્યા હોવી જોઈએ.',
    'present' => 'આ :attribute ક્ષેત્ર હાજર હોવું આવશ્યક છે.',
    'regex' => 'આ :attribute ફોર્મેટ અમાન્ય છે.',
    'required' => 'આ :attribute ક્ષેત્ર જરૂરી છે.',
    'required_if' => 'આ :attribute જ્યારે ક્ષેત્ર જરૂરી છે :other છે :value.',
    'required_unless' => 'આ :attribute ક્ષેત્ર જરૂરી છે સિવાય કે :other મા છે :values.',
    'required_with' => 'આ :attribute જ્યારે ક્ષેત્ર જરૂરી છે :values હાજર છે.',
    'required_with_all' => 'આ :attribute જ્યારે ક્ષેત્ર જરૂરી છે :values હાજર છે.',
    'required_without' => 'આ :attribute જ્યારે ક્ષેત્ર જરૂરી છે :values હાજર નથી.',
    'required_without_all' => 'આ :attribute ફીલ્ડ જરૂરી છે જ્યારે આમાંથી કોઈ :values નહીં હાજર છે.',
    'same' => 'આ :attribute અને :other મેળ ખાતો હોવો જોઈએ.',
    'size' => [
        'numeric' => 'આ :attribute :size ચોક્ક્સ હોવુ જોઈએ.',
        'file' => 'આ :attribute :size કિલોબાઈટ ચોક્ક્સ હોવુ જોઈએ.',
        'string' => 'આ :attribute :size સ્ટ્રિંગ ચોક્ક્સ હોવુ જોઈએ.',
        'array' => 'આ :attribute :size એરે સમાવી જોઈએ.',
    ],
    'starts_with' => 'આ :attribute નીચેનામાંથી એક સાથે શરૂ થવું જોઈએ: :values',
    'string' => 'આ :attribute શબ્દમાળા હોવી જોઈએ.',
    'timezone' => 'આ :attribute માન્ય ઝોન હોવો જોઈએ.',
    'unique' => 'આ :attribute પહેલેથી જ લેવામાં આવી છે.',
    'uploaded' => 'આ :attribute અપલોડ કરવામાં નિષ્ફળ.',
    'url' => 'આ :attribute ફોર્મેટ અમાન્ય છે.',
    'uuid' => 'આ :attribute માન્ય UUID હોવું આવશ્યક છે.',

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

    'not_empty' => "આ :attribute માત્ર જગ્યાને મંજૂરી આપતું નથી.",
    'not_exists' => 'આ :attribute અસ્તિત્વમાં નથી.',
    'no_space' => "આ :attribute જગ્યા હોવી જોઈએ નહીં.",
    'not_match' => 'આ :attribute અમારા રેકોર્ડ સાથે મેળ ખાતો નથી.',
    'not_equal' => 'આ :attribute અને :other સમાન હોવું જોઈએ નહીં',
    'equal_to' => 'આ :attribute અને :other સમાન હોવું જોઈએ',
    'lettersonly' => 'આ :attribute માત્ર અક્ષરો અને ખાલી જગ્યાઓ સમાવી શકે છે.',
    'alpha_numeric' => 'આ :attribute તેમાં માત્ર અક્ષરો, સંખ્યાઓ અને જગ્યાઓ હોઈ શકે છે.',
    'image_dimentions' => 'આ :attribute :min x :max px પરિમાણો ની હોવી જોઈએ.',
    'emoji_found'   =>  ':attribute ઇમોજીસને પ્રવેશની મંજૂરી નથી ',

    // 'duration'      =>  'વિડિયોનો સમયગાળો માત્ર 1 થી 60 સેકન્ડનો હોવો જોઈએ.',
    'duration'      =>  'વિડિયોનો સમયગાળો 60 સેકન્ડથી વધુ ન હોઈ શકે.',
    'video'         =>  [
        'portrait'  =>  'વિડિયો પોટ્રેટ મોડમાં હોવો જોઈએ',
        'landscape' =>  'વિડિયો લેન્ડસ્કેપ મોડમાં હોવો જોઈએ',
    ],

    'attributes' => [
        "field"         =>  "ક્ષેત્ર",
        "password"      =>  "પાસવર્ડ",
        "name"          =>  "નામ",
        "username"      =>  "વપરાશકર્તા નુ નામ",
        "contact"       =>  "સંપર્ક",
        "country_id"    =>  "દેશ",
        "country"       =>  "દેશ",
        "start_age"     =>  "શરૂઆતની ઉંમર",
        "end_age"       =>  "અંતિમ ઉંમર",
        "public"        =>  [
            "email"     =>  "ઇમેઇલ",
            "contact"   =>  "સંપર્ક",
        ],
        "description"   =>  "વર્ણન",
        "link"          =>  "લિંક",
        "city"          =>  "શહેર",
        "remove_profile"    =>  "પ્રોફાઇલ દૂર કરો",
        "birthday"          =>  "જન્મદિવસ",
        "profile"           =>  "પ્રોફાઇલ",
        "old_password"      =>  "જુનો પાસવર્ડ",
        "token"             =>  "ટોકન",
        "device_type"       =>  "ઉપકરણ ના પ્રકાર",
        "checksum"          =>  "ચેકસમ",
        "limit"             =>  "મર્યાદા",
        "offset"            =>  "ઓફસેટ",
        "type"              =>  "પ્રકાર",
        "category"          =>  "શ્રેણી",
        "thumbnail"         =>  "થંબનેલ",
        "video"             =>  "વિડિઓ",
        "duration"          =>  "સમયગાળો",
        "hashtags"          =>  "હેશટેગ્સ",
        "cause"             =>  "કારણ",
        "state"             =>  "રાજ્ય",
        "id"                =>  "આઈડી",
        "keyword"           =>  "કીવર્ડ",
        "device"            =>  "ઉપકરણ",
        "udid"              =>  "udid",
        "user"              =>  "વપરાશકર્તા",
        "reported_user"     =>  "જાણ કરેલ વપરાશકર્તા",
        "message"           =>  "સંદેશ",
        "user_id"           =>  "વપરાશકર્તા",

        // Custom Resource Validation
        "contact_no"            =>  "સંપર્ક નંબર",
        "security_token"        =>  "સુરક્ષા ટોકન",
        "first_name"            =>  "પ્રથમ નામ",
        "last_name"             =>  "છેલ્લું નામ",
        "full_name"             =>  "પૂરું નામ",
        "email"                 =>  "ઇમેઇલ",
        "birth_date"            =>  "જન્મતારીખ",
        "gender"                =>  "લિંગ",
        "interest"              =>  "રૂચિ",
        "location"              =>  "સ્થાન",
        "location_id"           =>  "સ્થાન",
        "language"              =>  "ભાષા",
        "languages"             =>  "ભાષાઓ",
        "profile_photo"         =>  "પ્રોફાઇલ ફોટો",
        "country_code"          =>  "દેશનો કોડ",
        "room_name"             =>  "રૂમનું નામ",
        "room"                  =>  "રૂમ",
        "room_id"               =>  "રૂમ",
        "participant_id"        =>  "સહભાગી",
        "search"                =>  "શોધ",
        "distance"              =>  "અંતર",
        "parent_id"             =>  "પિતૃ",
        "level"                 =>  "લેવલ",
        "attribute"             =>  "લક્ષણ",
        "interests"             =>  "રૂચિઓ",
        "interests.*"           =>  "રૂચિઓ",
        "images"                =>  "છબીઓ",
        "videos"                =>  "વીડિયો",
        "videos.*"              =>  "વીડિયો",
        "image"                 =>  "છબી",
        "image_path"            =>  "છબી પાથ",
        "api_key"               =>  "api કી",
        "api_secret"            =>  "api ગુપ્ત",
        "sid"                   =>  "sid",
        "push_id"               =>  "પુશ આઈડી",
        "identity"              =>  "ઓળખ",
        "time_line"             =>  "સમય રેખા",
        "facebook_id"           =>  "ફેસબુક આઈડી",
        "google_id"             =>  "ગૂગલ આઈડી",
        "apple_id"              =>  "એપલ આઈડી",
        "latitude"              =>  "અક્ષાંશ",
        "longitude"             =>  "રેખાંશ",
        "reason"                =>  "કારણ",
        "start_time"            =>  "પ્રારંભ સમય",
        "end_time"              =>  "સમાપ્તિ સમય",
        "remaining_time"        =>  "remaining time",
        "version"               =>  "આવૃત્તિ",
        "os"                    =>  "ઓએસ",
        "app_version"           =>  "એપ્લિકેશન સંસ્કરણ",
        "status"                =>  "સ્થિતિ",
        "about_me"              =>  "મારા વિશે",
        "fav_movie"             =>  "મનપસંદ ફિલ્મ",
        "personalities"         =>  "વ્યક્તિત્વ",
        "remove_personalities"  =>  "વ્યક્તિત્વ દૂર કરો",
        "education"             =>  "શિક્ષણ",
        "university_college"    =>  "યુનિવર્સિટી અને કોલેજ",
        "profession"            =>  "વ્યવસાય",
        "religion"              =>  "ધર્મ",
        "relationship_status"   =>  "સંબંધો સ્થિતિ",
        "i_am_here"             =>  "હું અહીં",
        "food_preference"       =>  "ખોરાક પસંદગી",
        "drinking"              =>  "પીવું",
        "smoking"               =>  "ધૂમ્રપાન",
        "star_sign"             =>  "સિતારાની સહી",
        "community"             =>  "સમુદાય",
        "old_profile_photo"     =>  "જૂનો પ્રોફાઇલ ફોટો",
        "old_images"            =>  "જૂની છબીઓ",
        "voice"                 =>  "અવાજ",
        "voice_answer"          =>  "અવાજ જવાબ",
        "remove_voice"          =>  "અવાજ દૂર કરો",
        "remove_video"          =>  "વિડિઓ દૂર કરો",
        "remove_image"          =>  "છબી દૂર કરો",
        "remove_interests"      =>  "રુચિઓ દૂર કરો",
        "image_sequence"        =>  "છબી ક્રમ",
        "file"                  =>  "ફાઇલ",
        "message_id"            =>  "સંદેશ",
    ],
];
