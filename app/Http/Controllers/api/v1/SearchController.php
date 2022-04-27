<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Support\Facades\ { Auth, DB };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\Search\ { SearchMatchChatRequest };
use App\Models\ { ChatRoom };

class SearchController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * Search Match Profiles & Chat Rooms
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchMatchAndChat(Request $request)
    {
        $rules = SearchMatchChatRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $search = $request->search;
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
                                likes.created_at as created_at")
                    ->where(function ($query) use ($search) {
                        $query->where('users.full_name', 'like', "%{$search}%");
                    })->get();

                $rooms = ChatRoom::with(['creator:id,custom_id,full_name,profile_photo',
                                        'participator:id,custom_id,full_name,profile_photo',
                                        'latestMessage.sender:id,custom_id'])
                                ->whereHas('chatMessages')
                                ->selectRaw("chat_rooms.*, (SELECT MAX(created_at) from chat_messages WHERE chat_messages.room_id=chat_rooms.id) as latest_message_on")
                                ->orderBy("latest_message_on", "DESC")
                                ->withCount(['chatMessages' => function ($query) {
                                    $query->where('status','!=' ,'read');
                                }])
                                ->where(function ($query) use ($auth_id) {
                                    $query->whereIsActive('y')
                                            ->whereCreatorId($auth_id)
                                            ->orWhere('participate_id',$auth_id);
                                })
                                ->where(function ($query) use ($search) {
                                    $query->whereHas('creator', function ($q1) use ($search){
                                        $q1->where('full_name', 'like', '%'.$search.'%');
                                    })->orWhereHas('participator', function ($q2) use ($search){
                                        $q2->where('full_name', 'like', '%'.$search.'%');
                                    });
                                })->get();

                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>   NULL,
                    'meta' => [
                        'api'       =>  $this->getVersion(),
                        'url'       =>  url()->current(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.list', ['entity' => __("Match & Chat") ]),
                    ] ]);

            } catch(ModelNotFoundException $exception) {    
                $this->response['meta']['message'] = trans('api.went_wrong');
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'seatch_match_chat');
            }
        }
        return $this->returnResponse();
    }
}
