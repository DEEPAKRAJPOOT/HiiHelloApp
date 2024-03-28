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
                $challenge = GameChallenge::where(['challenger_id'=>$challenger_id,'user_id'=>$user_id,'status'=>'0'])->first();
                if($challenge){
                    $data['status'] = true;
                    $data['message'] = trans('api.challenge_pending');
                    return ([
                        'data'  => $data,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.challenge_pending'),
                        ]
                    ]);
                }else{
                    $challengeData['user_id'] = $user_id;
                    $challengeData['challenger_id'] = $challenger_id;
                    $challengeAdded = GameChallenge::create($challengeData);
                    if($challengeAdded){
                        $payload = $this->generatePayload($user_id);
                        DB::enableQueryLog();
                        $deviceToken = DeviceToken::where(['user_id'=>$user_id])->first();
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
                            
                        }
                    }
                    $data['status'] = true;
                    $data['message'] = trans('api.challenge_add');
                    return ([
                        'data'  => $data,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.challenge_add'),
                        ]
                    ]);
                }
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

    public function generatePayload($user_id){

        $challenges = GameChallenge::with('challengerUser','challengerUser.userTranslation')->where(['user_id'=>$user_id,'status'=>'0'])->first();
        $challengeData=[];
        if($challenges){
            $challengeData = [
                'id'        =>  $challenges->challengerUser->custom_id ?? "",
                'full_name' =>  $challenges->challengerUser->userTranslation ? $challenges->challengerUser->userTranslation->full_name : "",
                'gender'            =>  $challenges->challengerUser->gender ?? "",
                'profile_photo'     =>  generateURL($challenges->challengerUser->profile_photo) ?? "",
                'type'              => 'game_challenge'
            ];
        }
        return $challengeData;
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
                    if($status == '1'){
                        $data['isAccepted'] = true;
                        $data['message'] = trans('api.challenge_accept');
                        return ([
                            'data'  => NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.challenge_accept'),
                            ]
                        ]);
                    }else if($status == '2'){
                        $data['isAccepted'] = false;
                        $data['message'] = trans('api.challenge_reject');
                        return ([
                            'data'  => NULL,
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
                        'data'  => NULL,
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
            $challengesSql = GameChallenge::with('challengerUser','challengerUser.userTranslation')->where(['user_id'=>$user_id,'status'=>'0'])->groupBy('challenger_id');
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

   public function updateGamePlayStaus(Request $request){
       $gamePlayRequest = new GamePlayStatusRequest();
       if ($this->apiValidator($request->all(), $gamePlayRequest->rules())) {
          try{
          $user = $this->getAuthUser();
          $user_id = $user->id;
          $user  = User::find($user_id);
          $user->game_playing_status =  $request->status;
          $user->save();

          if($user){
                return ([
                    'data'  => NULL,
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
}
