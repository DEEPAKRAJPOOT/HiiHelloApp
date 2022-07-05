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
    'error' =>  'Something went wrong. Try again later',
    'success' =>  ':entity details get sucessfully',
    'fail'   =>  ':entity unsucessfully',
    'add' => ':entity added successfully',
    'remove' => ':entity removed successfully',
    'update' => ':entity updated successfully',
    'delete' => ':entity deleted successfully',
    'list' => ':entity retrieved successfully',
    'not_found' => ':entity not found',
    'not_verified' => ':entity is not varified',
    'not_activated' => ':entity is not activated',
    'subscription_required' => 'Your subscription is not activated. Please subscribe to continue.',
    'went_wrong'    =>  'Oops! Something went wrong, please try again later',
    'not_empty'     => 'Please select valid :entity',
    'not_available' =>  ':entity is not available',
    'liked'     =>  ':entity liked successfully',
    'dis-liked'     =>  ':entity Dis-liked successfully',
    'follow'     =>  ':entity followed successfully',
    'unfollow'     =>  ':entity un-followed successfully',
    'already_added'     =>  ':entity already added to cart',
    'already_offered'     =>  ':entity is already offered to seller',
    'already_exists'     =>  ':entity already exists',
    'not_saved'     =>  ':entity is already saved',
    'save'     =>  ':entity saved successfully',
    'expired'     =>  ':entity is expired',
    'min_amount'    => ':entity must be greater than or equal to :entity2',
    'empty' => ':entity is empty',
    'not_cancel'      => ':entity cannot be cancelled',
    'max_referred' => 'This referrel code cannot be used',
    'generated'  =>  ':entity generated successfully',
    'apply_success' =>  ':entity applied succesfully',
    'swipe_over' =>  'Your daily swipe limit is over. Upgrade to continue.',

    /* Login Messages */
    'login_fail' => 'These credentials do not match our records',
    'in_active' => 'You are blocked. please contact administrative',
    'account_deleted' => 'Your account has been deleted by administrator',
    'not_registered' => 'You are not registered with us',
    'login' => 'You are succesfully login to your account',
    'registered' => 'You are succesfully registered with us',
    'profile_setuped' => 'You profile details are saved succesfully',
    'profile_setuped_fail' => 'Unable to update profile details',
    'logout' => 'You are succesfully logout',
    'push_token_added' => 'Push token added to our records',

    /* Edit Profile */
    'invalid' => 'The selected :entity is invalid',
    'old_password' => 'Old :entity is invalid',
    'current_new_password_not_same' =>  'Old password & new password must be different',
    'not_adult' => 'The age must be at least 18 years',
    'not_exists' => 'The :entity does not exist',
    'edit_profile' => 'Profile updated successfully',
    'edit_profile_image' => 'Profile image updated successfully',
    'password_not_match' => 'Old password doesn\'t match with our records',

    'maintenance'   =>  'Application is currently under maintenance. We will be back soon!',
    'token-expired'     =>  'Your session has expired. Please log in again.',

    /* Order Tracking */
    'to_many_request'   =>  'To Many Requests',
    'already_cenceled'  =>  ':entity Already Cancelled',

    /* Reset Password */
    'link_sent'     =>  ':entity link sent successfully',
    'link_not_send' =>  'Enable to send an e-mail. Please try again later',

    'reset_success' =>  'Password reset successfully',
    
    'favourite'     =>  ':entity :detail successfully',

    'already_exists'    =>  ':entity details are already exists',

    'dynamic-link'  =>  [
        'success'   =>  'Dynamic link generated successfully',
        'fail'      =>  'Unable to generated dynamic link',
    ],

    'validate'          =>  ':entity validated successfully!',
    'validate_fail'     =>  'Invalid :entity, please try again later',

    'qr'    =>  [
        'generated' =>  ':entity generated successfully!',
    ],

    'payment'   =>  [
        'success'   =>  [
            'url-generated' =>  'Payment url generated successfully.'
        ],
        'fail'  =>  [
            'url-generated' =>  'Unable to generate payment URL',
        ],
    ],

    'razorpay'  =>  [
        'order' =>  [
            'success'   =>  'Your order id is generated successfully.',
            'fail'      =>  'Unable to generate order id.',
        ],
        'verify_signature'  => [
            'success'   =>  'Your payment was successful.',
            'fail'      =>  'Your payment failed.',
        ],
    ],
    'ios_payment'   =>  [
        'success'   =>  'Your payment was successful.',
        'fail'      =>  'Your payment failed.',
    ],

    'verified'    =>  "Your :entity has been successfully verified.",
    'already_verified'    =>  "Your :entity was already verified.",
    'thanks'    =>  'Thanks for :entity :type.',

    'report'    =>  [
        'success'   =>  'Profile repoted succesfully',
        'fail'      =>  'Profile repoted unsuccesfully',
    ],

    'block' =>  [
        'no_action'     =>  'Unbale to add :entity your profile is blocked',
        'not_able'      =>  'Unbale to block profile',
        'success'       =>  'Profile blocked succesfully',
        'fail'          =>  'Profile blocked unsuccesfully',
    ],
    'unblock' =>  [
        'success'   =>  'Profile unblocked succesfully',
        'fail'      =>  'Profile unblocked unsuccesfully',
    ],
    'verification'    =>  [
        'success'   =>  ':entity verified succesfully',
    ],
    'verification_upload'    =>  [
        'success'   =>  'Verification details updated succesfully',
        'fail'      =>  'Failed to upload Verification details',
    ],
    'subscription'    =>  [
        'already_purchased'   =>  'Subscription already purchased.',
    ],
    'chat_room'  =>  [
        'delete'        =>  'Chat Session Deleted',
        'not_found'     =>  'Chat Session Not Found',
    ],

    'notify_message'    =>  [
        'image_moderation'    =>  [
            'title'     =>  "Hi Hello",
            'message'   =>  "Your photo is removed from Hi Hello profile as it does not adhere to community guidelines",
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
