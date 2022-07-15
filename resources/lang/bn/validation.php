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

    'accepted' => 'দ্য :attribute গ্রহণ করতে হবে।',
    'active_url' => 'দ্য :attribute একটি বৈধ URL নয়।',
    'after' => 'দ্য :attribute পরে একটি তারিখ হতে হবে :date.',
    'after_or_equal' => 'দ্য :attribute এর পরে বা সমান তারিখ হতে হবে :date.',
    'alpha' => 'দ্য :attribute শুধুমাত্র অক্ষর থাকতে পারে।',
    'alpha_dash' => 'দ্য :attribute শুধুমাত্র অক্ষর, সংখ্যা, ড্যাশ এবং আন্ডারস্কোর থাকতে পারে।',
    'alpha_num' => 'দ্য :attribute শুধুমাত্র অক্ষর এবং সংখ্যা থাকতে পারে।',
    'array' => 'দ্য :attribute একটি অ্যারে হতে হবে।',
    'before' => 'দ্য :attribute আগে একটি তারিখ হতে হবে :date.',
    'before_or_equal' => 'দ্য :attribute একটি তারিখ আগে বা সমান হতে হবে :date.',
    'between' => [
        'numeric' => 'দ্য :attribute মধ্যে হতে হবে :min এবং :max.',
        'file' => 'দ্য :attribute মধ্যে হতে হবে :min এবং :max কিলোবাইট.',
        'string' => 'দ্য :attribute মধ্যে হতে হবে :min এবং :max চরিত্র.',
        'array' => 'দ্য :attribute মধ্যে থাকতে হবে :min এবং :max আইটেম.',
    ],
    'boolean' => 'দ্য :attribute ক্ষেত্র সত্য বা মিথ্যা হতে হবে।',
    'confirmed' => 'দ্য :attribute নিশ্চিতকরণ মেলে না।',
    'date' => 'দ্য :attribute একটি বৈধ তারিখ নয়।',
    'date_equals' => 'দ্য :attribute এর সমান একটি তারিখ হতে হবে :date.',
    'date_format' => 'দ্য :attribute বিন্যাসের সাথে মেলে না :format.',
    'different' => 'দ্য :attribute এবং :other ভিন্ন হতে হবে।',
    'digits' => 'দ্য :attribute অবশ্যই :digits অঙ্ক.',
    'digits_between' => 'দ্য :attribute মধ্যে হতে হবে :min এবং :max অঙ্ক.',
    'dimensions' => 'দ্য :attribute অবৈধ চিত্র মাত্রা আছে.',
    'distinct' => 'দ্য :attribute ক্ষেত্রের একটি ডুপ্লিকেট মান আছে।',
    'email' => 'দ্য :attribute একটি বৈধ ইমেইল ঠিকানা আবশ্যক.',
    'ends_with' => 'দ্য :attribute নিম্নলিখিতগুলির একটি দিয়ে শেষ করতে হবে: :values',
    'exists' => 'নির্বাচিত :attribute অবৈধ.',
    'file' => 'দ্য :attribute একটি ফাইল হতে হবে।',
    'filled' => 'দ্য :attribute ক্ষেত্রের একটি মান থাকতে হবে।',
    'gt' => [
        'numeric' => 'দ্য :attribute এর থেকে বড় হতে হবে :value.',
        'file' => 'দ্য :attribute এর থেকে বড় হতে হবে :value কিলোবাইট.',
        'string' => 'দ্য :attribute এর থেকে বড় হতে হবে :value চরিত্র.',
        'array' => 'দ্য :attribute এর বেশি থাকতে হবে :value আইটেম.',
    ],
    'gte' => [
        'numeric' => 'দ্য :attribute এর থেকে বড় বা সমান হতে হবে :value.',
        'file' => 'দ্য :attribute এর থেকে বড় বা সমান হতে হবে :value কিলোবাইট.',
        'string' => 'দ্য :attribute এর থেকে বড় বা সমান হতে হবে :value চরিত্র.',
        'array' => 'দ্য :attribute অবশ্যই থাকতে হবে :value আইটেম বা আরও বেশি।',
    ],
    'image' => 'দ্য :attribute একটি ছবি হতে হবে।',
    'in' => 'নির্বাচিত :attribute অবৈধ.',
    'in_array' => 'দ্য :attribute ক্ষেত্র বিদ্যমান নেই :other.',
    'integer' => 'দ্য :attribute একটি পূর্ণসংখ্যা হতে হবে।',
    'ip' => 'দ্য :attribute একটি বৈধ আইপি ঠিকানা হতে হবে।',
    'ipv4' => 'দ্য :attribute একটি বৈধ IPv4 ঠিকানা হতে হবে।',
    'ipv6' => 'দ্য :attribute একটি বৈধ IPv6 ঠিকানা হতে হবে।',
    'json' => 'দ্য :attribute একটি বৈধ JSON স্ট্রিং হতে হবে।',
    'lt' => [
        'numeric' => 'দ্য :attribute থেকে কম হতে হবে :value.',
        'file' => 'দ্য :attribute থেকে কম হতে হবে :value কিলোবাইট.',
        'string' => 'দ্য :attribute থেকে কম হতে হবে :value চরিত্র.',
        'array' => 'দ্য :attribute এর কম থাকতে হবে :value আইটেম.',
    ],
    'lte' => [
        'numeric' => 'দ্য :attribute থেকে কম হতে হবে বা সমান :value.',
        'file' => 'দ্য :attribute থেকে কম হতে হবে বা সমান :value কিলোবাইট.',
        'string' => 'দ্য :attribute থেকে কম হতে হবে বা সমান :value চরিত্র.',
        'array' => 'দ্য :attribute এর বেশি থাকতে হবে না :value আইটেম.',
    ],
    'max' => [
        'numeric' => 'দ্য :attribute এর বেশি নাও হতে পারে :max.',
        'file' => 'দ্য :attribute এর বেশি নাও হতে পারে :max কিলোবাইট.',
        'string' => 'দ্য :attribute এর বেশি নাও হতে পারে :max চরিত্র.',
        'array' => 'দ্য :attribute এর বেশি নাও থাকতে পারে :max আইটেম.',
    ],
    'mimes' => 'দ্য :attribute টাইপের ফাইল হতে হবে: :values.',
    'mimetypes' => 'দ্য :attribute টাইপের ফাইল হতে হবে: :values.',
    'min' => [
        'numeric' => 'দ্য :attribute নূন্যতম হতে হবে :min.',
        'file' => 'দ্য :attribute নূন্যতম হতে হবে :min কিলোবাইট.',
        'string' => 'দ্য :attribute নূন্যতম হতে হবে :min চরিত্র.',
        'array' => 'দ্য :attribute অন্তত থাকতে হবে :min আইটেম.',
    ],
    'not_in' => 'নির্বাচিত :attribute অবৈধ.',
    'not_regex' => 'দ্য :attribute বিন্যাস অবৈধ.',
    'numeric' => 'দ্য :attribute অবশ্যই একটি সংখ্যা হবে.',
    'present' => 'দ্য :attribute ক্ষেত্র উপস্থিত হতে হবে।',
    'regex' => 'দ্য :attribute বিন্যাস অবৈধ.',
    'required' => 'দ্য :attribute আপনি উত্তর দিবেন না.',
    'required_if' => 'দ্য :attribute ক্ষেত্রের প্রয়োজন হয় যখন :other হয় :value.',
    'required_unless' => 'দ্য :attribute ক্ষেত্র প্রয়োজন যদি না :other মধ্যে আছে :values.',
    'required_with' => 'দ্য :attribute ক্ষেত্রের প্রয়োজন হয় যখন :values উপস্থিত.',
    'required_with_all' => 'দ্য :attribute ক্ষেত্রের প্রয়োজন হয় যখন :values উপস্থিত আছেন.',
    'required_without' => 'দ্য :attribute ক্ষেত্রের প্রয়োজন হয় যখন :values উপস্থিত নেই',
    'required_without_all' => 'দ্য :attribute ক্ষেত্রের প্রয়োজন হয় যখন কোনটি :values উপস্থিত আছেন.',
    'same' => 'দ্য :attribute এবং :other মেলানো.',
    'size' => [
        'numeric' => 'দ্য :attribute অবশ্যই :size.',
        'file' => 'দ্য :attribute অবশ্যই :size কিলোবাইট.',
        'string' => 'দ্য :attribute অবশ্যই :size চরিত্র.',
        'array' => 'দ্য :attribute অবশ্যই থাকতে হবে :size আইটেম.',
    ],
    'starts_with' => 'দ্য :attribute নিম্নলিখিতগুলির একটি দিয়ে শুরু করতে হবে: :values',
    'string' => 'দ্য :attribute অবশ্যই একটি স্ট্রিং',
    'timezone' => 'দ্য :attribute অবশ্যই একটি বৈধ অঞ্চল।',
    'unique' => 'দ্য :attribute আগেই নেয়া হয়েছে.',
    'uploaded' => 'দ্য :attribute আপলোড করতে ব্যর্থ।',
    'url' => 'দ্য :attribute বিন্যাস অবৈধ।',
    'uuid' => 'দ্য :attribute অবশ্যই একটি বৈধ UUID।',

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

    'not_empty' => ":attribute শুধুমাত্র স্থান অনুমতি দেয় না.",
    'not_exists' => 'দ্য :attribute বিদ্যমান নেই।',
    'no_space' => "দ্য :attribute স্থান থাকতে হবে না।",
    'not_match' => 'দ্য :attribute আমাদের রেকর্ডের সাথে মেলে না।',
    'not_equal' => 'দ্য :attribute এবং :other একই হতে হবে না',
    'equal_to' => ':attribute এবং :other একই হতে হবে',
    'lettersonly' => ':attribute শুধুমাত্র অক্ষর এবং স্পেস থাকতে পারে।',
    'alpha_numeric' => ':attribute শুধুমাত্র অক্ষর, সংখ্যা এবং স্পেস থাকতে পারে।',
    'image_dimentions' => ':attribute অবশ্যই :min x :max px মাত্রার হতে হবে।',
    'emoji_found'   =>  ':attribute -এ ইমোজি অনুমোদিত নয়',

    // 'duration'      =>  'The duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'ভিডিওর সময়কাল 60 সেকেন্ডের বেশি নাও হতে পারে।',
    'video'         =>  [
        'portrait'  =>  'ভিডিও অবশ্যই পোর্ট্রেট মোডে থাকতে হবে',
        'landscape' =>  'ভিডিও অবশ্যই ল্যান্ডস্কেপ মোডে হতে হবে',
    ],

    'attributes' => [
        "field"         =>  "ক্ষেত্র",
        "password"      =>  "পাসওয়ার্ড",
        "name"          =>  "নাম",
        "username"      =>  "ব্যবহারকারীর নাম",
        "contact"       =>  "যোগাযোগ",
        "country_id"    =>  "দেশ",
        "country"       =>  "দেশ",
        "start_age"     =>  "শুরু বয়স",
        "end_age"       =>  "শেষ বয়স",
        "public"        =>  [
            "email"     =>  "ইমেইল",
            "contact"   =>  "যোগাযোগ",
        ],
        "description"   =>  "বর্ণনা",
        "link"          =>  "লিঙ্ক",
        "city"          =>  "শহর",
        "remove_profile"    =>  "প্রোফাইল সরান",
        "birthday"          =>  "জন্মদিন",
        "profile"           =>  "প্রোফাইল",
        "old_password"      =>  "পুরানো পাসওয়ার্ড",
        "token"             =>  "টোকেন",
        "device_type"       =>  "ডিভাইসের প্রকার",
        "checksum"          =>  "চেকসাম",
        "limit"             =>  "সীমা",
        "offset"            =>  "অফসেট",
        "type"              =>  "টাইপ",
        "category"          =>  "বিভাগ",
        "thumbnail"         =>  "থাম্বনেল",
        "video"             =>  "ভিডিও",
        "duration"          =>  "সময়কাল",
        "hashtags"          =>  "হ্যাশট্যাগ",
        "cause"             =>  "কারণ",
        "state"             =>  "রাষ্ট্র",
        "id"                =>  "আইডি",
        "keyword"           =>  "কীওয়ার্ড",
        "device"            =>  "ডিভাইস",
        "udid"              =>  "উদিদ",
        "user"              =>  "ব্যবহারকারী",
        "reported_user"     =>  "প্রতিবেদিত ব্যবহারকারী",
        "message"           =>  "বার্তা",
        "user_id"           =>  "ব্যবহারকারী",

        // Custom Resource Validation
        "contact_no"            =>  "যোগাযোগ নম্বর",
        "security_token"        =>  "নিরাপত্তা টোকেন",
        "first_name"            =>  "প্রথম নাম",
        "last_name"             =>  "শেষ নাম",
        "full_name"             =>  "পুরো নাম",
        "email"                 =>  "ইমেইল",
        "birth_date"            =>  "জন্ম তারিখ",
        "gender"                =>  "লিঙ্গ",
        "interest"              =>  "আগ্রহ",
        "location"              =>  "অবস্থান",
        "location_id"           =>  "অবস্থান",
        "language"              =>  "ভাষা",
        "languages"             =>  "ভাষা",
        "profile_photo"         =>  "প্রোফাইল ফটো",
        "country_code"          =>  "দেশের কোড",
        "room_name"             =>  "রুমের নাম",
        "room"                  =>  "রুম",
        "room_id"               =>  "রুম",
        "participant_id"        =>  "অংশগ্রহণকারী",
        "search"                =>  "অনুসন্ধান",
        "distance"              =>  "দূরত্ব",
        "parent_id"             =>  "দূরত্ব",
        "level"                 =>  "দূরত্ব",
        "attribute"             =>  "বিশিষ্ট",
        "interests"             =>  "আগ্রহ",
        "interests.*"           =>  "আগ্রহ",
        "images"                =>  "ছবি",
        "videos"                =>  "ভিডিও",
        "videos.*"              =>  "ভিডিও",
        "image"                 =>  "চিত্র",
        "image_path"            =>  "চিত্রের পথ",
        "api_key"               =>  "এপিআই কী",
        "api_secret"            =>  "api গোপন",
        "sid"                   =>  "sid",
        "push_id"               =>  "পুশ আইডি",
        "identity"              =>  "পরিচয়",
        "time_line"             =>  "টাইম লাইন",
        "facebook_id"           =>  "ফেসবুক আইডি",
        "google_id"             =>  "গুগল আইডি",
        "apple_id"              =>  "আপেল আইডি",
        "latitude"              =>  "অক্ষাংশ",
        "longitude"             =>  "দ্রাঘিমাংশ",
        "reason"                =>  "কারণ",
        "start_time"            =>  "শুরু করার সময়",
        "end_time"              =>  "শেষ সময়",
        "remaining_time"        =>  "বাকি সময়",
        "version"               =>  "সংস্করণ",
        "os"                    =>  "os",
        "app_version"           =>  "অ্যাপ সংস্করণ",
        "status"                =>  "স্থিতি",
        "about_me"              =>  "আমার সম্পর্কে",
        "fav_movie"             =>  "পছন্দের সিনেমা",
        "personalities"         =>  "ব্যক্তিত্ব",
        "remove_personalities"  =>  "ব্যক্তিত্ব অপসারণ করুন",
        "education"             =>  "শিক্ষা",
        "university_college"    =>  "বিশ্ববিদ্যালয় এবং কলেজ",
        "profession"            =>  "পেশা",
        "religion"              =>  "ধর্ম",
        "relationship_status"   =>  "rসম্পর্কের অবস্থা",
        "i_am_here"             =>  "আমি এখানে",
        "food_preference"       =>  "খাবার পছন্দ",
        "drinking"              =>  "পান করা",
        "smoking"               =>  "ধূমপান",
        "star_sign"             =>  "তারকা চিহ্ন",
        "community"             =>  "সম্প্রদায়",
        "old_profile_photo"     =>  "পুরনো প্রোফাইল ফটো",
        "old_images"            =>  "পুরানো ছবি",
        "voice"                 =>  "কণ্ঠ",
        "voice_answer"          =>  "কণ্ঠের উত্তর",
        "remove_voice"          =>  "ভয়েস সরান",
        "remove_video"          =>  "ভিডিও সরান",
        "remove_image"          =>  "ছবি সরান",
        "remove_interests"      =>  "আগ্রহগুলি সরান",
        "image_sequence"        =>  "ছবির ক্রম",
        "file"                  =>  "ফাইল",
    ],
];
