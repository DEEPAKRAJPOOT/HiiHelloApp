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
    'fail'   =>  ':entity की विवरण असफल प्राप्त हुई',
    'add' => ':entity सफलतापूर्वक जोड़ा गया',
    'remove' => ':entity सफलतापूर्वक हटा दिया गया',
    'update' => ':entity सफलतापूर्वक अपडेट किया गया है',
    'delete' => ':entity सफलतापूर्वक हटा दी गई',
    'list' => ':entity सफलतापूर्वक पुनर्प्राप्त की गई',
    'not_found' => ':entity नहीं मिली',
    'not_verified' => 'आपका :entity सत्यापित नहीं किया जा सका|',
    'not_activated' => ':entity सक्रिय नहीं है',
    'subscription_required' => 'आपकी सदस्यता सक्रिय नहीं है। जारी रखने के लिए कृपया सदस्यता लें।',
    'went_wrong'    =>  'ओह! कुछ गलत हो गया है। कृपया बाद में दोबारा प्रयास करें',
    'not_empty'     => 'कृपया सही :entity चुने',
    'not_available' =>  ':entity उपलब्ध नहीं हैं',
    'liked'         =>  ':entity सफलतापूर्वक पसंद किया गया',
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
    'swipe_over' =>  'आपकी दैनिक स्वाइप सीमा समाप्त हो गई है। जारी रखने के लिए अपग्रेड करें।',

    /* Login Messages */
    'login_fail' => 'ये प्रमाण हमारे रिकॉर्ड से मेल नहीं खाते हैं',
    'in_active' => 'तुम अवरुद्ध हो। कृपया प्रशासनिक से संपर्क करें',
    'account_deleted' => 'आपका खाता व्यवस्थापक द्वारा हटा दिया गया है',
    'not_registered' => 'आप अवरुद्ध हो। कृपया प्रशासनिक से संपर्क करें',
    'login' => 'आपने अपने खाते में सफलतापूर्वक प्रवेश कर लिया है',
    'registered' => 'आप हमारे साथ सफलतापूर्वक रजिस्टर्ड हैं',
    'profile_setuped' => 'आपका प्रोफ़ाइल विवरण सफलतापूर्वक सहेजा गया है',
    'profile_setuped_fail' => 'प्रोफ़ाइल विवरण अपडेट करने में असमर्थ',
    'logout' => 'आप सफलतापूर्वक लॉगआउट कर रहे हैं',
    'push_token_added' => 'पुश टोकन हमारे रिकॉर्ड में जोड़ा गया',

    /* Edit Profile */
    'invalid' => ':entity अमान्य है',
    'old_password' => 'पुराना :entity अमान्य है',
    'current_new_password_not_same' =>  'पुराना पासवर्ड और नया पासवर्ड अलग होना चाहिए',
    'not_adult' => 'उम्र कम से कम 18 साल होनी चाहिए',
    'not_exists' => ':entity का अस्तित्व नहीं है',
    'edit_profile' => 'प्रोफाइल को सफलतापूर्वक अपडेट किया गया',
    'edit_profile_image' => 'प्रोफ़ाइल छवि सफलतापूर्वक अपडेट की गई',
    'password_not_match' => 'पुराना पासवर्ड हमारे रिकॉर्ड के साथ मेल नहीं खाता है',

    'maintenance'   =>  'आवेदन अभी रखरखाव के अधीन है। हम जल्दी ही लौटेंगे',
    'token-expired'       =>  'आपका सत्र समाप्त हो गया है। कृपया फिर भाग लें',

    /* Order Tracking */
    'to_many_request'   =>  'कई अनुरोधों के लिए',
    'already_cenceled'  =>  ':entity पहले ही रद्द किया गया',

    /* Reset Password */
    'link_sent'     =>  ':entity लिंक आपके दर्ज किये ईमेल पते पर सफलतापूर्वक भेजा गया।',
    'link_not_send' =>  'ईमेल भेजने के लिए सक्षम करें। कृपया पुनः देर से प्रयास करें',
    
    'reset_success' =>  'पासवर्ड सफलतापूर्वक रीसेट किया गया',

    'favourite'     =>  ':entity विस्तार से सफलतापूर्वक',
    
    'already_exists'    =>  ':entity विवरण पहले से मौजूद हैं',

    'dynamic-link'  =>  [
        'success'   =>  'डायनेमिक लिंक सफलतापूर्वक उत्पन्न हुआ',
        'fail'      =>  'डायनेमिक लिंक उत्पन्न करने में असमर्थ',
    ],

    'validate'          =>  ':entity सफलतापूर्वक सत्यापित की गई!',
    'validate_fail'     =>  'अमान्य: इकाई, कृपया बाद में पुनः प्रयास करें',

    'qr'    =>  [
        'generated' =>  ':entity सफलतापूर्वक उत्पन्न हुई',
    ],

    'payment'   =>  [
        'success'   =>  [
            'url-generated' =>  'सफलतापूर्वक भुगतान किया गया url'
        ],
        'fail'  =>  [
            'url-generated' =>  'भुगतान URL उत्पन्न करने में असमर्थ',
        ],
    ],

    'razorpay'  =>  [
        'order' =>  [
            'success'   =>  'आपकी ऑर्डर आईडी सफलतापूर्वक जेनरेट हो गई है।',
            'fail'      =>  'आदेश आईडी जनरेट करने में असमर्थ.',
        ],
        'verify_signature'  => [
            'success'   =>  'आपका भुगतान सफल रहा है',
            'fail'      =>  'आपका भुगतान विफल रहा है',
        ],
    ],
    'ios_payment'   =>  [
        'success'   =>  'आपका भुगतान सफल रहा है',
        'fail'      =>  'आपका भुगतान विफल रहा है',
    ],

    'verified'    =>  "आपके :entity को सफलतापूर्वक सत्यापित कर लिया गया है|",
    'already_verified'    =>  "आपका :entity पहले से ही सत्यापित था। ",
    'thanks'    =>  'के लिए धन्यवाद: इकाई: प्रकार',

    'report'    =>  [
        'success'   =>  'प्रोफ़ाइल रिपोर्ट सफलतापूर्वक किया गया',
        'fail'      =>  'प्रोफ़ाइल रिपोर्ट असफल हुआ',
    ],

    'block' =>  [
        'no_action'     =>  'जोड़ने में असमर्थ :entity आपकी प्रोफ़ाइल अवरुद्ध है',
        'not_able'      =>  'प्रोफ़ाइल को अवरोधित करने के लिए असंतुलित करना',
        'success'       =>  'प्रोफ़ाइल को सफलतापूर्वक अवरोधित किया गया',
        'fail'          =>  'प्रोफ़ाइल को ब्लॉक नहीं किया जा सका',
    ],
    'unblock' =>  [
        'success'   =>  'प्रोफ़ाइल को सफलतापूर्वक अनब्लॉक किया गया',
        'fail'      =>  'प्रोफ़ाइल अनब्लॉक की गई असफल',
    ],
    'verification'    =>  [
        'success'   =>  ':entity सफलतापूर्वक सत्यापित किया गया',
    ],
    'verification_upload'    =>  [
        'success'   =>  'सत्यापन विवरण सफलतापूर्वक अपडेट किया गया',
        'fail'      =>  'सत्यापन विवरण अपलोड करने में विफल',
    ],
    'subscription'    =>  [
        'already_purchased'   =>  'सदस्यता पहले ही खरीदी जा चुकी है।',
    ],
    'chat_room'  =>  [
        'delete'        =>  'चैट सत्र हटाया गया',
        'not_found'     =>  'चैट सत्र नहीं मिला',
    ],

    'notify_message'    =>  [
        'image_moderation'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your photo is removed from Hi Hello profile as it does not adhere to community guidelines",
        ],
        'image_moderation_chat'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "The Image you have sent is removed from Hi Hello as it does not adhere to community guidelines",
        ],
        'add_like'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Someone liked you in Hi Hello",
        ],
        'new_match'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Match is wating for you in Hi Hello",
        ],
        'profile_verified'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your profile verification is complete",
        ],
        'profile_not_verified'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Verifiy your profile to enjoy Hi Hello services",
        ],
        'subscription_expire'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your Hi Hello Membership is expiring soon. Renew now.",
        ],
        'swipe_alert'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your daily swipe limit is over. Upgrade to continue.",
        ],
        'verify_fail_email'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your email verification is not completed. Retry again.",
        ],
        'verify_fail_photo'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your photo verification has failed. Retry again.",
        ],
        'verify_fail_video'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your video verification has failed. Retry again.",
        ],
        'subscription_success'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "You have successfully subscribed to Hi Hello membership",
        ],
        'subscription_fail'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your subscription renewal has failed. Please retry.",
        ],
    ],
    'sms'   =>  [
        'message'   =>  [
            'welcome'   =>  "Welcome to the loving Hi Hello community. Time to search for your Dil Ka Connection at Hi Hello with loads of fun and verified profiles.",
            'subscription_purchase'     =>  "Namaste :entity thank you for upgrading your account with Hi Hello. Go ahead and explore exclusive In-App features customised for you.",
            'subscription_renew'    =>  "Namaste :entity thank you for renewing your subscription with Hi Hello account. Continue to enjoy Hi Hello services designed for you.",
            'birthday'    =>  "Hi :entity We wish you Happy Bday from the entire Hi Hello community. Have a fun filled day and awesome year ahead.",
        ],
    ],
];
