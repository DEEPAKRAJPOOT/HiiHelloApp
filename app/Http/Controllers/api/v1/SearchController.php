<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Requests\Api\Search\{SearchMatchChatRequest};
use App\Http\Resources\v1\{SearchMatchChatResource};
use App\Models\{ChatRoom};

class SearchController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * Search Match Profiles & Chat Rooms
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /* Currently Not Working
    public function searchMatchAndChat(Request $request)
    {
        $searchMatchChatRequest = new SearchMatchChatRequest();        
        if( $this->apiValidator($request->all(), $searchMatchChatRequest->rules()) ) {
            try{
                $search = $request->search;
                $results = ['matches' => [], 'rooms' => []];
                $auth_id = $request->user() ? $request->user()->id : NULL;

                // Match Profiles
                $results['matches'] = DB::table('likes')
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

                // Chat Rooms
                $results['rooms'] = ChatRoom::with(['creator:id,custom_id,profile_photo',
                                        'participator:id,custom_id,profile_photo',
                                        'creator.userTranslation','participator.userTranslation',
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
                                    $query->whereHas('creator.userTranslation', function ($q1) use ($search){
                                        $q1->where('full_name', 'like', '%'.$search.'%');
                                    })->orWhereHas('participator.userTranslation', function ($q2) use ($search){
                                        $q2->where('full_name', 'like', '%'.$search.'%');
                                    });
                                })->get();

                if($results['matches']->isEmpty() && $results['rooms']->isEmpty() ){
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __("Search Result")]); 
                    $this->status = Response::HTTP_NOT_FOUND;   
                }else{
                    return (new SearchMatchChatResource($results))
                        ->additional([
                            'meta' => [
                                'message'       =>  trans('api.list',['entity' => __("Search Result")]),
                            ] ]);
                }

            } catch(ModelNotFoundException $exception) {    
                $this->response['meta']['message'] = trans('api.went_wrong');
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'seatch_match_chat');
            }
        }
        return $this->returnResponse();
    }
    */
}
