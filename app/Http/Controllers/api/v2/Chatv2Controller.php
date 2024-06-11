<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\General\PaginationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\{ChatRoom, ChatMessage, User, CallLog};
use App\Http\Resources\v2\ChatRoomResource;
use App\Http\Requests\Api\Chat\GetRoomRequest;

class Chatv2Controller extends Controller
{
    private $version = "v.2.0";
    public function getVersion(){ return $this->version; }

    // Get Chat Rooms Details

    public function getChatRooms(Request $request){
        
        $getRoomRequest = new GetRoomRequest();
        if($this->apiValidator($request->all(),$getRoomRequest->rules())){
            try {
                $limit = !empty($request->limit) ? $request->limit : config('utility.pagination.limit');
                $offset = !empty($request->offset) ? $request->offset : config('utility.pagination.offset');
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $search = $request->search;
                $system_chat_room_id = config('utility.chat.system_chat_room');
                $has_system_messages = ChatMessage::where('room_id',$system_chat_room_id)->where('receiver_id',$auth_id)->exists();
                $rooms = ChatRoom::whereHas('chatMessages')
                ->withCount(['chatMessages'=>function($query){
                    $query->where('status','!=','read');
                }])
                ->withCount('blockBy')
                ->where(function($query)use($auth_id,$has_system_messages,$system_chat_room_id){
                    $query->where(function($q)use($auth_id){
                        $q->whereCreatorId($auth_id)->whereNull('creator_deleted_at');
                    });
                    $query->orWhere(function($q)use($auth_id){
                        $q->whereParticipateId($auth_id)->whereNull('participate_deleted_at');
                    });
                    if($has_system_messages){
                        $query->orWhere(function($q)use($system_chat_room_id){
                            $q->where('id',$system_chat_room_id);
                        });
                    }
                });
                if(!empty($search)){
                    $rooms->where(function($query)use($search){
                        $query->whereHas('creator.userTranslations',function($q1)use($search){
                            $q1->where('full_name','like','%'.$search.'%');
                        })->orWhereHas('participator.userTranslations',function($q2)use($search){
                            $q2->where('full_name','like','%'.$search.'%');
                        });
                    });
                }
                $count = $rooms->count();
                $rooms = $rooms->orderBy('updated_at','desc')->limit($limit)->offset($offset)->get();
                if($rooms->isNotEmpty()){
                    $rooms = $rooms->sortBy(function($room)use($system_chat_room_id){
                        return ($room->id == $system_chat_room_id) ? 0 : 1;
                    });
                    return (ChatRoomResource::Collection($rooms))->additional([
                        'meta' => [
                            'limit'    => $limit,
                            'offset'   => $offset,
                            'total'    => $count,
                            'url'      => url()->current(),
                            'api'      => $this->getVersion(),
                            'language' => app()->getLocale(),
                            'is_ban'   => false,
                            'message'  => trans('api.list',['entity'=>__('Chat rooms')]),
                        ]
                    ]);
                } else {
                    $this->status = Response::HTTP_OK;
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Chat rooms')]);
                    $this->response['meta']['is_ban'] = false;
                }
            } catch (ModelNotFoundException $exception) {
                $this->status = Response::HTTP_OK;
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            }catch(\Exception $e){
                $this->status = Response::HTTP_OK;
                $this->storeErrorLog($e, 'get_chat_rooms');
            }
        }
        return $this->returnResponse();
    }

    public function getChatRoomsOld(Request $request)
    {
        $getRoomRequest = new GetRoomRequest();
        if ($this->apiValidator($request->all(), $getRoomRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $search = $request->search;

                $rooms = ChatRoom::with([
                    'creator:id,custom_id,profile_photo,language_id',
                    'participator:id,custom_id,profile_photo,language_id',
                    'creator.language:id,lang_code', 'participator.language:id,lang_code',
                    'creator.userTranslation', 'participator.userTranslation',
                    'latestMessage.sender:id,custom_id'
                ])
                    ->whereHas('chatMessages')
                    ->selectRaw("chat_rooms.*, (SELECT MAX(created_at) from chat_messages WHERE deleted_at is null and chat_messages.room_id=chat_rooms.id) as latest_message_on")
                    ->orderBy("latest_message_on", "DESC")
                    ->withCount(['chatMessages' => function ($query) {
                        $query->where('status', '!=', 'read');
                    }])
                    ->withCount('blockBy')
                    ->where(function($query)use($auth_id){
                        $query->where(function($q)use($auth_id){
                            $q->whereCreatorId($auth_id)
                            ->whereNull('creator_deleted_at');
                        });
                        $query->orWhere(function($q)use($auth_id){
                            $q->whereParticipateId($auth_id)
                            ->whereNull('participate_deleted_at');
                        });
                        $query->orWhere(function($q)use($auth_id){
                            $q->whereId(config('utility.chat.system_chat_room'))->whereHas('chatMessages',function($sq)use($auth_id){
                                $sq->where('receiver_id',$auth_id);
                            });
                        });
                    });

                if (!empty($search)) {
                    $rooms = $rooms->where(function ($query) use ($search) {
                        $query->whereHas('creator.userTranslations', function ($q1) use ($search) {
                            $q1->where('full_name', 'like', '%' . $search . '%');
                        })->orWhereHas('participator.userTranslations', function ($q2) use ($search) {
                            $q2->where('full_name', 'like', '%' . $search . '%');
                        });
                    });
                }

                $count = $rooms->count();
                $rooms = $rooms->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($rooms->isNotEmpty()) {
                    return (ChatRoomResource::Collection($rooms))->additional([
                        'meta'  =>  [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.list', ['entity' =>  __('Chat rooms')]),
                        ]
                    ]);
                } else {
                    $this->status = Response::HTTP_OK;  // Return 200 because android can handle popup screen
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Chat rooms')]);
                    $this->response['meta']['is_ban'] = false;
                }
            } catch (ModelNotFoundException $exception) {
                $this->status = Response::HTTP_OK;
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->status = Response::HTTP_OK;
                $this->storeErrorLog($e, 'get_chat_rooms');
            }
        }
        return $this->returnResponse();
    }
}