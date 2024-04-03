<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Auth};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Http\Requests\Api\Game\{SetChallengeRequest,ActionOnChallengeRequest,GamePlayStatusRequest};
use App\Http\Resources\v1\{GameChallengeResource};
use App\Http\Traits\FirebaseTrait;
use App\Models\{User, UserSetting, Location, Language, GameChallenge, DeviceToken};
use DB;


class GameChallengeController extends Controller
{
    private $version = "v.1.0";
    use FirebaseTrait;
    public function getVersion(){ return $this->version; }
    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }
    public function doGameChallenge(Request $request){
        DB::enableQueryLog();
        $setChallengeRequest = new SetChallengeRequest();
        if ($this->apiValidator($request->all(), $setChallengeRequest->rules())) {
            try {
            $challenger = $this->getAuthUser();
            $user = User::select('id','game_playing_status')->whereCustomId($request->user_id)->where('id', '!=', $challenger->id)->where('id', '!=', config('utility.system.system_user_id'))->whereIsActive('y')->firstOrFail();
            $challenger_id = $challenger->id;
            $user_id = $user->id;
            if($user->game_playing_status == false){
                // $challenge = GameChallenge::where(['challenger_id'=>$challenger_id,'user_id'=>$user_id,'status'=>'0'])->first();
                // if($challenge){
                //     $data['status'] = true;
                //     $data['message'] = trans('api.challenge_pending');
                //     return ([
                //         'data'  => $data,
                //         'meta' => [
                //             'url'       =>  url()->current(),
                //             'api'       =>  $this->getVersion(),
                //             'language'  =>  app()->getLocale(),
                //             'is_ban'    =>  false,
                //             'message'   =>  trans('api.challenge_pending'),
                //         ]
                //     ]);
                // }else{
                    $challengeData['user_id'] = $user_id;
                    $challengeData['challenger_id'] = $challenger_id;
                    $challengeAdded = GameChallenge::create($challengeData);
                    if($challengeAdded){
                        $this->sendFirebaseNotification($user_id,'game_challenge');
                    }
                    $res['status'] = 'true';
                    $res['message'] = trans('api.challenge_add');
                    // dd($data);
                    return ([
                        'data'  => $res,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.challenge_add'),
                        ]
                    ]);
                //}
             }else{
                $data['status'] = false;
                $data['message'] = trans('api.already_playing_game');
                return ([
                    'data'  => $data,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.already_playing_game'),
                    ]
                ]);
             }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Game Challenge")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_game_challenge');
            } 
        }
        return $this->returnResponse();
    }

    public function acceptRejectChallenge(Request $request){

        $setChallengeRequest = new ActionOnChallengeRequest();
        if ($this->apiValidator($request->all(), $setChallengeRequest->rules())) {
            try {
                $user = $this->getAuthUser();
                $challenger = User::select('id')->whereCustomId($request->user_id)->where('id', '!=', $user->id)->where('id', '!=', config('utility.system.system_user_id'))->whereIsActive('y')->firstOrFail();
               $challenger_id = $challenger->id;
               $user_id = $user->id;
               $status =  $request->status;
               $challenge = GameChallenge::where(['challenger_id'=>$challenger_id,'user_id'=>$user_id,'status'=>'0'])->first();
               if($challenge){

                    $challenge->status = $status;
                    $challenge->save();

                    if((string)$status === '1'){
                       $notifyData =  $this->sendFirebaseNotification($challenger_id, 'accept_challenge');
                        $data['isAccepted'] = true;
                        $data['message'] = trans('api.challenge_accept');
                        return ([
                            'data'  => $data,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.challenge_accept'),
                            ]
                        ]);
                    }else if((string)$status === '2'){
                        $notifyData =  $this->sendFirebaseNotification($challenger_id, 'accept_challenge');
                        $data['isAccepted'] = false;
                        $data['message'] = trans('api.challenge_reject');
                        return ([
                            'data'  => $data,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.challenge_reject'),
                            ]
                        ]);
                    }
               }else{
                   $data['isAccepted'] = false;
                   $data['message'] =  trans('api.went_wrong');
                    return ([
                        'data'  => $data,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.went_wrong'),
                        ]
                    ]);
               }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Action Game Challenge")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'action_game_challenge');
            }  
        }      
        
    }

    public function getChallengeList(Request $request){
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
        try {
            $user = $this->getAuthUser();
            $user_id = $user->id;
            $limit = $request->limit;
            $offset = $request->offset;
            $challengesSql = GameChallenge::with('challengerUser','challengeReceiverUser','challengerUser.userTranslation','challengeReceiverUser.userTranslation')->where(['user_id'=>$user_id])->orWhere(['challenger_id'=>$user_id])->groupBy('challenger_id','user_id');
             $totalCount = $challengesSql->get()->count();
             $challenges = $challengesSql->limit($limit ?? config('utility.pagination.limit'))
            ->offset($offset ?? config('utility.pagination.offset'))->get();
            if($challenges->count()){
                return (GameChallengeResource::collection($challenges))->additional([
                    'meta' => [
                        'limit'     =>  10,
                        'offset'    =>  0,
                        'total'     =>  $totalCount,
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'is_ban'    =>  false,
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.list', ['entity' => __('Users')]),
                    ]
                ]);
            }else{
                $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Users')]);
                $this->response['meta']['is_ban']  = false;
                $this->status = Response::HTTP_NOT_FOUND;
            }
        } catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                    $this->response['meta']['is_ban']  = false;
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban']  = false;
                    break;
            };
        } catch (\Exception $e) {
            throw $e;
            $this->storeErrorLog($e, 'get_home_feed');
        }    
    }
    return $this->returnResponse();
   } 

   public function playGame(Request $request){
        $gamePlayRequest = new GamePlayStatusRequest();
        if ($this->apiValidator($request->all(), $gamePlayRequest->rules())) {
            $user = $this->getAuthUser();
            $challenger_id = $user->id;
            $user = User::whereCustomId($request->user_id)->first();  
            $user_id = $user->id;
            $status =  $request->status;
            $challenge = GameChallenge::where(['challenger_id'=>$challenger_id,'user_id'=>$user_id,'challenger_status'=>'0','status'=>1])->orWhere(['challenger_id'=>$user_id,'user_id'=>$challenger_id,'challenger_status'=>'0','status'=>1])->orderBy('id','desc')->first();
               if($challenge){

                    $challenge->challenger_status = $status;
                    $challenge->save();
                    if((string)$status === '1'){
                         $notifysenderData =  $this->sendFirebaseNotification($challenge->challenger_id, 'game_play');
                         $notifyReceiverData =  $this->sendFirebaseNotification($challenge->user_id, 'game_play');
                         $data['isAccepted'] = true;
                         $data['message'] = trans('api.challenge_accept');
                         return ([
                             'data'  => $data,
                             'meta' => [
                                 'url'       =>  url()->current(),
                                 'api'       =>  $this->getVersion(),
                                 'language'  =>  app()->getLocale(),
                                 'is_ban'    =>  false,
                                 'message'   =>  trans('api.challenge_accept'),
                             ]
                         ]);
                     }else if((string)$status === '2'){
                         $notifyData =  $this->sendFirebaseNotification($request->user_id, 'game_play');
                         $data['isAccepted'] = false;
                         $data['message'] = trans('api.challenge_reject');
                         return ([
                             'data'  => $data,
                             'meta' => [
                                 'url'       =>  url()->current(),
                                 'api'       =>  $this->getVersion(),
                                 'language'  =>  app()->getLocale(),
                                 'is_ban'    =>  false,
                                 'message'   =>  trans('api.challenge_reject'),
                             ]
                         ]);
                     }

               }

        }
   }

   public function updateGamePlayStaus(Request $request){
       $gamePlayRequest = new GamePlayStatusRequest();
       if ($this->apiValidator($request->all(), $gamePlayRequest->rules())) {
          try{
          $user = $this->getAuthUser();
          $user_id = $user->id;
          $user  = User::find($user_id);
          $user->game_playing_status =  $request->status;
          $user->save();
          if(isset($request->user_id) && !empty($request->user_id)){
            $notifyData =  $this->sendFirebaseNotification($request->user_id, 'play_game');
          }
          if($user){
                $data['isStatusUpdated'] = true;
                $data['message'] = trans('api.game_play_status');
                return ([
                    'data'  => $data,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.game_play_status'),
                    ]
                ]);
          }
        } catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                    $this->response['meta']['is_ban']  = false;
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban']  = false;
                    break;
            };
        } catch (\Exception $e) {
            throw $e;
            $this->storeErrorLog($e, 'get_home_feed');
        } 

       }
       return $this->returnResponse();
   }

   public function sendFirebaseNotification($user_id, $type){
    $payload = $this->generatePayload($user_id, $type);
    DB::enableQueryLog();
    if($type === 'play_game'){
        $user = User::whereCustomId($user_id)->first();
        $user_id = $user->id;
    }
    $deviceToken = DeviceToken::where(['user_id'=>$user_id])->orderBy('id','desc')->first();
    if($deviceToken != null){

        $send_notification = [
            'priority'  =>  'high',
            'to'        =>  $deviceToken->token,
            'sound'     =>  'default',
        ];

        if( $deviceToken->type == 'android' ) {
            $send_notification['data'] = $payload;
        } else {
            $send_notification['notification'] = $payload;
            $send_notification['data'] = $payload;
        }

        $data = json_encode($send_notification);
        $sendNotify = $this->sendPushNotification($data);
        $notifyData['payload'] = $data;
        $notifyData['notify'] =$sendNotify;
        return $notifyData;
        
    }
}

public function generatePayload($user_id, $type){
        $user = $this->getAuthUser();
        $auth_id = $user->id;
       $challengeData=[];
        if($type === 'game_challenge'){
            $challenges = GameChallenge::with('challengerUser','challengerUser.userTranslation','challengeReceiverUser','challengeReceiverUser.userTranslation')->where(['user_id'=>$user_id,'status'=>'0','challenger_id'=>$auth_id])->first();
            if($challenges){
                $challengeData = [
                    'title' => "Game challenge",
                    'body'=>$challenges->challengerUser->userTranslation->full_name." has challenged you to play a Game in Hihello Games",
                    'id'        =>  $challenges->challengerUser->custom_id ?? "",
                    'full_name' =>  $challenges->challengerUser->userTranslation ? $challenges->challengerUser->userTranslation->full_name : "",
                    'gender'            =>  $challenges->challengerUser->gender ?? "",
                    'age'               =>  $challenges->challengerUser->getAge(),
                    'Status'            => $challenges->getStatus(),
                    'onlineStatus'      => $challenges->challengerUser->onlineStatus(),
                    'isReadToPlay'      => $this->isReadToPlay($challenges->challengerUser->onlineStatus(),$challenges->getStatus()),
                    'profile_photo'     =>  generateURL($challenges->challengerUser->profile_photo) ?? "",
                    'type'              => 'game_challenge'
                ];
            }

        }else if($type === 'accept_challenge'){
            $challenges = GameChallenge::with('challengerUser','challengerUser.userTranslation','challengeReceiverUser','challengeReceiverUser.userTranslation')->where(['challenger_id'=>$user_id,'user_id'=>$auth_id])->orderBy('id','desc')->first();
            
            $challengeData=[];
            if($challenges){
                if((int)$challenges->status){
                    $body = $challenges->challengeReceiverUser->userTranslation->full_name." has Accepted your challenge to play a Game in Hihello Games.";
                }else{
                    $body = $challenges->challengeReceiverUser->userTranslation->full_name." not available to play a Game with you.";
                }
                

                $challengeData = [
                    'title' => "Game challenge",
                    'body'=>$body,
                    'id'        =>  $challenges->challengeReceiverUser->custom_id ?? "",
                    'full_name' =>  $challenges->challengeReceiverUser->userTranslation ? $challenges->challengeReceiverUser->userTranslation->full_name : "",
                    'gender'            =>  $challenges->challengeReceiverUser->gender ?? "",
                    'age'               =>  $challenges->challengeReceiverUser->getAge(),
                    'Status'            =>  $challenges->getStatus(),
                    'onlineStatus'      =>  $challenges->challengeReceiverUser->onlineStatus(),
                    'isReadToPlay'      =>  $this->isReadToPlay($challenges->challengeReceiverUser->onlineStatus(),$challenges->getStatus()),
                    'profile_photo'     =>  generateURL($challenges->challengeReceiverUser->profile_photo) ?? "",
                    'type'              => 'game_challenge'
                ];
            }

            
        }else if($type === 'play_game'){
            $user = User::whereCustomId($user_id)->first();
            $user_id = $user->id;
            $challenges = GameChallenge::with('challengerUser','challengerUser.userTranslation','challengeReceiverUser','challengeReceiverUser.userTranslation')->where(['challenger_id'=>$user_id,'user_id'=>$auth_id,'status'=>1])->orWhere(['user_id'=>$user_id,'challenger_id'=>$auth_id,'status'=>1])->orderBy('id','desc')->first();
            
            $challengeData=[];
            if($challenges){
                if($challenges->challenger_id != $user_id){
                        $body = $challenges->challengerUser->userTranslation->full_name." going to play a Game with you in Hihello Games.";

                        $challengeData = [
                        'title' => "Game challenge",
                        'body'=>$body,
                        'sender_id' =>  $challenges->challengerUser->custom_id ?? "",
                        'receiver_id'=> $challenges->challengeReceiverUser->custom_id ?? "",
                        'id'        =>  $challenges->challengerUser->custom_id ?? "",
                        'full_name' =>  $challenges->challengerUser->userTranslation ? $challenges->challengerUser->userTranslation->full_name : "",
                        'gender'            =>  $challenges->challengerUser->gender ?? "",
                        'age'               =>  $challenges->challengerUser->getAge(),
                        'Status'            =>  $challenges->getStatus(),
                        'onlineStatus'      =>  $challenges->challengerUser->onlineStatus(),
                        'isReadToPlay'      =>  $this->isReadToPlay($challenges->challengerUser->onlineStatus(),$challenges->getStatus()),
                        'profile_photo'     =>  generateURL($challenges->challengerUser->profile_photo) ?? "",
                        'type'              => 'game_challenge'
                    ];

                }else{
                       $body = $challenges->challengeReceiverUser->userTranslation->full_name." going to play a Game with you in Hihello Games.";
                    

                    $challengeData = [
                        'title' => "Game challenge",
                        'body'=>$body,
                        'sender_id' =>  $challenges->challengerUser->custom_id ?? "",
                        'receiver_id'=> $challenges->challengeReceiverUser->custom_id ?? "",
                        'id'        =>  $challenges->challengeReceiverUser->custom_id ?? "",
                        'full_name' =>  $challenges->challengeReceiverUser->userTranslation ? $challenges->challengeReceiverUser->userTranslation->full_name : "",
                        'gender'            =>  $challenges->challengeReceiverUser->gender ?? "",
                        'age'               =>  $challenges->challengeReceiverUser->getAge(),
                        'Status'            =>  $challenges->getStatus(),
                        'onlineStatus'      =>  $challenges->challengeReceiverUser->onlineStatus(),
                        'isReadToPlay'      =>  $this->isReadToPlay($challenges->challengeReceiverUser->onlineStatus(),$challenges->getStatus()),
                        'profile_photo'     =>  generateURL($challenges->challengeReceiverUser->profile_photo) ?? "",
                        'type'              => 'game_challenge'
                    ];

                }
                
            }
        }else{
            $challenges = GameChallenge::with('challengerUser','challengerUser.userTranslation','challengeReceiverUser','challengeReceiverUser.userTranslation')->where(['challenger_id'=>$user_id,'user_id'=>$auth_id,'status'=>1])->orWhere(['user_id'=>$user_id,'challenger_id'=>$auth_id,'status'=>1])->orderBy('id','desc')->first();
            
            $challengeData=[];
            if($challenges){
                
                if($challenges->challenger_id != $user_id){
                    if((int)$challenges->challenger_status){
                        $body = $challenges->challengerUser->userTranslation->full_name." is waiting for you to Join in Hihello Games.";
                    }else{
                        $body = $challenges->challengerUser->userTranslation->full_name." not available to play a Game with you.";
                    }
                    $challengeData = [
                        'title' => "Game Play Request",
                        'body'=>$body,
                        'sender_id' =>  $challenges->challengerUser->custom_id ?? "",
                        'receiver_id'=> $challenges->challengeReceiverUser->custom_id ?? "",
                        'id'        =>  $challenges->challengerUser->custom_id ?? "",
                        'full_name' =>  $challenges->challengerUser->userTranslation ? $challenges->challengerUser->userTranslation->full_name : "",
                        'gender'            =>  $challenges->challengerUser->gender ?? "",
                        'age'               =>  $challenges->challengerUser->getAge(),
                        'Status'            =>  $challenges->getStatus(),
                        'senderStatus'      =>  $challenges->senderStatus(),
                        'onlineStatus'      =>  $challenges->challengerUser->onlineStatus(),
                        'isReadToPlay'      =>  $this->isReadToPlay($challenges->challengerUser->onlineStatus(),$challenges->getStatus()),
                        'profile_photo'     =>  generateURL($challenges->challengerUser->profile_photo) ?? "",
                        'type'              => 'game_play'
                    ];
                
                }else{
                    if((int)$challenges->challenger_status){
                        $body = $challenges->challengeReceiverUser->userTranslation->full_name." is waiting for you to Join in Hihello Games.";
                    }else{
                        $body = $challenges->challengeReceiverUser->userTranslation->full_name." not available to play a Game with you.";
                    }
                    $challengeData = [
                        'title' => "Game Play Request",
                        'body'=>$body,
                        'sender_id' =>  $challenges->challengerUser->custom_id ?? "",
                        'receiver_id'=> $challenges->challengeReceiverUser->custom_id ?? "",
                        'id'        =>  $challenges->challengeReceiverUser->custom_id ?? "",
                        'full_name' =>  $challenges->challengeReceiverUser->userTranslation ? $challenges->challengeReceiverUser->userTranslation->full_name : "",
                        'gender'            =>  $challenges->challengeReceiverUser->gender ?? "",
                        'age'               =>  $challenges->challengeReceiverUser->getAge(),
                        'Status'            =>  $challenges->getStatus(),
                        'senderStatus'      =>  $challenges->senderStatus(),
                        'onlineStatus'      =>  $challenges->challengeReceiverUser->onlineStatus(),
                        'isReadToPlay'      =>  $this->isReadToPlay($challenges->challengeReceiverUser->onlineStatus(),$challenges->getStatus()),
                        'profile_photo'     =>  generateURL($challenges->challengeReceiverUser->profile_photo) ?? "",
                        'type'              => 'game_play'
                    ];
                }  
            }
        }
        
        return $challengeData;
    }

    public function isReadToPlay($onlineStatus,$status){
        if($onlineStatus === 'online' && $status === 'Accepted'){
            return true;
        }else{
            return false;
        }
    }
}
