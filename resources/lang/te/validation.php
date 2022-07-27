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

    'accepted' => 'ది :attribute ଗ୍ରହଣ କରିବା ଜରୁରୀ |.',
    'active_url' => 'ది :attribute చెల్లుబాటు అయ్యే URL కాదు.',
    'after' => 'ది :attribute తర్వాత తేదీ అయి ఉండాలి :date.',
    'after_or_equal' => 'ది :attribute తప్పనిసరిగా తర్వాత లేదా సమానమైన తేదీ అయి ఉండాలి :date.',
    'alpha' => 'ది :attribute అక్షరాలు మాత్రమే ఉండవచ్చు.',
    'alpha_dash' => 'ది :attribute అక్షరాలు, సంఖ్యలు, డాష్‌లు మరియు అండర్‌స్కోర్‌లను మాత్రమే కలిగి ఉండవచ్చు.',
    'alpha_num' => 'ది :attribute అక్షరాలు మరియు సంఖ్యలను మాత్రమే కలిగి ఉండవచ్చు.',
    'array' => 'ది :attribute శ్రేణి అయి ఉండాలి.',
    'before' => 'ది :attribute ముందు తేదీ అయి ఉండాలి :date.',
    'before_or_equal' => 'ది :attribute తప్పనిసరిగా ముందు లేదా సమానమైన తేదీ అయి ఉండాలి :date.',
    'between' => [
        'numeric' => 'ది :attribute మధ్య ఉండాలి :min మరియు :max.',
        'file' => 'ది :attribute మధ్య ఉండాలి :min మరియు :max కిలోబైట్లు.',
        'string' => 'ది :attribute మధ్య ఉండాలి :min మరియు :max పాత్రలు.',
        'array' => 'ది :attribute మధ్య ఉండాలి :min మరియు :max అంశాలు.',
    ],
    'boolean' => 'ది :attribute ఫీల్డ్ తప్పక నిజం లేదా తప్పుగా ఉండాలి.',
    'confirmed' => 'ది :attribute నిర్ధారణ సరిపోలలేదు.',
    'date' => 'ది :attribute చెల్లుబాటు అయ్యే తేదీ కాదు.',
    'date_equals' => 'ది :attribute తప్పనిసరిగా సమానమైన తేదీ అయి ఉండాలి :date.',
    'date_format' => 'ది :attribute ఫార్మాట్‌తో సరిపోలడం లేదు :format.',
    'different' => 'ది :attribute మరియు :other భిన్నంగా ఉండాలి.',
    'digits' => 'ది :attribute ఉండాలి :digits అంకెలు.',
    'digits_between' => 'ది :attribute మధ్య ఉండాలి :min మరియు :max అంకెలు.',
    'dimensions' => 'ది :attribute చెల్లని చిత్ర కొలతలు ఉన్నాయి.',
    'distinct' => 'ది :attribute ఫీల్డ్ నకిలీ విలువను కలిగి ఉంది.',
    'email' => 'ది :attribute ఒక చెల్లుబాటు అయ్యే ఇ - మెయిల్ చిరునామా ఉండాలి.',
    'ends_with' => 'ది :attribute కింది వాటిలో ఒకదానితో ముగించాలి: :values',
    'exists' => 'ది ఎంపిక చేయబడింది :attribute చెల్లదు.',
    'file' => 'ది :attribute తప్పనిసరిగా ఫైల్ అయి ఉండాలి.',
    'filled' => 'ది :attribute ఫీల్డ్ తప్పనిసరిగా విలువను కలిగి ఉండాలి.',
    'gt' => [
        'numeric' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి :value.',
        'file' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి :value కిలోబైట్లు.',
        'string' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి :value పాత్రలు.',
        'array' => 'ది :attribute కంటే ఎక్కువ కలిగి ఉండాలి :value అంశాలు.',
    ],
    'gte' => [
        'numeric' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి లేదా సమానం :value.',
        'file' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి లేదా సమానం :value కిలోబైట్లు.',
        'string' => 'ది :attribute కంటే ఎక్కువగా ఉండాలి లేదా సమానం :value పాత్రలు.',
        'array' => 'ది :attribute కలిగి ఉండాలి :value అంశాలు లేదా మరిన్ని.',
    ],
    'image' => 'ది :attribute చిత్రంగా ఉండాలి.',
    'in' => 'ది ఎంపిక చేయబడింది :attribute చెల్లదు.',
    'in_array' => 'ది :attribute ఫీల్డ్ ఉనికిలో లేదు :other.',
    'integer' => 'ది :attribute పూర్ణాంకం అయి ఉండాలి.',
    'ip' => 'ది :attribute తప్పనిసరిగా చెల్లుబాటు అయ్యే IP చిరునామా అయి ఉండాలి.',
    'ipv4' => 'ది :attribute తప్పక చెల్లుబాటు అవుతుంది IPv4 చిరునామా.',
    'ipv6' => 'ది :attribute తప్పక చెల్లుబాటు అవుతుంది IPv6 చిరునామా.',
    'json' => 'ది :attribute తప్పక చెల్లుబాటు అవుతుంది JSON స్ట్రింగ్.',
    'lt' => [
        'numeric' => 'ది :attribute కంటే తక్కువగా ఉండాలి :value.',
        'file' => 'ది :attribute కంటే తక్కువగా ఉండాలి :value కిలోబైట్లు.',
        'string' => 'ది :attribute కంటే తక్కువగా ఉండాలి :value పాత్రలు.',
        'array' => 'ది :attribute కంటే తక్కువగా ఉండాలి :value అంశాలు.',
    ],
    'lte' => [
        'numeric' => 'ది :attribute కంటే తక్కువగా ఉండాలి లేదా సమానం :value.',
        'file' => 'ది :attribute కంటే తక్కువగా ఉండాలి లేదా సమానం :value కిలోబైట్లు.',
        'string' => 'ది :attribute కంటే తక్కువగా ఉండాలి లేదా సమానం :value పాత్రలు.',
        'array' => 'ది :attribute కంటే ఎక్కువ ఉండకూడదు :value అంశాలు.',
    ],
    'max' => [
        'numeric' => 'ది :attribute కంటే ఎక్కువ కాకపోవచ్చు :max.',
        'file' => 'ది :attribute కంటే ఎక్కువ కాకపోవచ్చు :max కిలోబైట్లు.',
        'string' => 'ది :attribute కంటే ఎక్కువ కాకపోవచ్చు :max పాత్రలు.',
        'array' => 'ది :attribute కంటే ఎక్కువ ఉండకపోవచ్చు :max అంశాలు.',
    ],
    'mimes' => 'ది :attribute తప్పనిసరిగా ఫైల్ రకం అయి ఉండాలి: :values.',
    'mimetypes' => 'ది :attribute తప్పనిసరిగా ఫైల్ రకం అయి ఉండాలి: :values.',
    'min' => [
        'numeric' => 'ది :attribute కనీసం ఉండాలి :min.',
        'file' => 'ది :attribute కనీసం ఉండాలి :min కిలోబైట్లు.',
        'string' => 'ది :attribute కనీసం ఉండాలి :min పాత్రలు.',
        'array' => 'ది :attribute కనీసం కలిగి ఉండాలి :min అంశాలు.',
    ],
    'not_in' => 'ది ఎంపిక చేయబడింది :attribute చెల్లదు.',
    'not_regex' => 'ది :attribute ఫార్మాట్ చెల్లదు.',
    'numeric' => 'ది :attribute ఒక సంఖ్య అయి ఉండాలి.',
    'present' => 'ది :attribute ఫీల్డ్ తప్పనిసరిగా ఉండాలి.',
    'regex' => 'ది :attribute ఫార్మాట్ చెల్లదు.',
    'required' => 'ది :attribute ఫీల్డ్ అవసరం.',
    'required_if' => 'ది :attribute ఫీల్డ్ అవసరం ఎప్పుడు :other ఉంది :value.',
    'required_unless' => 'ది :attribute ఫీల్డ్ అవసరం తప్ప :other ఉంది లో :values.',
    'required_with' => 'ది :attribute ఫీల్డ్ అవసరం ఎప్పుడు :values ఉంది ప్రస్తుతం.',
    'required_with_all' => 'ది :attribute ఫీల్డ్ అవసరం ఎప్పుడు :values ఉన్నాయి.',
    'required_without' => 'ది :attribute ఫీల్డ్ అవసరం ఎప్పుడు :values ఉంది ప్రస్తుతం లేదు.',
    'required_without_all' => 'ది :attribute ఫీల్డ్ అవసరం ఎప్పుడు ఏది కాదు :values ఉన్నాయి.',
    'same' => 'ది :attribute మరియు :other తప్పక జత కుదరాలి.',
    'size' => [
        'numeric' => 'ది :attribute ఉండాలి :size.',
        'file' => 'ది :attribute ఉండాలి :size కిలోబైట్లు.',
        'string' => 'ది :attribute ఉండాలి :size పాత్రలు.',
        'array' => 'ది :attribute కలిగి ఉండాలి :size అంశాలు.',
    ],
    'starts_with' => 'ది :attribute కింది వాటిలో ఒకదానితో ప్రారంభించాలి: :values',
    'string' => 'ది :attribute ఒక స్ట్రింగ్ అయి ఉండాలి.',
    'timezone' => 'ది :attribute తప్పక చెల్లుబాటు అయ్యే జోన్ అయి ఉండాలి.',
    'unique' => 'ది :attribute ఇప్పటికే తీసుకోబడింది.',
    'uploaded' => 'ది :attribute అప్‌లోడ్ చేయడంలో విఫలమైంది.',
    'url' => 'ది :attribute ఫార్మాట్ చెల్లదు.',
    'uuid' => 'ది :attribute తప్పనిసరిగా చెల్లుబాటు అయ్యే UUID అయి ఉండాలి.',

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

    'not_empty' => "ది :attribute ఖాళీని మాత్రమే అనుమతించదు.",
    'not_exists' => 'ది :attribute ఉనికిలో లేదు.',
    'no_space' => "ది :attribute ఖాళీ ఉండకూడదు.",
    'not_match' => 'ది :attribute మా రికార్డులతో సరిపోలడం లేదు.',
    'not_equal' => 'ది :attribute మరియు :other ఒకేలా ఉండకూడదు',
    'equal_to' => 'ది :attribute మరియు :other ఒకేలా ఉండాలి',
    'lettersonly' => 'ది :attribute అక్షరాలు మాత్రమే ఉండవచ్చు మరియు ఖాళీలు.',
    'alpha_numeric' => 'ది :attribute అక్షరాలు మాత్రమే ఉండవచ్చు, సంఖ్యలు మరియు ఖాళీలు.',
    'image_dimentions' => 'ది :attribute యొక్క ఉండాలి :min px :max px కొలతలు.',
    'emoji_found'   =>  'ఎమోజీలు లోపలికి అనుమతించబడవు :attribute',

    // 'duration'      =>  'ది duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'ది వీడియో వ్యవధి 60 సెకన్ల కంటే ఎక్కువ ఉండకూడదు.',
    'video'         =>  [
        'portrait'  =>  'వీడియో తప్పనిసరిగా పోర్ట్రెయిట్ మోడ్‌లో ఉండాలి',
        'landscape' =>  'వీడియో తప్పనిసరిగా ల్యాండ్‌స్కేప్ మోడ్‌లో ఉండాలి',
    ],

    'attributes' => [
        "field"         =>  "ఫీల్డ్",
        "password"      =>  "పాస్వర్డ్",
        "name"          =>  "పేరు",
        "username"      =>  "వినియోగదారు పేరు",
        "contact"       =>  "సంప్రదించండి",
        "country_id"    =>  "దేశం",
        "country"       =>  "దేశం",
        "start_age"     =>  "ప్రారంభ వయస్సు",
        "end_age"       =>  "ముగింపు వయస్సు",
        "public"        =>  [
            "email"     =>  "ఇమెయిల్",
            "contact"   =>  "సంప్రదించండి",
        ],
        "description"   =>  "వివరణ",
        "link"          =>  "లింక్",
        "city"          =>  "నగరం",
        "remove_profile"    =>  "ప్రొఫైల్ తొలగించండి",
        "birthday"          =>  "పుట్టినరోజు",
        "profile"           =>  "ప్రొఫైల్",
        "old_password"      =>  "పాత పాస్వర్డ్",
        "token"             =>  "టోకెన్",
        "device_type"       =>  "పరికరం రకం",
        "checksum"          =>  "చెక్సమ్",
        "limit"             =>  "పరిమితి",
        "offset"            =>  "ఆఫ్సెట్",
        "type"              =>  "రకం",
        "category"          =>  "వర్గం",
        "thumbnail"         =>  "సూక్ష్మచిత్రం",
        "video"             =>  "వీడియో",
        "duration"          =>  "వ్యవధి",
        "hashtags"          =>  "హ్యాష్‌ట్యాగ్‌లు",
        "cause"             =>  "కారణం",
        "state"             =>  "రాష్ట్రం",
        "id"                =>  "id",
        "keyword"           =>  "కీవర్డ్",
        "device"            =>  "పరికరం",
        "udid"              =>  "నువ్వు చేసావు",
        "user"              =>  "వినియోగదారు",
        "reported_user"     =>  "నివేదించబడిన వినియోగదారు",
        "message"           =>  "సందేశం",
        "user_id"           =>  "వినియోగదారు",

        // Custom Resource Validation
        "contact_no"            =>  "సంప్రదింపు సంఖ్య",
        "security_token"        =>  "భద్రతా టోకెన్",
        "first_name"            =>  "మొదటి పేరు",
        "last_name"             =>  "చివరి పేరు",
        "full_name"             =>  "పూర్తి పేరు",
        "email"                 =>  "ఇమెయిల్",
        "birth_date"            =>  "పుట్టిన తేదీ",
        "gender"                =>  "లింగం",
        "interest"              =>  "ఆసక్తి",
        "location"              =>  "స్థానం",
        "location_id"           =>  "స్థానం",
        "language"              =>  "భాష",
        "languages"             =>  "భాషలు",
        "profile_photo"         =>  "ప్రొఫైల్ ఫోటో",
        "country_code"          =>  "దేశం కోడ్",
        "room_name"             =>  "గది పేరు",
        "room"                  =>  "గది",
        "room_id"               =>  "గది",
        "participant_id"        =>  "పాల్గొనేవాడు",
        "search"                =>  "వెతకండి",
        "distance"              =>  "దూరం",
        "parent_id"             =>  "తల్లిదండ్రులు",
        "level"                 =>  "స్థాయి",
        "attribute"             =>  "గుణం",
        "interests"             =>  "ఆసక్తులు",
        "interests.*"           =>  "ఆసక్తులు",
        "images"                =>  "చిత్రాలు",
        "videos"                =>  "వీడియోలు",
        "videos.*"              =>  "వీడియోలు",
        "image"                 =>  "చిత్రం",
        "image_path"            =>  "చిత్రం మార్గం",
        "api_key"               =>  "api కీ",
        "api_secret"            =>  "api రహస్య",
        "sid"                   =>  "సిడ్",
        "push_id"               =>  "పుష్ ID",
        "identity"              =>  "గుర్తింపు",
        "time_line"             =>  "కాలక్రమం",
        "facebook_id"           =>  "facebook id",
        "google_id"             =>  "google id",
        "apple_id"              =>  "ఆపిల్ ఐడి",
        "latitude"              =>  "అక్షాంశం",
        "longitude"             =>  "రేఖాంశం",
        "reason"                =>  "కారణం",
        "start_time"            =>  "ప్రారంభ సమయం",
        "end_time"              =>  "ముగింపు సమయం",
        "remaining_time"        =>  "మిగిలిన సమయం",
        "version"               =>  "సంస్కరణ: Telugu",
        "os"                    =>  "os",
        "app_version"           =>  "యాప్ వెర్షన్",
        "status"                =>  "హోదా",
        "about_me"              =>  "నా గురించి",
        "fav_movie"             =>  "ఇష్టమైన సినిమా",
        "personalities"         =>  "వ్యక్తిత్వాలు",
        "remove_personalities"  =>  "వ్యక్తిత్వాలను తొలగించండి",
        "education"             =>  "చదువు",
        "university_college"    =>  "విశ్వవిద్యాలయం మరియు కళాశాల",
        "profession"            =>  "వృత్తి",
        "religion"              =>  "మతం",
        "relationship_status"   =>  "సంబంధాల స్థాయి",
        "i_am_here"             =>  "నేను ఇక్కడ ఉన్నాను",
        "food_preference"       =>  "ఆహార ప్రాధాన్యత",
        "drinking"              =>  "తాగడం",
        "smoking"               =>  "ధూమపానం",
        "star_sign"             =>  "నక్షత్రం గుర్తు",
        "community"             =>  "సంఘం",
        "old_profile_photo"     =>  "పాత ప్రొఫైల్ ఫోటో",
        "old_images"            =>  "పాత చిత్రాలు",
        "voice"                 =>  "వాయిస్",
        "voice_answer"          =>  "వాయిస్ సమాధానం",
        "remove_voice"          =>  "వాయిస్ తొలగించండి",
        "remove_video"          =>  "వీడియోను తీసివేయండి",
        "remove_image"          =>  "చిత్రాన్ని తీసివేయండి",
        "remove_interests"      =>  "ఆసక్తులను తొలగించండి",
        "image_sequence"        =>  "చిత్ర క్రమం",
        "file"                  =>  "ఫైల్",
        "message_id"            =>  "సందేశం",
    ],
];
