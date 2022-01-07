<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    /* Commnon Messages */
    'error' =>  'कुछ गलत हो गया। बाद में पुन: प्रयास करे',
    'success'   =>  ':entity की विवरण सफलतापूर्वक प्राप्त हुई',
    'add' => ':entity सफलतापूर्वक जोड़ा गया',
    'remove' => ':entity सफलतापूर्वक हटा दिया गया',
    'update' => ':entity सफलतापूर्वक बदल गया',
    'delete' => ':entity सफलतापूर्वक मिटाया गया',
    'list' => ':entity की सूची सफलतापूर्वक प्राप्त हुई',
    'not_found' => ':entity को सूची लाने में असमर्थ',
    'not_verified' => ':entity विश्वस्निय नहीं है',
    'not_activated' => ':entity सक्रिय नहीं है',
    'went_wrong'    =>  'उफ़! कुछ गलत हो गया है। कृपया बाद में दोबारा प्रयास करें',
    'not_empty'     => 'कृपया सही :entity चुने',
    'not_available' =>  ':entity उपलब्ध नहीं है',
    'not_liked'     =>  ':entity सफलतापूर्वक पसंद किया गया',
    'dis-liked'     =>  ':entity सफलतापूर्वक नापसंद किया गया',
    'follow'     =>  ':entity सफलतापूर्वक अनुसरण किया गया',
    'unfollow'     =>  ':entity सफलतापूर्वक अन-फॉलो किया गया',
    'already_added'     =>  ':entity कार्ट में पहले ही जोड़ा जा चुका',
    'already_offered'     =>  ':entity विक्रेता को पहले से ही पेशकश की गई है',
    'already_exists'     =>  ':entity पहले से ही मौजूद है',
    'not_saved'     =>  ':entity पहले से ही सेव किया गया',
    'save'     =>  ':entity सफलतापूर्वक सेव किया गया',
    'expired'     =>  ':entity समयसीमा समाप्त हो गई',
    'min_amount'    => ':entity :entity2 से बड़ा या बराबर होना चाहिए',
    'empty' => ':entity खाली है',
    'not_cancel'      => ':entity रद्द नहीं किया जा सकता',
    'max_referred' => 'इस रेफरल कोड का उपयोग नहीं किया जा सकता',
    'generated'  =>  ':entity सफलतापूर्वक उत्पन्न किया गया',
    'apply_success' =>  ':entity सफलतापूर्वक लागू किया गया',

    /* Login Messages */
    'login_fail' => 'आपकी जानकारी हमारे रिकॉर्ड से मेल नहीं खा रही',
    'in_active' => 'आप सक्रिय में नही हैं। कृपया व्यवस्थापक से संपर्क करें',
    'account_deleted' => 'आपका खाता व्यवस्थापक द्वारा हटा दिया गया है',
    'not_registered' => 'आप हमारे साथ पंजीकृत नहीं हैं',
    'login' => 'आप सफलतापूर्वक अपने खाते में प्रवेश कर रहे हैं',
    'registered' => 'आप हमारे साथ सफलतापूर्वक पंजीकृत हो गए हैं',
    'profile_setuped' => 'आपका प्रोफ़ाइल विवरण सफलतापूर्वक सहेजा गया है',
    'profile_setuped_fail' => 'प्रोफ़ाइल विवरण अपडेट करने में असमर्थ',
    'logout' => 'आप सफलतापूर्वक लॉगआउट हो गए हैं',
    'push_token_added' => 'पुश टोकन हमारे रिकॉर्ड में जोड़ा गया',

    /* Edit Profile */
    'invalid' => 'चुना हुआ :entity अमान्य है',
    'old_password' => 'पुराना :entity अमान्य है',
    'current_new_password_not_same' =>  'पुराना पासवर्ड और नया पासवर्ड अलग होना चाहिए',
    'not_adult' => 'उम्र कम से कम 18 साल होनी चाहिए',
    'not_exists' => ':entity मौजूद नहीं होना',
    'edit_profile' => 'प्रोफाइल को सफलतापूर्वक अपडेट किया गया',
    'edit_profile_image' => 'प्रोफ़ाइल चित्र सफलतापूर्वक अपडेट किया गया',
    'password_not_match' => 'पुराना पासवर्ड हमारे रिकॉर्ड से मेल नहीं खाता',

    /* Order Tracking */
    'to_many_request'   =>  'कई अनुरोधों के लिए',
    'already_cenceled'  =>  ':entity पहले ही रद्द किया गया',

    /* Reset Password */
    'link_sent'     =>  ':entity लिंक सफलतापूर्वक भेजा गया',
    'link_not_send' =>  'ई-मेल भेजने के लिए सक्षम करें। बाद में पुन: प्रयास करें',
    'reset_success' =>  'पासवर्ड सफलतापूर्वक रीसेट किया गया',
    
    'already_exists'    =>  ':entity विवरण पहले से मौजूद हैं',
    'dynamic-link'  =>  [
        'success'   =>  'गतिशील लिंक सफलतापूर्वक उत्पन्न हुई हैं',
        'fail'      =>  'गतिशील लिंक उत्पन्न करने में असमर्थ',
    ],
    'validate'          =>  ':entity सफलतापूर्वक सत्यापित!',
    'validate_fail'     =>  'अमान्य :entity, कृपया बाद में पुन: प्रयास करें',
    'qr'    =>  [
        'generated' =>  ':entity सफलतापूर्वक उत्पन्न!',
    ],
    'payment'   =>  [
        'success'   =>  [
            'url-generated' =>  'भुगतान url सफलतापूर्वक जनरेट किया गया'
        ],
        'fail'  =>  [
            'url-generated' =>  'भुगतान URL उत्पन्न करने में असमर्थ',
        ],
    ],

    /* Custom Message */    
];
