<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Auth};
use App\Http\Requests\Api\Game\{SetChallengeRequest,ActionOnChallengeRequest};
use App\Http\Resources\v1\{DiscoveryResource};
use App\Models\{User, UserSetting, Location, Language, GameChallenge};
use DB;


class GameChallengeController extends Controller
{
    private $version = "v.1.0";
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
            $user = User::select('id')->whereCustomId($request->user_id)->where('id', '!=', $challenger->id)->where('id', '!=', config('utility.system.system_user_id'))->whereIsActive('y')->firstOrFail();
            
            $challenger_id = $challenger->id;
            $user_id = $user->id;
            $challenge = GameChallenge::where(['challenger_id'=>$challenger_id,'user_id'=>$user_id,'status'=>'0'])->first();
            if($challenge){
                return ([
                    'data'  => NULL,
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
                return ([
                    'data'  => NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.challenge_add'),
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
            // try {
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
            // } catch (ModelNotFoundException $exception) {
            //     switch ($exception->getModel()) {
            //         case 'App\Models\Location':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Action Game Challenge")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         default:
            //             $this->response['meta']['message'] = trans('api.went_wrong');
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //     };
            // } catch (\Exception $e) {
            //     $this->storeErrorLog($e, 'action_game_challenge');
            // }  
        }      
        
    }
}
