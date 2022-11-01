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

    'accepted' => 'দ্য... :attribute গ্ৰহণ কৰিব লাগিব।',
    'active_url' => 'দ্য... :attribute এটা বৈধ URL নহয়।',
    'after' => 'দ্য... :attribute তাৰ পিছৰ তাৰিখ হ’ব লাগিব :date.',
    'after_or_equal' => 'দ্য... :attribute তাৰ পিছৰ বা সমান তাৰিখ হ’ব লাগিব :date.',
    'alpha' => 'দ্য... :attribute কেৱল চিঠিহে থাকিব পাৰে।',
    'alpha_dash' => 'দ্য... :attribute কেৱল আখৰ, সংখ্যা, ডেছ আৰু আণ্ডাৰস্কোৰ থাকিব পাৰে।',
    'alpha_num' => 'দ্য... :attribute কেৱল আখৰ আৰু সংখ্যা থাকিব পাৰে।',
    'array' => "দ্য... :attribute এটা এৰে হ'ব লাগিব।",
    'before' => 'দ্য... :attribute আগৰ তাৰিখ হ’ব লাগিব :date.',
    'before_or_equal' => 'দ্য... :attribute তাৰ আগৰ বা সমান তাৰিখ হ’ব লাগিব :date.',
    'between' => [
        'numeric' => 'দ্য... :attribute মাজত থাকিব লাগিব :min আৰু :max.',
        'file' => 'দ্য... :attribute মাজত থাকিব লাগিব :min আৰু :max কিলোবাইট.',
        'string' => 'দ্য... :attribute মাজত থাকিব লাগিব :min আৰু :max চৰিত্ৰ.',
        'array' => 'দ্য... :attribute মাজত থাকিব লাগিব :min আৰু :max সামগ্ৰী.',
    ],
    'boolean' => 'দ্য... :attribute ক্ষেত্ৰ সঁচা বা মিছা হ’ব লাগিব।',
    'confirmed' => 'দ্য... :attribute নিশ্চিতকৰণৰ মিল নাই।',
    'date' => 'দ্য... :attribute বৈধ তাৰিখ নহয়।',
    'date_equals' => 'দ্য... :attribute ৰ সমান তাৰিখ হ’ব লাগিব :date.',
    'date_format' => 'দ্য... :attribute বিন্যাসৰ সৈতে মিল নাথাকে :format.',
    'different' => 'দ্য... :attribute আৰু :other বেলেগ হ’ব লাগিব.',
    'digits' => 'দ্য... :attribute হ’ব লাগিব :digits সংখ্যা।',
    'digits_between' => 'দ্য... :attribute মাজত থাকিব লাগিব :min আৰু :max সংখ্যা।.',
    'dimensions' => 'দ্য... :attribute অবৈধ ছবিৰ মাত্ৰা আছে।',
    'distinct' => 'দ্য... :attribute ক্ষেত্ৰৰ এটা নকল মান আছে।',
    'email' => 'দ্য... :attribute এটা বৈধ ইমেইল ঠিকনা হ’ব লাগিব।',
    'ends_with' => 'দ্য... :attribute তলৰ এটাৰে শেষ হ’ব লাগিব: :values',
    'exists' => 'দ্য... নিৰ্বাচিত কৰা হৈছে :attribute অবৈধ।',
    'file' => "দ্য... :attribute এটা ফাইল হ'ব লাগিব।",
    'filled' => 'দ্য... :attribute ফিল্ডৰ এটা মান থাকিব লাগিব।',
    'gt' => [
        'numeric' => 'দ্য... :attribute তকৈ ডাঙৰ হ’ব লাগিব :value.',
        'file' => 'দ্য... :attribute তকৈ ডাঙৰ হ’ব লাগিব :value কিলোবাইট.',
        'string' => 'দ্য... :attribute তকৈ ডাঙৰ হ’ব লাগিব :value চৰিত্ৰ.',
        'array' => 'দ্য... :attribute তকৈ অধিক থাকিব লাগিব :value সামগ্ৰী.',
    ],
    'gte' => [
        'numeric' => 'দ্য... :attribute তাতকৈ ডাঙৰ বা সমান হ’ব লাগিব :value.',
        'file' => 'দ্য... :attribute তাতকৈ ডাঙৰ বা সমান হ’ব লাগিব :value কিলোবাইট.',
        'string' => 'দ্য... :attribute তাতকৈ ডাঙৰ বা সমান হ’ব লাগিব :value চৰিত্ৰ.',
        'array' => 'দ্য... :attribute থাকিব লাগিব :value বস্তু বা তাতকৈ অধিক।',
    ],
    'image' => 'দ্য... :attribute এটা প্ৰতিচ্ছবি হ’ব লাগিব।',
    'in' => 'দ্য... নিৰ্বাচিত কৰা হৈছে :attribute অবৈধ।',
    'in_array' => 'দ্য... :attribute ফিল্ডৰ অস্তিত্ব নাই :other.',
    'integer' => 'দ্য... :attribute এটা পূৰ্ণসংখ্যা হ’ব লাগিব।',
    'ip' => "দ্য... :attribute এটা বৈধ IP ঠিকনা হ'ব লাগিব।",
    'ipv4' => "দ্য... :attribute এটা বৈধ IPv4 ঠিকনা হ'ব লাগিব।",
    'ipv6' => "দ্য... :attribute এটা বৈধ IPv6 ঠিকনা হ'ব লাগিব।",
    'json' => "দ্য... :attribute এটা বৈধ JSON স্ট্ৰিং হ'ব লাগিব।",
    'lt' => [
        'numeric' => 'দ্য... :attribute তকৈ কম হ’ব লাগিব :value.',
        'file' => 'দ্য... :attribute তকৈ কম হ’ব লাগিব :value কিলোবাইট.',
        'string' => 'দ্য... :attribute তকৈ কম হ’ব লাগিব :value চৰিত্ৰ.',
        'array' => 'দ্য... :attribute তকৈ কম থাকিব লাগিব :value সামগ্ৰী.',
    ],
    'lte' => [
        'numeric' => 'দ্য... :attribute কম বা সমান হ’ব লাগিব :value.',
        'file' => 'দ্য... :attribute কম বা সমান হ’ব লাগিব :value কিলোবাইট.',
        'string' => 'দ্য... :attribute কম বা সমান হ’ব লাগিব :value চৰিত্ৰ.',
        'array' => 'দ্য... :attribute তকৈ বেছি থাকিব নালাগে :value সামগ্ৰী.',
    ],
    'max' => [
        'numeric' => 'দ্য... :attribute তকৈ ডাঙৰ নহ’বও পাৰে :max.',
        'file' => 'দ্য... :attribute তকৈ ডাঙৰ নহ’বও পাৰে :max কিলোবাইট.',
        'string' => 'দ্য... :attribute তকৈ ডাঙৰ নহ’বও পাৰে :max চৰিত্ৰ.',
        'array' => 'দ্য... :attribute তকৈ বেছি নাথাকিবও পাৰে :max সামগ্ৰী.',
    ],
    'mimes' => "দ্য... :attribute ধৰণৰ এটা ফাইল হ'ব লাগিব: :values.",
    'mimetypes' => 'দ্য... :attribute ধৰণৰ ফাইল হ’ব লাগিব: :values.',
    'min' => [
        'numeric' => 'দ্য... :attribute অন্ততঃ হ’ব লাগিব :min.',
        'file' => 'দ্য... :attribute অন্ততঃ হ’ব লাগিব :min কিলোবাইট.',
        'string' => 'দ্য... :attribute অন্ততঃ হ’ব লাগিব :min চৰিত্ৰ.',
        'array' => 'দ্য... :attribute অন্ততঃ থাকিব লাগিব :min সামগ্ৰী.',
    ],
    'not_in' => 'দ্য... নিৰ্বাচিত কৰা হৈছে :attribute অবৈধ।',
    'not_regex' => 'দ্য... :attribute বিন্যাস অবৈধ।',
    'numeric' => 'দ্য... :attribute এটা সংখ্যা হ’ব লাগিব।',
    'present' => 'দ্য... :attribute ফিল্ড উপস্থিত থাকিব লাগিব।',
    'regex' => 'দ্য... :attribute বিন্যাস অবৈধ।',
    'required' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন।',
    'required_if' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন হয় যেতিয়া :other is :value.',
    'required_unless' => 'দ্য... :attribute ক্ষেত্ৰখনৰ প্ৰয়োজন যদিহে :other is in :values.',
    'required_with' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন হয় যেতিয়া :values উপস্থিত থাকে.',
    'required_with_all' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন হয় যেতিয়া :values উপস্থিত থাকে।',
    'required_without' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন হয় যেতিয়া :values উপস্থিত নহয়।',
    'required_without_all' => 'দ্য... :attribute ফিল্ডৰ প্ৰয়োজন হয় যেতিয়া কোনোটোৱেই নহয় :values উপস্থিত থাকে।',
    'same' => 'দ্য... :attribute আৰু :other মিলিব লাগিব।',
    'size' => [
        'numeric' => 'দ্য... :attribute হব লাগিব :size.',
        'file' => 'দ্য... :attribute হব লাগিব :size কিলোবাইট.',
        'string' => 'দ্য... :attribute হব লাগিব :size চৰিত্ৰ.',
        'array' => 'দ্য... :attribute থাকিব লাগিব :size সামগ্ৰী.',
    ],
    'starts_with' => 'দ্য... :attribute তলৰ এটাৰ পৰা আৰম্ভ কৰিব লাগিব: :values',
    'string' => "দ্য... :attribute এটা ষ্ট্ৰিং হ'ব লাগিব।",
    'timezone' => "দ্য... :attribute এটা বৈধ জ'ন হ'ব লাগিব।",
    'unique' => 'দ্য... :attribute ইতিমধ্যে লোৱা হৈছে।',
    'uploaded' => "দ্য... :attribute আপলোড কৰাত ব্যৰ্থ হ'ল।",
    'url' => 'দ্য... :attribute বিন্যাস অবৈধ।',
    'uuid' => "দ্য... :attribute এটা বৈধ UUID হ'ব লাগিব।",

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

    'not_empty' => "দ্য... :attribute কেৱল ঠাইৰ অনুমতি নিদিয়ে।",
    'not_exists' => 'দ্য... :attribute is not exists.',
    'no_space' => "দ্য... :attribute ঠাই থাকিব নালাগে।",
    'not_match' => 'দ্য... :attribute আমাৰ ৰেকৰ্ডৰ সৈতে মিল নাই।',
    'not_equal' => 'দ্য... :attribute আৰু :other একে হ’ব নালাগে',
    'equal_to' => 'দ্য... :attribute আৰু :other একেই হ’ব লাগিব',
    'lettersonly' => 'দ্য... :attribute কেৱল আখৰ আৰু খালী ঠাই থাকিব পাৰে।',
    'alpha_numeric' => 'দ্য... :attribute কেৱল আখৰ, সংখ্যা আৰু খালী ঠাই থাকিব পাৰে।',
    'image_dimentions' => "দ্য... :attribute ৰ হ'ব লাগিব :min x :max px মাত্ৰাসমূহ।",
    'emoji_found'   =>  'ইমোজি সোমাব নালাগে :attribute',

    // 'duration'      =>  'ভিডিঅ’ৰ সময়সীমা মাত্ৰ ১ৰ পৰা ৬০ ছেকেণ্ডৰ ভিতৰত হ’ব লাগিব.',
    'duration'      =>  'দ্য... ভিডিঅ’ৰ সময়সীমা ৬০ ছেকেণ্ডতকৈ বেছি নহ’বও পাৰে।',
    'video'         =>  [
        'portrait'  =>  'ভিডিঅ’টো প’ৰ্ট্ৰেইট মোডত থাকিব লাগিব',
        'landscape' =>  'ভিডিঅ’টো লেণ্ডস্কেপ মোডত থাকিব লাগিব',
    ],

    'attributes' => [
        "field"         =>  "ক্ষেত্ৰ",
        "password"      =>  "পাছৱৰ্ড",
        "name"          =>  "নাম",
        "username"      =>  "ব্যৱহাৰকাৰীৰ নাম",
        "contact"       =>  "যোগাযোগ কৰক",
        "country_id"    =>  "দেশ",
        "country"       =>  "দেশ",
        "start_age"     =>  "আৰম্ভণিৰ বয়স",
        "end_age"       =>  "শেষ বয়স",
        "public"        =>  [
            "email"     =>  "ইমেইল",
            "contact"   =>  "যোগাযোগ কৰক",
        ],
        "description"   =>  "বিৱৰণ",
        "link"          =>  "লিংক",
        "city"          =>  "চহৰ",
        "remove_profile"    =>  "প্ৰ'ফাইল আঁতৰাওক",
        "birthday"          =>  "জন্মদিন",
        "profile"           =>  "ৰূপৰেখা",
        "old_password"      =>  "পুৰণা গুপ্তশব্দ",
        "token"             =>  "টোকেন",
        "device_type"       =>  "ডিভাইচৰ ধৰণ",
        "checksum"          =>  "checksum",
        "limit"             =>  "সীমা",
        "offset"            =>  "অফছেট",
        "type"              =>  "প্ৰকাৰ",
        "category"          =>  "শ্ৰেণী",
        "thumbnail"         =>  "থাম্বনেইল",
        "video"             =>  "ভিডিঅ’",
        "duration"          =>  "সময়কাল",
        "hashtags"          =>  "হেচটেগসমূহ",
        "cause"             =>  "কাৰণ",
        "state"             =>  "state",
        "id"                =>  "id",
        "keyword"           =>  "মূল শব্দ",
        "device"            =>  "ডিভাইচ",
        "udid"              =>  "উডিড",
        "user"              =>  "ব্যৱহাৰকাৰী",
        "reported_user"     =>  "ৰিপৰ্ট কৰা ব্যৱহাৰকাৰী",
        "message"           =>  "বাৰ্তা",
        "user_id"           =>  "ব্যৱহাৰকাৰী",

        // Custom Resource Validation
        "contact_no"            =>  "যোগাযোগ নম্বৰ",
        "security_token"        =>  "নিৰাপত্তা টোকেন",
        "first_name"            =>  "প্ৰথম নাম",
        "last_name"             =>  "উপাধি",
        "full_name"             =>  "সম্পূৰ্ণ নাম",
        "email"                 =>  "ইমেইল",
        "birth_date"            =>  "জন্ম তাৰিখ",
        "gender"                =>  "লিংগ",
        "interest"              =>  "সুত",
        "location"              =>  "অৱস্থান",
        "location_id"           =>  "অৱস্থান",
        "language"              =>  "ভাষা",
        "languages"             =>  "ভাষাসমূহ",
        "profile_photo"         =>  "প্ৰফাইল ফটো",
        "country_code"          =>  "দেশৰ সংকেত",
        "room_name"             =>  "কোঠাৰ নাম",
        "room"                  =>  "কোঠা",
        "room_id"               =>  "কোঠা",
        "participant_id"        =>  "অংশগ্ৰহণকাৰী",
        "search"                =>  "সন্ধান",
        "distance"              =>  "দূৰত্ব",
        "parent_id"             =>  "পিতৃ-মাতৃ",
        "level"                 =>  "স্তৰ",
        "attribute"             =>  "বৈশিষ্ট্য",
        "interests"             =>  "স্বাৰ্থ",
        "interests.*"           =>  "স্বাৰ্থ",
        "images"                =>  "ছবিসমূহ",
        "videos"                =>  "ভিডিঅ'সমূহ",
        "videos.*"              =>  "ভিডিঅ'সমূহ",
        "image"                 =>  "ছৱি",
        "image_path"            =>  "ছবিৰ পথ",
        "api_key"               =>  "api key",
        "api_secret"            =>  "api গোপন",
        "sid"                   =>  "sid",
        "push_id"               =>  "push id",
        "identity"              =>  "পৰিচয়",
        "time_line"             =>  "সময় ৰেখা",
        "facebook_id"           =>  "ফেচবুক আইডি",
        "google_id"             =>  "গুগল আইডি",
        "apple_id"              =>  "আপেল আইডি",
        "latitude"              =>  "অক্ষাংশ",
        "longitude"             =>  "দ্ৰাঘিমাংশ",
        "reason"                =>  "কাৰণ",
        "start_time"            =>  "আৰম্ভণিৰ সময়",
        "end_time"              =>  "শেষ সময়",
        "remaining_time"        =>  "বাকী থকা সময়",
        "version"               =>  "সংস্কৰণ",
        "os"                    =>  "os",
        "app_version"           =>  "এপ সংস্কৰণ",
        "status"                =>  "স্থিতি",
        "about_me"              =>  "মোৰ বিষয়ে",
        "fav_movie"             =>  "প্ৰিয় চিনেমা",
        "personalities"         =>  "ব্যক্তিত্ব",
        "remove_personalities"  =>  "ব্যক্তিত্ব আঁতৰাই পেলাওক",
        "education"             =>  "শিক্ষা",
        "university_college"    =>  "বিশ্ববিদ্যালয় আৰু মহাবিদ্যালয়",
        "profession"            =>  "পেছা",
        "religion"              =>  "ধৰ্ম",
        "relationship_status"   =>  "সম্পৰ্কৰ অৱস্থা",
        "i_am_here"             =>  "মই ইয়াত আছো",
        "food_preference"       =>  "খাদ্যৰ পছন্দ",
        "drinking"              =>  "মদ্যপান কৰা",
        "smoking"               =>  "ধূমপান কৰা",
        "star_sign"             =>  "ৰাশি",
        "community"             =>  "সমুদায়",
        "old_profile_photo"     =>  "পুৰণি প্ৰফাইল ফটো",
        "old_images"            =>  "পুৰণি ছবি",
        "voice"                 =>  "কণ্ঠ",
        "voice_answer"          =>  "কণ্ঠৰ উত্তৰ",
        "remove_voice"          =>  "মাত আঁতৰাই পেলাওক",
        "remove_video"          =>  "ভিডিঅ' আঁতৰাওক",
        "remove_image"          =>  "ছবি আঁতৰাওক",
        "remove_interests"      =>  "স্বাৰ্থ আঁতৰাই পেলাওক",
        "image_sequence"        =>  "ছবিৰ ক্ৰম",
        "file"                  =>  "ফাইল",
        "message_id"            =>  "বাৰ্তা",
    ],
];
