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

    'accepted' => ':attribute ଗ୍ରହଣ କରିବା ଜରୁରୀ |',
    'active_url' => ':attribute ଏକ ବ valid ଧ URL ନୁହେଁ |.',
    'after' => ':attribute ପରେ ଏକ ତାରିଖ ହେବା ଜରୁରୀ | :date.',
    'after_or_equal' => ':attribute ଏକ ତାରିଖ ପରେ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ | :date.',
    'alpha' => ':attribute କେବଳ ଅକ୍ଷର ଧାରଣ କରିପାରେ |.',
    'alpha_dash' => ':attribute କେବଳ ଅକ୍ଷର ଧାରଣ କରିପାରେ |, ସଂଖ୍ୟା, ଡ୍ୟାସ୍ ଏବଂ ଅଣ୍ଡରସ୍କୋର୍ |',
    'alpha_num' => ':attribute କେବଳ ଅକ୍ଷର ଧାରଣ କରିପାରେ | ଏବଂ ସଂଖ୍ୟାଗୁଡିକ.',
    'array' => ':attribute ଏକ ଆରେ ହେବା ଜରୁରୀ |',
    'before' => ':attribute ପୂର୍ବରୁ ଏକ ତାରିଖ ହେବା ଜରୁରୀ | :date.',
    'before_or_equal' => ':attribute ପୂର୍ବରୁ ଏକ ତାରିଖ ହେବା ଜରୁରୀ | କିମ୍ବା ସମାନ | :date.',
    'between' => [
        'numeric' => ':attribute ମଧ୍ୟରେ ରହିବା ଜରୁରୀ | :min ଏବଂ :max.',
        'file' => ':attribute ମଧ୍ୟରେ ରହିବା ଜରୁରୀ | :min ଏବଂ :max କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ମଧ୍ୟରେ ରହିବା ଜରୁରୀ | :min ଏବଂ :max ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ମଧ୍ୟରେ ରହିବା ଜରୁରୀ | :min ଏବଂ :max ଆଇଟମ୍.',
    ],
    'boolean' => ':attribute କ୍ଷେତ୍ରଟି ସତ କିମ୍ବା ମିଥ୍ୟା ହେବା ଜରୁରୀ |.',
    'confirmed' => ':attribute ନିଶ୍ଚିତକରଣ ମେଳ ଖାଉ ନାହିଁ |.',
    'date' => ':attribute ଏକ ବ valid ଧ ତାରିଖ ନୁହେଁ |',
    'date_equals' => ':attribute ସମାନ ତାରିଖ ହେବା ଜରୁରୀ | :date.',
    'date_format' => ':attribute ଫର୍ମାଟ୍ ସହିତ ମେଳ ଖାଉ ନାହିଁ | :format.',
    'different' => ':attribute ଏବଂ :other ଅଲଗା ହେବା ଜରୁରୀ |',
    'digits' => ':attribute ହେବାକୁପଡିବ :digits ସଂଖ୍ୟା.',
    'digits_between' => ':attribute ମଧ୍ୟରେ ରହିବା ଜରୁରୀ | :min ଏବଂ :max ସଂଖ୍ୟା.',
    'dimensions' => ':attribute ଅବ inv ଧ ପ୍ରତିଛବି ପରିମାଣ ଅଛି |',
    'distinct' => ':attribute ଫିଲ୍ଡର ଏକ ନକଲ ମୂଲ୍ୟ ଅଛି |.',
    'email' => ':attribute ଏକ ବ valid ଧ ଇମେଲ୍ ଠିକଣା ହେବା ଜରୁରୀ |.',
    'ends_with' => ':attribute ନିମ୍ନଲିଖିତ ମଧ୍ୟରୁ ଗୋଟିଏ ସହିତ ଶେଷ ହେବା ଜରୁରୀ: :values',
    'exists' => 'ମନୋନୀତ | :attribute ଅବ alid ଧ ଅଟେ |',
    'file' => ':attribute ଏକ ଫାଇଲ୍ ହେବା ଜରୁରୀ |',
    'filled' => ':attribute କ୍ଷେତ୍ରର ଏକ ମୂଲ୍ୟ ଥିବା ଆବଶ୍ୟକ |',
    'gt' => [
        'numeric' => ':attribute ଠାରୁ ବଡ ହେବା ଜରୁରୀ | :value.',
        'file' => ':attribute ଠାରୁ ବଡ ହେବା ଜରୁରୀ | :value କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ଠାରୁ ବଡ ହେବା ଜରୁରୀ | :value ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଠାରୁ ଅଧିକ ଥିବା ଆବଶ୍ୟକ | :value ଆଇଟମ୍.',
    ],
    'gte' => [
        'numeric' => ':attribute ଠାରୁ ବଡ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ | :value.',
        'file' => ':attribute ଠାରୁ ବଡ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ | :value କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ଠାରୁ ବଡ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ | :value ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ନିଶ୍ଚୟ ଅଛି | :value ଆଇଟମ୍ କିମ୍ବା ଅଧିକ |.',
    ],
    'image' => ':attribute ଏକ ପ୍ରତିଛବି ହେବା ଜରୁରୀ |',
    'in' => 'ମନୋନୀତ :attribute ଅବ alid ଧ ଅଟେ |.',
    'in_array' => ':attribute କ୍ଷେତ୍ର ବିଦ୍ୟମାନ ନାହିଁ | :other.',
    'integer' => ':attribute ଏକ ପୂର୍ଣ୍ଣ ସଂଖ୍ୟା ହେବା ଜରୁରୀ |.',
    'ip' => ':attribute ଏକ ବ valid ଧ IP ଠିକଣା ହେବା ଜରୁରୀ |.',
    'ipv4' => ':attribute ଏକ ବ valid ଧ IPv4 ଠିକଣା ହେବା ଜରୁରୀ |.',
    'ipv6' => ':attribute ଏକ ବ valid ଧ IPv6 ଠିକଣା ହେବା ଜରୁରୀ |',
    'json' => ':attribute ଏକ ବ valid ଧ JSON ଷ୍ଟ୍ରିଙ୍ଗ୍ ହେବା ଜରୁରୀ |',
    'lt' => [
        'numeric' => ':attribute ଠାରୁ କମ୍ ହେବା ଜରୁରୀ :value.',
        'file' => ':attribute ଠାରୁ କମ୍ ହେବା ଜରୁରୀ :value କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ଠାରୁ କମ୍ ହେବା ଜରୁରୀ :value ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଠାରୁ କମ୍ ଥିବା ଆବଶ୍ୟକ :value ଆଇଟମ୍.',
    ],
    'lte' => [
        'numeric' => ':attribute କମ୍ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ :value.',
        'file' => ':attribute କମ୍ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ :value କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute କମ୍ କିମ୍ବା ସମାନ ହେବା ଜରୁରୀ :value ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଠାରୁ ଅଧିକ ନଥିବା ଆବଶ୍ୟକ :value ଆଇଟମ୍.',
    ],
    'max' => [
        'numeric' => ':attribute ଠାରୁ ବଡ ହୋଇନପାରେ | :max.',
        'file' => ':attribute ଠାରୁ ବଡ ହୋଇନପାରେ | :max କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ଠାରୁ ବଡ ହୋଇନପାରେ | :max ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଠାରୁ ଅଧିକ ନ ଥାଇପାରେ :max ଆଇଟମ୍.',
    ],
    'mimes' => ':attribute ଏକ ପ୍ରକାରର ଫାଇଲ୍ ହେବା ଜରୁରୀ: :values.',
    'mimetypes' => ':attribute ଏକ ପ୍ରକାରର ଫାଇଲ୍ ହେବା ଜରୁରୀ: :values.',
    'min' => [
        'numeric' => ':attribute ଅତିକମରେ ହେବା ଜରୁରୀ | :min.',
        'file' => ':attribute ଅତିକମରେ ହେବା ଜରୁରୀ | :min କିଲୋବାଇଟ୍.',
        'string' => ':attribute ଅତିକମରେ ହେବା ଜରୁରୀ | :min ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଅତିକମରେ ରହିବା ଜରୁରୀ | :min ଆଇଟମ୍',
    ],
    'not_in' => 'ମନୋନୀତ | :attribute ଅବ alid ଧ ଅଟେ |',
    'not_regex' => ':attribute ଫର୍ମାଟ୍ ଅବ alid ଧ ଅଟେ |',
    'numeric' => ':attribute ଦୁଇଟି ସଂଖ୍ୟା ହେବା ଜରୁରୀ |.',
    'present' => ':attribute କ୍ଷେତ୍ର ଉପସ୍ଥିତ ରହିବା ଜରୁରୀ |.',
    'regex' => ':attribute ଫର୍ମାଟ୍ ବ valid ଧ ଅଟେ |.',
    'required' => ':attribute କ୍ଷେତ୍ର ଆବଶ୍ୟକ |',
    'required_if' => ':attribute କେତେବେଳେ ଫିଲ୍ଡ ଆବଶ୍ୟକ ହୁଏ | :other is :value.',
    'required_unless' => ':attribute ଯେପର୍ଯ୍ୟନ୍ତ ଫିଲ୍ଡ ଆବଶ୍ୟକ ନାହିଁ | :other ରେ :values.',
    'required_with' => ':attribute କେତେବେଳେ ଫିଲ୍ଡ ଆବଶ୍ୟକ ହୁଏ | :values ଉପସ୍ଥିତ ଅଛି |',
    'required_with_all' => ':attribute କେତେବେଳେ ଫିଲ୍ଡ ଆବଶ୍ୟକ ହୁଏ | :values ଉପସ୍ଥିତ ଅଛନ୍ତି |.',
    'required_without' => ':attribute କେତେବେଳେ ଫିଲ୍ଡ ଆବଶ୍ୟକ ହୁଏ | :values ଉପସ୍ଥିତ ନାହିଁ.',
    'required_without_all' => ':attribute କେତେବେଳେ ଫିଲ୍ଡ ଆବଶ୍ୟକ ହୁଏ | none of :values ଉପସ୍ଥିତ ଅଛନ୍ତି |.',
    'same' => ':attribute ଏବଂ :other ନିଶ୍ଚିତ ଭାବରେ ମେଳ ହେବ |',
    'size' => [
        'numeric' => ':attribute ହେବାକୁପଡିବ :size.',
        'file' => ':attribute ହେବାକୁପଡିବ :size କିଲୋବାଇଟ୍ |.',
        'string' => ':attribute ହେବାକୁପଡିବ :size ବର୍ଣ୍ଣଗୁଡିକ.',
        'array' => ':attribute ଧାରଣ କରିବା ଜରୁରୀ |:size ଆଇଟମ୍.',
    ],
    'starts_with' => ':attribute ନିମ୍ନଲିଖିତ ମଧ୍ୟରୁ ଗୋଟିଏ ସହିତ ଆରମ୍ଭ କରିବା ଜରୁରୀ: :values',
    'string' => ':attribute ଏକ ଷ୍ଟ୍ରିଙ୍ଗ୍ ହେବା ଜରୁରୀ |',
    'timezone' => ':attribute ଏକ ବ valid ଧ ଜୋନ୍ ହେବା ଜରୁରୀ |',
    'unique' => ':attribute ପୂର୍ବରୁ ନିଆଯାଇଛି',
    'uploaded' => ':attribute ଅପଲୋଡ୍ କରିବାରେ ବିଫଳ ହେଲା |',
    'url' => ':attribute ଫର୍ମାଟ୍ ଅବ alid ଧ ଅଟେ |',
    'uuid' => ':attribute ଏକ ବ valid ଧ UUID ହେବା ଜରୁରୀ |.',

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

    'not_empty' => ":attribute କେବଳ ଜାଗା ଅନୁମତି ଦିଏ ନାହିଁ |",
    'not_exists' => ':attribute ବିଦ୍ୟମାନ ନାହିଁ |',
    'no_space' => ":attribute ଜାଗା ନଥିବା ଆବଶ୍ୟକ |",
    'not_match' => ':attribute ଆମର ରେକର୍ଡଗୁଡିକ ସହିତ ମେଳ ଖାଉ ନାହିଁ |.',
    'not_equal' => ':attribute ଏବଂ :other ସମାନ ହେବା ଉଚିତ ନୁହେଁ |',
    'equal_to' => ':attribute ଏବଂ :other ସମାନ ହେବା ଜରୁରୀ |',
    'lettersonly' => ':attribute କେବଳ ଅକ୍ଷର ଏବଂ ସ୍ପେସ୍ ଧାରଣ କରିପାରେ |.',
    'alpha_numeric' => ':attribute କେବଳ ଅକ୍ଷର, ସଂଖ୍ୟା ଏବଂ ସ୍ପେସ୍ ଧାରଣ କରିପାରେ |',
    'image_dimentions' => ':attributeର ହେବା ଜରୁରୀ | :min px :max px ପରିମାପ |.',
    'emoji_found'   =>  'ଇମୋଜିଗୁଡିକ ଅନୁମତିପ୍ରାପ୍ତ ନୁହେଁ | :attribute',

    // 'duration'      =>  'duration of video must be between 1 to 60 seconds only.',
    'duration'      =>  'ଭିଡିଓର ଅବଧି 60 ସେକେଣ୍ଡରୁ ଅଧିକ ହୋଇନପାରେ |',
    'video'         =>  [
        'portrait'  =>  'ଭିଡିଓ ନିଶ୍ଚିତ ଭାବରେ ପୋଟ୍ରେଟ୍ ମୋଡ୍ ରେ ଅଛି |',
        'landscape' =>  'ଭିଡିଓ ଲ୍ୟାଣ୍ଡସ୍କେପ୍ ମୋଡ୍ ରେ ରହିବା ଜରୁରୀ |',
    ],

    'attributes' => [
        "field"         =>  "କ୍ଷେତ୍ର",
        "password"      =>  "ପାସୱାର୍ଡ",
        "name"          =>  "ନାମ",
        "username"      =>  "ଉପଯୋଗକର୍ତ୍ତା ନାମ",
        "contact"       =>  "ଯୋଗାଯୋଗ କରନ୍ତୁ |",
        "country_id"    =>  "ଦେଶ",
        "country"       =>  "ଦେଶ",
        "start_age"     =>  "ବୟସ ଆରମ୍ଭ",
        "end_age"       =>  "ଶେଷ ବୟସ",
        "public"        =>  [
            "email"     =>  "ଇମେଲ୍ |",
            "contact"   =>  "ଯୋଗାଯୋଗ କରନ୍ତୁ |",
        ],
        "description"   =>  "ବର୍ଣ୍ଣନା",
        "link"          =>  "ଲିଙ୍କ୍",
        "city"          =>  "ସହର",
        "remove_profile"    =>  "ପ୍ରୋଫାଇଲ୍ ଅପସାରଣ କରନ୍ତୁ |",
        "birthday"          =>  "ଜନ୍ମଦିନ",
        "profile"           =>  "ପ୍ରୋଫାଇଲ୍",
        "old_password"      =>  "ପୁରୁଣା ପ୍ରବେଶ ସବ୍ଦ",
        "token"             =>  "ଟୋକନ୍",
        "device_type"       =>  "ଉପକରଣ ପ୍ରକାର",
        "checksum"          =>  "ଚେକସମ୍ |",
        "limit"             =>  "ସୀମା",
        "offset"            =>  "ଅଫସେଟ",
        "type"              =>  "ଟାଇପ୍ କରନ୍ତୁ |",
        "category"          =>  "ବର୍ଗ",
        "thumbnail"         =>  "ଥମ୍ na ନେଲ୍ |",
        "video"             =>  "ଭିଡିଓ",
        "duration"          =>  "ଅବଧି",
        "hashtags"          =>  "ହ୍ୟାସଟ୍ୟାଗ୍",
        "cause"             =>  "କାରଣ",
        "state"             =>  "ରାଜ୍ୟ",
        "id"                =>  "id",
        "keyword"           =>  "କୀ ଶବ୍ଦ",
        "device"            =>  "ଉପକରଣ",
        "udid"              =>  "udid",
        "user"              =>  "ଉପଯୋଗକର୍ତ୍ତା |",
        "reported_user"     =>  "ରିପୋର୍ଟ ହୋଇଥିବା ଉପଭୋକ୍ତା |",
        "message"           =>  "ବାର୍ତ୍ତା |",
        "user_id"           =>  "ଉପଯୋଗକର୍ତ୍ତା |",

        // Custom Resource Validation
        "contact_no"            =>  "ଯୋଗାଯୋଗ ନମ୍ବର",
        "security_token"        =>  "ସୁରକ୍ଷା ଟୋକନ୍ |",
        "first_name"            =>  "ପ୍ରଥମ ନାମ",
        "last_name"             =>  "ଶେଷ ନାମ",
        "full_name"             =>  "ପୁରା ନାମ",
        "email"                 =>  "ଇମେଲ୍ |",
        "birth_date"            =>  "ଜନ୍ମ ତାରିଖ",
        "gender"                =>  "ଲିଙ୍ଗ",
        "interest"              =>  "ଆଗ୍ରହ",
        "location"              =>  "ଅବସ୍ଥାନ",
        "location_id"           =>  "ଅବସ୍ଥାନ",
        "language"              =>  "ଭାଷା",
        "languages"             =>  "ଭାଷା",
        "profile_photo"         =>  "ପ୍ରୋଫାଇଲ୍ ଫଟୋ",
        "country_code"          =>  "ଦେଶ କୋଡ୍",
        "room_name"             =>  "କୋଠରୀ ନାମ",
        "room"                  =>  "କୋଠରୀ",
        "room_id"               =>  "କୋଠରୀ",
        "participant_id"        =>  "ଅଂଶଗ୍ରହଣକାରୀ",
        "search"                =>  "ସନ୍ଧାନ",
        "distance"              =>  "ଦୂରତା",
        "parent_id"             =>  "ପିତାମାତା |",
        "level"                 =>  "ସ୍ତର",
        "attribute"             =>  "ଗୁଣ",
        "interests"             =>  "ଆଗ୍ରହ",
        "interests.*"           =>  "ଆଗ୍ରହ",
        "images"                =>  "ପ୍ରତିଛବିଗୁଡିକ",
        "videos"                =>  "ଭିଡିଓଗୁଡିକ",
        "videos.*"              =>  "ଭିଡିଓଗୁଡିକ",
        "image"                 =>  "ପ୍ରତିଛବି |",
        "image_path"            =>  "ପ୍ରତିଛବି ପଥ",
        "api_key"               =>  "api କି",
        "api_secret"            =>  "api ଗୁପ୍ତ",
        "sid"                   =>  "ସାଇଡ୍",
        "push_id"               =>  "ପୁସ୍ id",
        "identity"              =>  "ପରିଚୟ",
        "time_line"             =>  "ସମୟ ରେଖା |",
        "facebook_id"           =>  "facebook id",
        "google_id"             =>  "google id",
        "apple_id"              =>  "ଆପଲ୍ id",
        "latitude"              =>  "ଅକ୍ଷାଂଶ",
        "longitude"             =>  "ଦ୍ରାଘିମା",
        "reason"                =>  "କାରଣ",
        "start_time"            =>  "ଆରମ୍ଭ ସମୟ",
        "end_time"              =>  "ଶେଷ ସମୟ",
        "remaining_time"        =>  "ଅବଶିଷ୍ଟ ସମୟ",
        "version"               =>  "ସଂସ୍କରଣ",
        "os"                    =>  "os",
        "app_version"           =>  "ଆପ୍ ସଂସ୍କରଣ |",
        "status"                =>  "ସ୍ଥିତି",
        "about_me"              =>  "ମୋ ବିସୟରେ",
        "fav_movie"             =>  "ପ୍ରିୟ ଚଳଚ୍ଚିତ୍ର",
        "personalities"         =>  "ବ୍ୟକ୍ତିତ୍ୱ",
        "remove_personalities"  =>  "ବ୍ୟକ୍ତିତ୍ୱଗୁଡିକ ଅପସାରଣ କରନ୍ତୁ |",
        "education"             =>  "ଶିକ୍ଷା",
        "university_college"    =>  "ବିଶ୍ୱବିଦ୍ୟାଳୟ ଏବଂ କଲେଜ",
        "profession"            =>  "ବୃତ୍ତି",
        "religion"              =>  "ଧର୍ମ",
        "relationship_status"   =>  "ସମ୍ପର୍କ ସ୍ଥିତି",
        "i_am_here"             =>  "ମୁ ଏଠାରେ ଅଛି",
        "food_preference"       =>  "ଖାଦ୍ୟ ପସନ୍ଦ",
        "drinking"              =>  "ପିଇବା",
        "smoking"               =>  "ଧୂମପାନ",
        "star_sign"             =>  "ତାରା ଚିହ୍ନ",
        "community"             =>  "ସମ୍ପ୍ରଦାୟ",
        "old_profile_photo"     =>  "ପୁରୁଣା ପ୍ରୋଫାଇଲ୍ ଫଟୋ |",
        "old_images"            =>  "ପୁରୁଣା ପ୍ରତିଛବିଗୁଡ଼ିକ |",
        "voice"                 =>  "ସ୍ୱର",
        "voice_answer"          =>  "ସ୍ୱର ଉତ୍ତର",
        "remove_voice"          =>  "ସ୍ୱର ଅପସାରଣ କରନ୍ତୁ |",
        "remove_video"          =>  "ଭିଡିଓ ଅପସାରଣ କରନ୍ତୁ |",
        "remove_image"          =>  "ପ୍ରତିଛବି ଅପସାରଣ କରନ୍ତୁ |",
        "remove_interests"      =>  "ଆଗ୍ରହ ହଟାନ୍ତୁ |",
        "image_sequence"        =>  "ପ୍ରତିଛବି କ୍ରମ |",
        "file"                  =>  "ଫାଇଲ୍ |",
        "message_id"            =>  "ବାର୍ତ୍ତା |",
    ],
];
