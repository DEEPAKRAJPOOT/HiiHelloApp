<?php

namespace App\Http\Traits;
use App\Models\Notification;
use App\Models\NotificationStatus;
use App\Notifications\PushNotification;

trait FirebaseTrait {

	public function getDynamicLink($link, $meta = [])
	{
	    $data = [
	        'dynamicLinkInfo'   =>  [
	            'domainUriPrefix'   =>  config('utility.google.deeplink.uri'),
	            'link'              =>  $link,
	            'androidInfo'       =>  [
	                'androidPackageName'    =>  config('utility.google.deeplink.android.package'),
	                'androidFallbackLink'   =>  $link,
	            ],
	            'iosInfo'                   =>  [
	                'iosBundleId'           =>  config('utility.google.deeplink.ios.bundle'),
	                'iosAppStoreId'         =>  config('utility.google.deeplink.ios.store'),
	                'iosCustomScheme'       => config('utility.google.deeplink.ios.scheme'),
	                'iosFallbackLink'       =>  $link,
	            ],
	            'navigationInfo'    =>  [
	                'enableForcedRedirect' => config('utility.google.deeplink.forced-redirect')
	            ],
	        ],
	        'suffix'            =>  [
	            'option'    =>  config('utility.google.deeplink.url-type')
	        ],
	    ];
	    if( !empty($meta) ) {
	        $data['dynamicLinkInfo']['socialMetaTagInfo'] = $meta;
	    }
	    $url = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=".config('utility.google.deeplink.key');
	    $response = fireCURL($url, "POST", json_encode($data));
	    return $response;
	}

	public function sendPushNotification($data)
	{
		// $key = "AAAAc28YziA:APA91bE6W1_9Et3jl6P0PVinIoKAS6w9yr8rC2ESMK3lwRNWI2ba6BlKYqoWV16zGXEOYZJ87xpxa0wmBKgCQF6afcYoL2fbkAyFhE0hEgB3zRSXQYaxfOwWc59WTPzK8HmHdio_Wj-N";
		$key = config('utility.google.fcm');
		
		# CREATED UNDER : bythehourapp@gmail.com
		# Sub Account : payal.s@yudiz.in

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: key='.$key]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	// Send push notifications to all users
    public function sendPushNotificationToAll($notification, $users)
    {        
        $dbNotification = Notification::create($notification);
        $status = [];
        $tokens = [];
        foreach ($users as $user) {
            $status[] = [
                'user_id'           =>  $user->id,
                'notification_id'   =>  $dbNotification->id,
                'is_read'           =>  'n',
                'created_at'        =>  \Carbon\Carbon::now(),
                'updated_at'        =>  \Carbon\Carbon::now(),
            ];
            if( !empty($user->deviceToken) && !empty($user->deviceToken->token) ) {
                $tokens[$user->deviceToken->type][] = $user->deviceToken->token;
            }
        }
        NotificationStatus::insert($status);
        $data = [
            'key'           =>  $dbNotification->key ?? "",
            'value'         =>  $dbNotification->value ?? "",
            'type'  		=>  $dbNotification->type,
            'user_id'       =>  $dbNotification->user_id,
            'image_url'     =>  $dbNotification->image ? generateURL($dbNotification->image) : "",
        ];
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = ['Content-Type:application/json', 'Authorization:key='.config('utility.google.fcm') ];
        if( !empty($tokens['ios']) ) {            
            $iosNotification = [
                'priority'          => 'high',
                'registration_ids'  => $tokens['ios'],
                'content_available' =>  false,
                'mutable_content'   =>  true,
                'notification'      =>  [
                    'title' =>  $dbNotification->title,
                    'body'  =>  str_limit($dbNotification->message, 50),
                    // 'badge' =>  0,
                    'sound' =>  'default'
                ],
            ];

            $iosNotification['data'] = $data;
            $sendIosNotification = json_encode($iosNotification);
            fireCURL($url, "POST", $sendIosNotification, $header);
        }

        if( !empty($tokens['android']) ) {
            $mData = array_merge($data, [
                        'title' =>  $dbNotification->title,
                        'body'  =>  str_limit($dbNotification->message, 50)]
                    );
            $androidNotification = [
                'priority'          =>  'high',
                'registration_ids'  =>  $tokens['android'],
                'data'              =>  $mData,
                'notification'      =>  [
                    'title'     =>  $dbNotification->title,
                    'body'      =>  str_limit($dbNotification->message, 50),
            		'type'  	=>  $dbNotification->type,
                    // 'badge'     =>  0,
                    'image'     =>  $dbNotification->image ? generateURL($dbNotification->image) : "",
                ],
            ];
            $data = json_encode($androidNotification);
            fireCURL($url, "POST", $data, $header);
        }
    }
}