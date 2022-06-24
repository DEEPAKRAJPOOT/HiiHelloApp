<?php

namespace App\Http\Traits;
use Twilio\Rest\Client;
use App\Http\Controllers\Controller;

trait TwillioSmsTrait {

    /**
     * Send Twillio Sms
     */
    public static function sendSMS($phone_number, $message)
    {        
        $status = false; 
        try{
            // Your Account SID and Auth Token from twilio.com/console
            $account_sid     =   config('utility.twillio.account_sid');
            $auth_token      =   config('utility.twillio.account_token');

            // A Twilio number you own with SMS capabilities
            $twilio_number = "+919867175525";
            // $twilio_number = "+918866577985";

            // dd($account_sid, $auth_token, $twilio_number);
            $client = new Client($account_sid, $auth_token);
            $response =  $client->messages
                // ->create(
                //     // Where to send a text message (your cell phone?)
                //     '+919978876871',
                //     array(
                //         // 'from' => $twilio_number,
                //         "messagingServiceSid" => "MG18438b6ec2564047e124d6e80c8c1903",
                //         'body' => 'I sent this message in under 10 minutes!'
                //     )
                // );
                ->create($phone_number, // to
                        ["body" => $message, "from" => $phone_number]
                  );
            $status = true;
        } catch (\Exception $e) {
            $controller = new Controller();
            $controller->storeErrorLog($e,'send_sms');
            $status = false;
        }
        return $status;
    } 
}