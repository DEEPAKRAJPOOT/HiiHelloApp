<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\Twillio\ { CreateApiKeyRequest, CreateAccessTokenRequest };
use App\Http\Resources\v1\ { TwillioApiKey, TwillioAccessToken };
use Twilio\Rest\ { Client };
use Twilio\Jwt\ { AccessToken };
use Twilio\Jwt\Grants\ { ChatGrant, VideoGrant };

class TwillioController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

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

    public function createAccessToken(Request $request)
    {
        $rules = CreateAccessTokenRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $twilioAccountSid   =   config('utility.twillio.account_sid');
                $twilioApiKey       =   $request->api_key;
                $twilioApiSecret    =   $request->api_secret;
                $roomName           =   $request->room_name;
                $identity           =   $request->identity;
                $time_line          =   $request->time_line ? $request->time_line : config('utility.twillio.time_line'); 

                // Create access token, which we will serialize and send to the client
                $token = new AccessToken( $twilioAccountSid, $twilioApiKey, $twilioApiSecret, $time_line, $identity );

                // Create Video grant
                $videoGrant = new VideoGrant();
                $videoGrant->setRoom($roomName);

                // Add grant to token
                $token->addGrant($videoGrant);

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

    public function createServiceResource(Request $request)
    {
        dd("In Development");
        // try{
        //     $sid        =   config('utility.twillio.account_sid');
        //     $token      =   config('utility.twillio.account_token');
        //     $twilio     =   new Client($sid, $token);

        //     $friendly_name  =  'TestIdentity8';

        //     $service = $twilio->chat->v2->services->create($friendly_name);

        //     dump($service);
        //     dd($service->sid);
        // } catch (\Exception $e) {
        //     $this->response['meta']['message'] = trans('api.went_wrong');
        //     $this->status = Response::HTTP_NOT_FOUND;  
        //     $this->storeErrorLog($e,'twilio_create_service_resource');
        // }
        // return $this->returnResponse();
    }

    public function newMessageNotification(Request $request)
    {
        dd("In Development");
        // try{
        //     $sid        =   config('utility.twillio.account_sid');
        //     $token      =   config('utility.twillio.account_token');
        //     $twilio     =   new Client($sid, $token);

        //     $channel        =   'Test Room';
        //     $user           =   'TestIdentity8';
        //     $message        =   'This is Test Notification Of Twilio';
        //     $service_sid    =   'IS99e50ca615ed476a9c05cc7beaa8e534';  

        //     $service = $twilio->chat->v2->services($service_sid)
        //                     ->update(array(
        //                                  "notificationsAddedToChannelEnabled" => True,
        //                                  "notificationsAddedToChannelSound" => "default",
        //                                  "notificationsAddedToChannelTemplate" => "A New message in ".$channel." from ".$user.": ".$message.""
        //                              )
        //                     );

        //     dump($service);
        //     dd($service->friendlyName);
        // } catch (\Exception $e) {
        //     $this->response['meta']['message'] = trans('api.went_wrong');
        //     $this->status = Response::HTTP_NOT_FOUND;  
        //     $this->storeErrorLog($e,'twilio_new_message_notification');
        // }
        // return $this->returnResponse();
    }
}
