<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Twilio\Rest\ { Client };

class SmsController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
    * Send sms using twillio service 
    * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function sendSMS(Request $request)
    {
        dd($request->all());
        // try{
            // Your Account SID and Auth Token from twilio.com/console
            $account_sid     =   config('utility.twillio.account_sid');
            $auth_token      =   config('utility.twillio.account_token');

            // A Twilio number you own with SMS capabilities
            $twilio_number = "+919867175525";
            // $twilio_number = "+918866577985";

            // dd($account_sid, $auth_token, $twilio_number);
            $client = new Client($account_sid, $auth_token);
            $response =  $client->messages
                ->create(
                    // Where to send a text message (your cell phone?)
                    '+919978876871',
                    array(
                        // 'from' => $twilio_number,
                        "messagingServiceSid" => "MG18438b6ec2564047e124d6e80c8c1903",
                        'body' => 'I sent this message in under 10 minutes!'
                    )
                );

                // ->create("+919867175525", // to
                //            ["body" => "Hi there", "from" => "+919867175525"]
                //   );
            dd($response);
        // } catch (\Exception $e) {
        //     $this->response['meta']['message'] = trans('api.went_wrong');
        //     $this->status = Response::HTTP_NOT_FOUND;  
        //     $this->storeErrorLog($e,'send_sms');
        // }
        // return $this->returnResponse();


        // dd($request->all());
        // $data = $request->all();
        // $response = new VoiceResponse();

        // // make sure you passing caller id from client side. 
        // // Twilio.Device.connect(params); <----- in param object
        // // $dial = $response->dial('', ['callerId' => $data["outgoing_caller_id"]]);

        // $dial = $response->dial('', array(
        //                 'callerId'          =>  'client:' . $data["outgoing_caller_id"],
        //                 'answerOnBridge'    =>  true,  // Callback Event For Incoming Call (For Mobile Side)
        //             ));

        // $client = $dial->client($request->To);

        // // Sending custom parameters, We will use in client side 
        // $client->parameter([
        //     "name" => "outgoing_caller_id",
        //     "value" => $data["outgoing_caller_id"],
        // ]);

        // // pass custom room_id params to handle voice call logs
        // $client->parameter([
        //     "name" => "room_id",
        //     "value" => $data["room_id"],
        // ]);

        // return $response;
    }
}
