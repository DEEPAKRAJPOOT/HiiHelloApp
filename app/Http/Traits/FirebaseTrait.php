<?php

namespace App\Http\Traits;

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
}