<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\Twillio\ { CreateApiKeyRequest, OutgoingAppSidRequest, CreateAccessTokenRequest, GetCallLogRequest, StoreCallLogRequest };
use App\Http\Resources\v1\ { TwillioApiKey, TwillioAccessToken, CallLogResource };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Twilio\Rest\ { Client };
use Twilio\Jwt\ { AccessToken };
use Twilio\Jwt\Grants\ { ChatGrant, VideoGrant, VoiceGrant };
use Twilio\TwiML\ { VoiceResponse };
use App\Models\ { User, UserCommunication, ChatRoom, CallLog };

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

        $dial = $response->dial('', array('callerId' => 'client:' . $data["outgoing_caller_id"]));

        $client = $dial->client($request->To);

        // Sending custom parameters, We will use in client side 
        $client->parameter([
            "name" => "outgoing_caller_id",
            "value" => $data["outgoing_caller_id"],
        ]);

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
                $room = ChatRoom::with('callLog')->whereCustomId($request->room)->firstOrFail();
                
                if($room->callLog){ 
                    $this->status = Response::HTTP_OK;
                    return (new CallLogResource($room->callLog))
                        ->additional([
                            'meta' => [
                                'message'   =>  trans('api.success', ['entity' => __("Call log") ]),
                            ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Call log')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
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
                $room = ChatRoom::whereCustomId($request->room)->firstOrFail();

                $call_log = CallLog::updateOrCreate([
                    'room_id'           =>  $room->id,
                    'date'              =>  now()->format('Y-m-d'),
                    'start_time'        =>  $request->start_time ?? NULL,
                ],[
                    'custom_id'         =>  getUniqueString('call_logs'),
                    'end_time'          =>  $request->end_time ?? NULL,
                    'remaining_time'    =>  $request->remaining_time ?? NULL,
                ]);

                $this->status = Response::HTTP_OK;
                return (new CallLogResource($call_log))
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
