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

    'accepted' => 'ദി :attribute അംഗീകരിക്കണം.',
    'active_url' => 'ദി :attribute ഒരു സാധുവായ URL അല്ല.',
    'after' => 'ദി :attribute അതിന് ശേഷമുള്ള തീയതി ആയിരിക്കണം :date.',
    'after_or_equal' => 'ദി :attribute അതിന് ശേഷമുള്ളതോ അതിന് തുല്യമായതോ ആയിരിക്കണം :date.',
    'alpha' => 'ദി :attribute അക്ഷരങ്ങൾ മാത്രം അടങ്ങിയിരിക്കാം.',
    'alpha_dash' => 'ദി :attribute അക്ഷരങ്ങൾ, അക്കങ്ങൾ, ഡാഷുകൾ, അടിവരകൾ എന്നിവ മാത്രം അടങ്ങിയിരിക്കാം.',
    'alpha_num' => 'ദി :attribute അക്ഷരങ്ങളും അക്കങ്ങളും മാത്രം അടങ്ങിയിരിക്കാം.',
    'array' => 'ദി :attribute ഒരു അറേ ആയിരിക്കണം.',
    'before' => 'ദി :attribute മുമ്പുള്ള ഒരു തീയതി ആയിരിക്കണം :date.',
    'before_or_equal' => 'ദി :attribute അതിന് മുമ്പുള്ളതോ തുല്യമായതോ ആയ തീയതി ആയിരിക്കണം :date.',
    'between' => [
        'numeric' => 'ദി :attribute ഇടയിലായിരിക്കണം :min ഒപ്പം :max.',
        'file' => 'ദി :attribute ഇടയിലായിരിക്കണം :min ഒപ്പം :max കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute ഇടയിലായിരിക്കണം :min ഒപ്പം :max കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute ഇടയിൽ ഉണ്ടായിരിക്കണം :min ഒപ്പം :max ഇനങ്ങൾ.',
    ],
    'boolean' => 'ദി :attribute ഫീൽഡ് ശരിയോ തെറ്റോ ആയിരിക്കണം.',
    'confirmed' => 'ദി :attribute സ്ഥിരീകരണം പൊരുത്തപ്പെടുന്നില്ല.',
    'date' => 'ദി :attribute ഒരു സാധുവായ തീയതി അല്ല.',
    'date_equals' => 'ദി :attribute തുല്യമായ തീയതി ആയിരിക്കണം :date.',
    'date_format' => 'ദി :attribute ഫോർമാറ്റുമായി പൊരുത്തപ്പെടുന്നില്ല :format.',
    'different' => 'ദി :attribute ഒപ്പം :other വ്യത്യസ്തമായിരിക്കണം.',
    'digits' => 'ദി :attribute വ്യത്യസ്തമായിരിക്കണം :digits അക്കങ്ങൾ.',
    'digits_between' => 'ദി :attribute ഇടയിലായിരിക്കണം :min ഒപ്പം :max അക്കങ്ങൾ.',
    'dimensions' => 'ദി :attribute അസാധുവായ ഇമേജ് അളവുകൾ ഉണ്ട്.',
    'distinct' => 'ദി :attribute ഫീൽഡിന് ഒരു ഡ്യൂപ്ലിക്കേറ്റ് മൂല്യമുണ്ട്.',
    'email' => 'ദി :attribute വ്യത്യസ്തമായിരിക്കണം സാധുവായ ഒരു ഇമെയിൽ വിലാസം.',
    'ends_with' => 'ദി :attribute ഇനിപ്പറയുന്നവയിലൊന്നിൽ അവസാനിക്കണം: :values',
    'exists' => 'ദി തിരഞ്ഞെടുത്തു :attribute അസാധുവാണ്.',
    'file' => 'ദി :attribute വ്യത്യസ്തമായിരിക്കണം ഒരു ഫയല്.',
    'filled' => 'ദി :attribute ഫീൽഡിന് ഒരു മൂല്യം ഉണ്ടായിരിക്കണം.',
    'gt' => [
        'numeric' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം :value.',
        'file' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം :value കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം :value കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute ഉണ്ടായിരിക്കണം അതിലും കൂടുതൽ :value ഇനങ്ങൾ.',
    ],
    'gte' => [
        'numeric' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value.',
        'file' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute എന്നതിനേക്കാൾ വലുതായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute ഉണ്ടായിരിക്കണം :value ഇനങ്ങൾ അല്ലെങ്കിൽ കൂടുതൽ.',
    ],
    'image' => 'ദി :attribute ഒരു ചിത്രം ആയിരിക്കണം.',
    'in' => 'ദി തിരഞ്ഞെടുത്തു :attribute അസാധുവാണ്.',
    'in_array' => 'ദി :attribute ഫീൽഡ് നിലവിലില്ല :other.',
    'integer' => 'ദി :attribute ഒരു പൂർണ്ണസംഖ്യ ആയിരിക്കണം.',
    'ip' => 'ദി :attribute സാധുവായ ഒരു IP വിലാസം ആയിരിക്കണം.',
    'ipv4' => 'ദി :attribute സാധുവായ IPv4 വിലാസം ആയിരിക്കണം.',
    'ipv6' => 'ദി :attribute സാധുവായ IPv6 വിലാസം ആയിരിക്കണം.',
    'json' => 'ദി :attribute സാധുവായ ഒരു JSON സ്ട്രിംഗ് ആയിരിക്കണം.',
    'lt' => [
        'numeric' => 'ദി :attribute യിൽ കുറവായിരിക്കണം :value.',
        'file' => 'ദി :attribute യിൽ കുറവായിരിക്കണം :value കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute യിൽ കുറവായിരിക്കണം :value കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute യിൽ കുറവായിരിക്കണം :value ഇനങ്ങൾ.',
    ],
    'lte' => [
        'numeric' => 'ദി :attribute യിൽ കുറവായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value.',
        'file' => 'ദി :attribute യിൽ കുറവായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute യിൽ കുറവായിരിക്കണം അല്ലെങ്കിൽ തുല്യം :value കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute അധികം ഉണ്ടാകരുത് :value ഇനങ്ങൾ.',
    ],
    'max' => [
        'numeric' => 'ദി :attribute അതിലും വലുതായിരിക്കില്ല :max.',
        'file' => 'ദി :attribute അതിലും വലുതായിരിക്കില്ല :max കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute അതിലും വലുതായിരിക്കില്ല :max കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute അധികം ഉണ്ടാകണമെന്നില്ല :max ഇനങ്ങൾ.',
    ],
    'mimes' => 'ദി :attribute ഒരു ഫയൽ ആയിരിക്കണം of type: :values.',
    'mimetypes' => 'ദി :attribute ഒരു ഫയൽ ആയിരിക്കണം of type: :values.',
    'min' => [
        'numeric' => 'ദി :attribute കുറഞ്ഞത് ആയിരിക്കണം :min.',
        'file' => 'ദി :attribute കുറഞ്ഞത് ആയിരിക്കണം :min കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute കുറഞ്ഞത് ആയിരിക്കണം :min കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute കുറഞ്ഞത് ഉണ്ടായിരിക്കണം :min ഇനങ്ങൾ.',
    ],
    'not_in' => 'ദി തിരഞ്ഞെടുത്തു :attribute അസാധുവാണ്.',
    'not_regex' => 'ദി :attribute ഫോർമാറ്റ് അസാധുവാണ്.',
    'numeric' => 'ദി :attribute ഒരു സംഖ്യ ആയിരിക്കണം.',
    'present' => 'ദി :attribute ഫീൽഡ് ഉണ്ടായിരിക്കണം.',
    'regex' => 'ദി :attribute format അസാധുവാണ്.',
    'required' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ്.',
    'required_if' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് എപ്പോൾ :other ആണ് :value.',
    'required_unless' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് unless :other ആണ് ഇൻ :values.',
    'required_with' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് എപ്പോൾ :values ആണ് വർത്തമാന.',
    'required_with_all' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് എപ്പോൾ :values ആകുന്നു വർത്തമാന.',
    'required_without' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് എപ്പോൾ :values ആണ് അല്ല വർത്തമാന.',
    'required_without_all' => 'ദി :attribute ഫീൽഡ് ആവശ്യമാണ് എപ്പോൾ ആരും :values ആകുന്നു വർത്തമാന.',
    'same' => 'ദി :attribute ഒപ്പം :other ചേർന്നേ പറ്റുള്ളൂ.',
    'size' => [
        'numeric' => 'ദി :attribute ചെയ്തിരിക്കണം :size.',
        'file' => 'ദി :attribute ചെയ്തിരിക്കണം :size കിലോബൈറ്റുകൾ.',
        'string' => 'ദി :attribute ചെയ്തിരിക്കണം :size കഥാപാത്രങ്ങൾ.',
        'array' => 'ദി :attribute must contain :size ഇനങ്ങൾ.',
    ],
    'starts_with' => 'ദി :attribute ഇനിപ്പറയുന്നതിൽ ഒന്നിൽ നിന്ന് ആരംഭിക്കണം: :values',
    'string' => 'ദി :attribute ചെയ്തിരിക്കണം ഒരു ചരട്.',
    'timezone' => 'ദി :attribute ചെയ്തിരിക്കണം ഒരു സാധുവായ മേഖല.',
    'unique' => 'ദി :attribute ഇതിനകം എടുത്തുകഴിഞ്ഞു.',
    'uploaded' => 'ദി :attribute അപ്‌ലോഡ് ചെയ്യുന്നതിൽ പരാജയപ്പെട്ടു.',
    'url' => 'ദി :attribute format അസാധുവാണ്.',
    'uuid' => 'ദി :attribute ചെയ്തിരിക്കണം ഒരു സാധുവായ UUID.',

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

    'not_empty' => "ദി :attribute സ്ഥലം മാത്രം അനുവദിക്കുന്നില്ല.",
    'not_exists' => 'ദി :attribute നിലവിലില്ല.',
    'no_space' => "ദി :attribute സ്ഥലം പാടില്ല.",
    'not_match' => 'ദി :attribute ഞങ്ങളുടെ റെക്കോർഡുകളുമായി പൊരുത്തപ്പെടുന്നില്ല.',
    'not_equal' => 'ദി :attribute ഒപ്പം :other ഒരുപോലെ ആയിരിക്കരുത്',
    'equal_to' => 'ദി :attribute ഒപ്പം :other ചെയ്തിരിക്കണം അതേ',
    'lettersonly' => 'ദി :attribute അക്ഷരങ്ങൾ മാത്രം അടങ്ങിയിരിക്കാം ഒപ്പം ഇടങ്ങൾ.',
    'alpha_numeric' => 'ദി :attribute അക്ഷരങ്ങൾ മാത്രം അടങ്ങിയിരിക്കാം, സംഖ്യകൾ ഒപ്പം ഇടങ്ങൾ.',
    'image_dimentions' => 'ദി :attribute ചെയ്തിരിക്കണം ന്റെ :min x :max px അളവുകൾ.',
    'emoji_found'   =>  'ഇമോജികൾ അനുവദിക്കില്ല :attribute',

    // 'duration'      =>  'ദി duration of video ഇടയിലായിരിക്കണം 1 to 60 seconds only.',
    'duration'      =>  'ദി duration of video അതിലും വലുതായിരിക്കില്ല 60 സെക്കന്റുകൾ.',
    'video'         =>  [
        'portrait'  =>  'വീഡിയോ ചെയ്തിരിക്കണം പോർട്രെയിറ്റ് മോഡിൽ',
        'landscape' =>  'വീഡിയോ ചെയ്തിരിക്കണം ലാൻഡ്സ്കേപ്പ് മോഡിൽ',
    ],

    'attributes' => [
        "field"         =>  "വയൽ",
        "password"      =>  "password",
        "name"          =>  "പേര്",
        "username"      =>  "ഉപയോക്തൃനാമം",
        "contact"       =>  "ബന്ധപ്പെടുക",
        "country_id"    =>  "രാജ്യം",
        "country"       =>  "രാജ്യം",
        "start_age"     =>  "പ്രായം ആരംഭിക്കുക",
        "end_age"       =>  "അവസാന പ്രായം",
        "public"        =>  [
            "email"     =>  "ഇമെയിൽ",
            "contact"   =>  "ബന്ധപ്പെടുക",
        ],
        "description"   =>  "വിവരണം",
        "link"          =>  "ലിങ്ക്",
        "city"          =>  "നഗരം",
        "remove_profile"    =>  "പ്രൊഫൈൽ നീക്കം ചെയ്യുക",
        "birthday"          =>  "ജന്മദിനം",
        "profile"           =>  "പ്രൊഫൈൽ",
        "old_password"      =>  "പഴയ പാസ്വേഡ്",
        "token"             =>  "ടോക്കൺ",
        "device_type"       =>  "ഉപകരണ തരം",
        "checksum"          =>  "ചെക്ക്സം",
        "limit"             =>  "പരിധി",
        "offset"            =>  "ഓഫ്സെറ്റ്",
        "type"              =>  "തരം",
        "category"          =>  "വിഭാഗം",
        "thumbnail"         =>  "ലഘുചിത്രം",
        "video"             =>  "വീഡിയോ",
        "duration"          =>  "കാലാവധി",
        "hashtags"          =>  "ഹാഷ്ടാഗുകൾ",
        "cause"             =>  "കാരണമാകുന്നു",
        "state"             =>  "സംസ്ഥാനം",
        "id"                =>  "ഐഡി",
        "keyword"           =>  "കീവേഡ്",
        "device"            =>  "ഉപകരണം",
        "udid"              =>  "udid",
        "user"              =>  "ഉപയോക്താവ്",
        "reported_user"     =>  "റിപ്പോർട്ട് ചെയ്ത ഉപയോക്താവ്",
        "message"           =>  "സന്ദേശം",
        "user_id"           =>  "ഉപയോക്താവ്",

        // Custom Resource Validation
        "contact_no"            =>  "ബന്ധപ്പെടാനുള്ള നമ്പർ",
        "security_token"        =>  "സുരക്ഷാ ടോക്കൺ",
        "first_name"            =>  "പേരിന്റെ ആദ്യഭാഗം",
        "last_name"             =>  "പേരിന്റെ അവസാന ഭാഗം",
        "full_name"             =>  "പൂർണ്ണമായ പേര്",
        "email"                 =>  "ഇമെയിൽ",
        "birth_date"            =>  "ജനിച്ച ദിവസം",
        "gender"                =>  "ലിംഗഭേദം",
        "interest"              =>  "പലിശ",
        "location"              =>  "സ്ഥാനം",
        "location_id"           =>  "സ്ഥാനം",
        "language"              =>  "ഭാഷ",
        "languages"             =>  "ഭാഷകൾ",
        "profile_photo"         =>  "പ്രൊഫൈൽ ഫോട്ടോ",
        "country_code"          =>  "രാജ്യത്തിന്റെ കോഡ്",
        "room_name"             =>  "മുറിയുടെ പേര്",
        "room"                  =>  "മുറി",
        "room_id"               =>  "മുറി",
        "participant_id"        =>  "പങ്കാളി",
        "search"                =>  "തിരയുക",
        "distance"              =>  "ദൂരം",
        "parent_id"             =>  "രക്ഷിതാവ്",
        "level"                 =>  "നില",
        "attribute"             =>  "ആട്രിബ്യൂട്ട്",
        "interests"             =>  "താൽപ്പര്യങ്ങൾ",
        "interests.*"           =>  "താൽപ്പര്യങ്ങൾ",
        "images"                =>  "ചിത്രങ്ങൾ",
        "videos"                =>  "വീഡിയോകൾ",
        "videos.*"              =>  "വീഡിയോകൾ",
        "image"                 =>  "ചിത്രം",
        "image_path"            =>  "ചിത്ര പാത",
        "api_key"               =>  "api കീ",
        "api_secret"            =>  "api രഹസ്യം",
        "sid"                   =>  "സിഡ്",
        "push_id"               =>  "പുഷ് ഐഡി",
        "identity"              =>  "ഐഡന്റിറ്റി",
        "time_line"             =>  "സമയരേഖ",
        "facebook_id"           =>  "ഫേസ്ബുക്ക് ഐഡി",
        "google_id"             =>  "ഗൂഗിൾ ഐഡി",
        "apple_id"              =>  "ആപ്പിൾ ഐഡി",
        "latitude"              =>  "അക്ഷാംശം",
        "longitude"             =>  "രേഖാംശം",
        "reason"                =>  "കാരണം",
        "start_time"            =>  "ആരംഭ സമയം",
        "end_time"              =>  "അവസാന സമയം",
        "remaining_time"        =>  "ശേഷിക്കുന്ന സമയം",
        "version"               =>  "പതിപ്പ്",
        "os"                    =>  "os",
        "app_version"           =>  "അപ്ലിക്കേഷൻ പതിപ്പ്",
        "status"                =>  "പദവി",
        "about_me"              =>  "എന്നെ പറ്റി",
        "fav_movie"             =>  "പ്രിയപ്പെട്ട സിനിമ",
        "personalities"         =>  "വ്യക്തിത്വങ്ങൾ",
        "remove_personalities"  =>  "വ്യക്തിത്വങ്ങളെ നീക്കം ചെയ്യുക",
        "education"             =>  "വിദ്യാഭ്യാസം",
        "university_college"    =>  "യൂണിവേഴ്സിറ്റി കോളേജ്",
        "profession"            =>  "തൊഴിൽ",
        "religion"              =>  "മതം",
        "relationship_status"   =>  "ബന്ധ നില",
        "i_am_here"             =>  "ഞാൻ ഇവിടെ ഉണ്ട്",
        "food_preference"       =>  "ഭക്ഷണ മുൻഗണന",
        "drinking"              =>  "കുടിക്കുന്നു",
        "smoking"               =>  "പുകവലി",
        "star_sign"             =>  "നക്ഷത്ര ചിഹ്നം",
        "community"             =>  "സമൂഹം",
        "old_profile_photo"     =>  "പഴയ പ്രൊഫൈൽ ഫോട്ടോ",
        "old_images"            =>  "പഴയ ചിത്രങ്ങൾ",
        "voice"                 =>  "ശബ്ദം",
        "voice_answer"          =>  "ശബ്ദം ഉത്തരം",
        "remove_voice"          =>  "ശബ്ദം നീക്കം ചെയ്യുക",
        "remove_video"          =>  "വീഡിയോ നീക്കം ചെയ്യുക",
        "remove_image"          =>  "ചിത്രം നീക്കം ചെയ്യുക",
        "remove_interests"      =>  "താൽപ്പര്യങ്ങൾ നീക്കം ചെയ്യുക",
        "image_sequence"        =>  "ചിത്ര ക്രമം",
        "file"                  =>  "ഫയൽ",
    ],
];
