<?php

namespace App\Http\Traits;
use Twilio\Rest\Client;
use App\Models\TwilioSubaccounts;
use Twilio\Exceptions\RestException;

trait TwilioTrait {
	public static $mock = false;

    /**
     * Create new subaccount
     */
    public static function createSubAccount($friendlyName)
    {        
        if(self::$mock){
            $account = ['sid' => md5(rand()), 'authToken' => md5(rand())]; 
            return (object)$account;
        }
        $sid        =   config('utility.twillio.account_sid');
        $token      =   config('utility.twillio.account_token');
        $twilio     =   new Client($sid, $token);  

        try{            
            $account = $twilio->api->v2010->accounts
                                      ->create(["friendlyName" => $friendlyName]);
        }catch(RestException $e){
            \Log::error($e->getMessage());
            $account = false;
        }
        
        return $account;
    } 

    /**
     * Create new subaccount api key & token
     */
    public static function createSubaccountApiKey($subaccount_sid, $subaccount_token)
    {
        $new_key = false;   
        $twilio = new Client($subaccount_sid, $subaccount_token);

        try{    
            $new_key = $twilio->newKeys->create();
        }catch(RestException $e){
            \Log::error($e->getMessage());
        }
        return $new_key;
    }

     /**
     * Create new Application SID
     */
    public static function createSubAccountApplication($friendlyName, $subaccount_sid, $subaccount_token)
    {        
        if(self::$mock){
            $application = ['sid' => md5(rand())]; 
            return (object)$application;
        }

        $twilio = new Client($subaccount_sid, $subaccount_token);        
        try{            
            $application = $twilio->applications
                             ->create([
                                "voiceMethod"   =>  "POST",
                                "voiceUrl"      =>  url("api/communication/makecall"),                                   
                                "friendlyName"  =>  $friendlyName
                            ]);
        }catch(RestException $e){
            \Log::error($e->getMessage());
            $application = false;
        }
        return $application;
    }

    /**
     * Create new subaccount
     * $params array friendlyName, type, certificate (apn only), privateKey (apn only), secret (fcm)
     */
    public static function createCredential($subaccount_sid, $subaccount_token, $type, $params)
    {        
        if(self::$mock){
            $credential = ['sid' => md5(rand())]; 
            return (object)$credential;
        }

        $twilio = new Client($subaccount_sid, $subaccount_token);
        try{            
            $credential = $twilio->chat->v2->credentials->create($type, $params);
        }catch(RestException $e){
            \Log::error($e->getMessage());
            $credential = false;
        }
        return $credential;
    }

    /**
     * Get available number 
     */
    public static function searchIncomingNumber($areaCode = '')
    {
        if(self::$mock){
            return '0000000000';
        }
        
        $availableNumber = false;

        $props = ['smsEnabled' => True, 'voiceEnabled' => True, 'mmsEnabled' => True];
        if(!empty($areaCode)){
            $props["areaCode"] = intval($areaCode);
        }
        
        try{
            $sid        =   config('utility.twillio.account_sid');
            $token      =   config('utility.twillio.account_token');
            $twilio     =   new Client($sid, $token);

            $res = $twilio->availablePhoneNumbers("IN")
                            ->local
                            ->read($props, 20);

            foreach ($res as $record) {
                $availableNumber = $record->phoneNumber;
            }            

        }catch(RestException $e){
            //dd($e->getMessage());
            \Log::error($e->getMessage());
            $availableNumber = false;
        }

        return $availableNumber;
    }

    public static function buyIncomingNumber($phoneNumber, $subaccount_sid  = null)
    {        
        if(self::$mock){
            return true;
        }
        $sid        =   config('utility.twillio.account_sid');
        $token      =   config('utility.twillio.account_token');

        if(!empty($subaccount_sid)){
            $twilio = new Client($sid, $token, $subaccount_sid);
        }else{
            $twilio = new Client($sid, $token);
        }

        try{    
            $incoming_phone_number = $twilio->incomingPhoneNumbers
                                        ->create([
                                            "phoneNumber"   =>  $phoneNumber,
                                            "voiceUrl"      =>  url("api/communication/voiceincoming"),
                                            "smsUrl"        =>  url("api/communication/smsincoming")
                                            //"voiceUrl" => "https://inspectproject.com/thecontactcard/api/communication/voiceincoming",
                                            //"smsUrl" => "https://inspectproject.com/thecontactcard/api/communication/smsincoming"
                                            
                                        ]);
        }catch(RestException $e){
            \Log::error($e->getMessage());
            $incoming_phone_number = false;
        }
        return $incoming_phone_number;
    }
}