<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\Twillio\ { CreateApiKeyRequest, OutgoingAppSidRequest, CreateAccessTokenRequest, GetCallLogRequest, StoreCallLogRequest, GetReceiverDetailRequest };
use App\Http\Resources\v1\ { TwillioApiKey, TwillioAccessToken, CallLogResource, CallReceiverResource };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Twilio\Rest\ { Client };
use Twilio\Jwt\ { AccessToken };
use Twilio\Jwt\Grants\ { ChatGrant, VideoGrant, VoiceGrant };
use Twilio\TwiML\ { VoiceResponse };
use App\Models\ { User, UserCommunication, ChatRoom, CallLog, UserTranslation };
use App\Jobs\ { NotificationJob };

class TwillioController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /** 
    * Create voice token for audio calls
    * Same api for android & ios usage
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function createAccessToken(Request $request)
    {
        $rules = CreateAccessTokenRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $twilioAccountSid   =   config('utility.twillio.account_sid');
                // $pushCredentialSid  =   config('utility.twillio.push_sid');
                $outgoingAppSid     =   $request->sid ? $request->sid : config('utility.twillio.outgoing_app_sid');
                $twilioApiKey       =   $request->api_key;
                $twilioApiSecret    =   $request->api_secret;
                $identity           =   $request->identity;
                $time_line          =   $request->time_line ? $request->time_line : config('utility.twillio.time_line'); 

                // Create access token, which we will serialize and send to the client
                $token = new AccessToken( $twilioAccountSid, $twilioApiKey, $twilioApiSecret, $time_line, $identity );

                // Create Voice grant
                $voiceGrant = new VoiceGrant();
                $voiceGrant->setOutgoingApplicationSid($outgoingAppSid);

                // Optional: add to allow incoming calls
                $voiceGrant->setIncomingAllow(true);
                $voiceGrant->setpushCredentialSid($request->push_id);

                // Add grant to token
                $token->addGrant($voiceGrant);

                $this->status = Response::HTTP_OK;
                return (new TwillioAccessToken($token))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.list', ['entity' => __("Twilio Access Token") ]),
                        ] ]);
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;  
                $this->storeErrorLog($e,'twilio_create_aceess_token');
            }
        }
        return $this->returnResponse();
    }

    /**
    * Hanlde voice response using ip_addrss/voice url 
    * Need to set /voice url in twillio account configuration
    * @param  \Illuminate\Http\Request  $request
    * @return \Twilio\TwiML\VoiceResponse
    */
    public function voice(Request $request)
    {
        $data = $request->all();
        $response = new VoiceResponse();

        // make sure you passing caller id from client side. 
        // Twilio.Device.connect(params); <----- in param object
        // $dial = $response->dial('', ['callerId' => $data["outgoing_caller_id"]]);

        $dial = $response->dial('', array(
                    'callerId'          =>  'client:' . $data["outgoing_caller_id"],
                    'answerOnBridge'    =>  true,  // Callback Event For Incoming Call (For Mobile Side)
                ));

        $client = $dial->client($request->To,
                [
                    'statusCallbackEvent'   =>  'initiated ringing answered completed',
                    'statusCallback'        =>  env('APP_URL').'/events?room_id='.$data["room_id"].'&receiver_id='.$data["receiver_id"],  // user's Custom id to send notification
                    'statusCallbackMethod'  =>  'GET'
                ]);

        // Sending custom parameters, We will use in client side 
        $client->parameter([
            "name" => "outgoing_caller_id",
            "value" => $data["outgoing_caller_id"],
        ]);

        // pass custom room_id params to handle voice call logs
        $client->parameter([
            "name" => "room_id",
            "value" => $data["room_id"],
        ]);

        // pass custom display_name params to handle display caller name to receiver
        $client->parameter([
            "name" => "display_name",
            "value" => $data["display_name"],
        ]);

        return $response;
    }

    public function events(Request $request)
    {
        $response = new VoiceResponse();

        if($request->CallStatus == 'no-answer' || $request->CallStatus == 'failed'){
            $chat_room = ChatRoom::with('creator','participator')->whereCustomId($request->room_id)->first();
            if($chat_room){

                if($chat_room->creator && $chat_room->participator){
                    $caller     =   $chat_room->creator;
                    $receiver   =   $chat_room->participator;
                    if($chat_room->creator->custom_id == $request->receiver_id){
                        $caller     =   $chat_room->participator;
                        $receiver   =   $chat_room->creator;
                    }

                    $lang_code = $receiver ? $receiver->language ? $receiver->language->lang_code : "en" : "en";
                    app()->setLocale($lang_code); // Change Language As Per Receiver Langauge For Notification

                    $userTranslation = UserTranslation::select('full_name')->whereUserId($caller->id)->whereLocale($lang_code)->first();
                    if($userTranslation){ 
                        $caller_name = $userTranslation->full_name ?? "";
                    }else{
                        $caller_name = $caller ? $caller->userTransEn ? $caller->userTransEn->full_name : "" : "";
                    }
                    $caller_profile = $caller ? $caller->profile_photo : "";

                    $notification = [
                        'custom_id'     =>  getUniqueString('notifications'),
                        'key'           =>  'user_id',
                        'room_id'       =>  $request->room_id ? $request->room_id : "",
                        'value'         =>  $receiver->custom_id,
                        'user_id'       =>  $receiver->id,
                        'name'          =>  $caller_name,
                        'profile'       =>  generateURL($caller_profile),
                        'image'         =>  generateURL($caller_profile),
                        'title'         =>  trans('api.notify_message.voice_call_miss_call.title'),
                        'message'       =>  trans('api.notify_message.voice_call_miss_call.message',['entity' => $caller_name]),
                        'type'          =>  config('utility.notification.type.voice_call_miss_call'),
                    ];

                    // Notify
                    $notificationJob = new NotificationJob($notification, $receiver);
                    dispatch($notificationJob);
                }
            }
        }
        return $response;
    }

    /**
     * Get Remaining Call time details
     * @param  \Illuminate\Http\Request  $request
     * @return \Twilio\TwiML\VoiceResponse
     */
    public function getCallLog(Request $request)
    {
        $rules = GetCallLogRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $room = ChatRoom::with(['callLog' => function($query){
                            $query->where('date',now()->format('Y-m-d'));
                        }])->whereCustomId($request->room)->firstOrFail();
                
                $this->status = Response::HTTP_OK;
                return (new CallLogResource($room))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.success', ['entity' => __("Call log") ]),
                        ] ]);
                
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_call_time');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Store call log details
     * @param  \Illuminate\Http\Request  $request
     * @return \Twilio\TwiML\VoiceResponse
     */
    public function storeCallLog(Request $request)
    {
        $rules = StoreCallLogRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $room = ChatRoom::with(['creator','participator'])->whereCustomId($request->room)->firstOrFail();

                $call_log = CallLog::updateOrCreate([
                    'room_id'           =>  $room->id,
                    'date'              =>  now()->format('Y-m-d'),
                    'start_time'        =>  $request->start_time ?? NULL,
                ],[
                    'custom_id'         =>  getUniqueString('call_logs'),
                    'end_time'          =>  $request->end_time ?? NULL,
                    'remaining_time'    =>  $request->remaining_time ?? NULL,
                ]);

                if($call_log->remaining_time == "00:00" || $call_log->remaining_time == "00:00:00"){
                    $room->nofityCallTimeOut();
                }

                $this->status = Response::HTTP_OK;
                return (new CallLogResource($room))
                        ->additional([
                            'meta' => [
                                'message'   =>  trans('api.add', ['entity' => __("Call log") ]),
                            ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
                        break;
                    case 'App\Models\CallLog':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Call log")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'store_call_log');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Get Call Receiver Details Before call initiate
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReceiverDetail(Request $request)
    {
        $rules = GetReceiverDetailRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $auth_user = $request->user();
                $call_receiver = User::select('id','custom_id','gender','language_id','is_subscribed','subscription_end_date')
                            ->with('language')
                            ->with('userTransEn')
                            ->whereCustomId($request->user_id)->whereIsActive('y')->firstOrFail();
                $locale = $call_receiver->language ? $call_receiver->language->lang_code : 'en';

                $user_translation = UserTranslation::select('full_name')
                                ->where(['user_id' => $auth_user->id, 'locale' => $locale])->first();

                $call_receiver['twilio_rcv_show_name'] = "";
                if($user_translation){ $call_receiver['twilio_rcv_show_name'] = $user_translation->full_name ?? ""; }

                $this->status = Response::HTTP_OK;
                return (new CallReceiverResource($call_receiver))
                        ->additional([
                            'meta' => [
                                'message'   =>  trans('api.list', ['entity' => __("User") ]),
                            ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_receiver_detail');
            }
        }
        return $this->returnResponse();
    }


    /******************************************************** EXTRA ************************************************************/

    /*
    // Create Api Key & Secret Using App Name
    public function createApiKey(Request $request)
    {
        $rules = CreateApiKeyRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $sid        =   config('utility.twillio.account_sid');
                $token      =   config('utility.twillio.account_token');
                $twilio     =   new Client($sid, $token);
                $new_key    =   $twilio->newKeys->create(["friendlyName" => $request->name]);
                    
                $this->status = Response::HTTP_OK;
                return (new TwillioApiKey($new_key))
                    ->additional([
                        'meta' => [
                            'message'   =>  trans('api.list', ['entity' => __("Twilio Api Key") ]),
                        ] ]);
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;  
                $this->storeErrorLog($e,'twilio_create_api_key');
            }
        }
        return $this->returnResponse();
    }
    */

    /*
    // Create OutGoing Application SID Using App Name
    public function getOutgoingAppSid(Request $request)
    {
        $rules = OutgoingAppSidRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $sid        =   config('utility.twillio.account_sid');
                $token      =   config('utility.twillio.account_token');
                $twilio     =   new Client($sid, $token);
                    
                $application = $twilio->applications
                                    ->create([
                                       "voiceMethod" => "GET",
                                       "voiceUrl" => "http://demo.twilio.com/docs/voice.xml",
                                       "friendlyName" => $request->name
                                   ]);
                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  [
                        'sid'   =>  $application->sid,
                    ],
                    'meta' => [
                        'message'   =>  trans('api.list', ['entity' => __("Twilio Outgoing App Sid") ]),
                    ] ]);
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;  
                $this->storeErrorLog($e,'twilio_outgoing_app_sid');
            }
        }
        return $this->returnResponse();
    }
    */

    /*
    public function connectWithTwilio(Request $request)
    {    
        $user_ids = User::whereIsActive('y')->pluck('custom_id')->toArray();
        $rules = [
            'user_id' =>'required|in:'.implode(',',$user_ids),
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $this->response['meta']['message'] = trans('api.went_wrong');                
                $status = UserCommunication::connectWithTwilio($request->user_id);
                if($status == true){
                    $this->response['meta']['message'] = trans('api.success',['entity' => 'Twillio connection']);
                }
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;  
                $this->storeErrorLog($e,'twilio_connect');
            }
        }
        return $this->returnResponse();
    }
    */

    /*
    public function makeCall(Request $request)
    {
        $sid        =   config('utility.twillio.account_sid');
        $token      =   config('utility.twillio.account_token');

        // $account_sid = 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
        // $auth_token = 'your_auth_token';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

        // A Twilio number you own with Voice capabilities
        $twilio_number = "+91123456789";

        // Where to make a voice call (your cell phone?)
        $to_number = "+911234567988";

        $client = new Client($sid, $token);
        $client->account->calls->create(  
            $to_number,
            $twilio_number,
            array(
                "url" => "http://demo.twilio.com/docs/voice.xml"
            )
        );

        dd($client);
    }
    */

    /*
    public function ReceiveCall(Request $request)
    {
        // Start our TwiML response
        $response = new VoiceResponse;

        // Read a message aloud to the caller
        $response->say(
            "Thank you for calling! Have a great day.", 
            array("voice" => "alice")
        );

        dd($response);
    }
    */
}
