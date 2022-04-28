<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Support\Facades\ { Auth, DB };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Http\Requests\Api\Chat\ { DeleteRoomRequest };
use App\Http\Resources\v1\ { MatchResource };
use App\Models\ { User, Like, ChatRoom };

class MatchController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * Get new matched profile details.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNewMatches(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $auth_id = $request->user() ? $request->user()->id : NULL;

                $matches = DB::table('likes')
                    ->join("likes as like", function($q){
                        $q->on("likes.liker_id", "=", "like.user_id");
                        $q->on("like.liker_id", "=", "likes.user_id");
                    })
                    ->join('users', function($q){
                        $q->on('users.id',"=", "likes.user_id");
                    })
                    //to only get users details who likes current user
                    ->where("likes.liker_id", '=', $auth_id)
                    ->where("likes.user_id", '!=', $auth_id)
                    ->orderBy('likes.created_at', 'desc')
                    ->selectRaw("likes.custom_id as custom_id, users.custom_id as user_custom_id,
                                users.full_name as user_full_name, users.profile_photo as user_profile_photo,
                                likes.created_at as created_at");

                $count = $matches->count();
                $matches = $matches->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();

                if($matches->isNotEmpty()){
                    $this->status = Response::HTTP_OK;     
                    return (MatchResource::Collection($matches))->additional([
                        'meta'  =>  [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' =>  __('New Matches')]),
                        ]
                    ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('New Matches')]); 
                    $this->status = Response::HTTP_NOT_FOUND;   
                }
            } catch(ModelNotFoundException $exception) {    
                $this->response['meta']['message'] = trans('api.went_wrong');
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'new_matches');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Remove match/Unmatch profile detail.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeMatch(Request $request)
    {
        $rules = DeleteRoomRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            DB::beginTransaction();
            try{
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $match_user = User::select('id')->whereCustomId($request->user_id)->firstOrFail();

                // Delete Like Details
                Like::where(function($query) use ($auth_id, $match_user){
                    $query->whereUserId($auth_id)->whereLikerId($match_user->id);
                })->orWhere(function($query_or) use ($auth_id, $match_user){
                    $query_or->whereUserId($match_user->id)->whereLikerId($auth_id);
                })->delete();

                $room = ChatRoom::with('chatMessages')
                                ->where(function($query) use ($auth_id, $match_user){
                                    $query->where('creator_id',$auth_id)->where('participate_id',$match_user->id);
                                })->orWhere(function($query_or) use ($auth_id, $match_user){
                                    $query_or->where('creator_id',$match_user->id)->where('participate_id',$auth_id);
                                })->first();

                // Delete Chat Room & Chat Messages
                if($room){
                    if($room->chatMessages){ $room->chatMessages->each->delete(); }
                    $room->delete();
                }

                DB::commit();
                $this->status = Response::HTTP_OK;     
                return (['data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.delete', ['entity' =>  __('Unmatch')]),
                    ] ]);
            } catch(ModelNotFoundException $exception) {   
                DB::rollback();
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();   
                $this->storeErrorLog($e,'delete_match');
            }
        }
        return $this->returnResponse();
    }
}
