<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Requests\Api\General\{PaginationRequest};
use Illuminate\Support\Facades\{Auth};
use App\Models\{ChatRoom, ChatMessage, User, CallLog};
use App\Http\Resources\v1\{ChatRoomResource, ChatMessageResource};
use App\Http\Requests\Api\Chat\{CreateRoomRequest, ChatMessagesRequest, ClearRoomRequest, DeleteRoomRequest, GetRoomRequest, DisappearModeRequest, VanishModeRequest};

class ChatController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Create New Chat Room 
    public function createChatRoom(Request $request)
    {
        $createRoomRequest = new CreateRoomRequest();
        if ($this->apiValidator($request->all(), $createRoomRequest->rules())) {
            try {
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $participant = User::whereCustomId($request->participant_id)
                    ->where('id', '!=', $auth_id)->whereIsActive('y')->firstOrFail();
                $participant_id = $participant ? $participant->id : NULL;

                $chat_room = ChatRoom::with([
                    'creator:id,custom_id,profile_photo,language_id',
                    'participator:id,custom_id,profile_photo,language_id',
                    'creator.userTranslation', 'creator.language:id,lang_code',
                    'participator.userTranslation', 'participator.language:id,lang_code',
                    'latestMessage.sender:id,custom_id'
                ])
                    ->where(function ($query) use ($auth_id, $participant_id) {
                        $query->whereCreatorId($auth_id)->where('participate_id', $participant_id);
                    })->orWhere(function ($query) use ($auth_id, $participant_id) {
                        $query->whereCreatorId($participant_id)->where('participate_id', $auth_id);
                    })->first();

                if (empty($chat_room)) {
                    $chat_room = ChatRoom::firstOrCreate([
                        'creator_id'        =>  $auth_id,
                        'participate_id'    =>  $participant_id,
                    ], [
                        'custom_id'         =>  getUniqueString('chat_rooms'),
                    ]);
                }

                $this->status = Response::HTTP_OK;
                return (new ChatRoomResource($chat_room))->additional([
                    'meta'  =>  [
                        'message'   =>  trans('api.save', ['entity' =>  __('Chat room')]),
                        'is_ban'    =>  false,
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
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
                $this->storeErrorLog($e, 'create_chat_room');
            }
        }
        return $this->returnResponse();
    }

    // Get Chat Rooms Details
    public function getChatRooms(Request $request)
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

    // Get Chat Messages O$f The Room
    public function getChatMessages(Request $request)
    {
        $chatMessagesRequest = new ChatMessagesRequest();
        if ($this->apiValidator($request->all(), $chatMessagesRequest->rules())) {
            try {
                $user_type = 'participant';
                $cleared_time = '';
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $room = ChatRoom::whereCustomId($request->room)->whereIsActive('y')->first();
                if(!empty($room)){
                    if($room->creator_id == $auth_id){
                        $user_type = 'creator';
                        $cleared_time  = $room->creator_cleared_at;
                    }

                    if($room->participate_id == $auth_id){
                        $cleared_time = $room->participate_cleared_at;
                    }
                }
                $messages = ChatMessage::select('id', 'custom_id', 'room_id', 'sender_id', 'message', 'status', 'created_at', 'updated_at', 'deleted_at','is_vanished')
                ->where(function($expired_query){
                    $expired_query->where('is_vanished','n');
                    $expired_query->orWhere('status','!=','read');
                })
                ->where(function($sender_deleted_query)use($auth_id){
                    $sender_deleted_query->whereNull('sender_deleted_at');
                    $sender_deleted_query->orWhere('sender_id','!=',$auth_id);
                });
                if(!empty($cleared_time)){
                    $messages->where('created_at','>',$cleared_time);
                }
                if($room->id == config('utility.chat.system_chat_room')){
                    $messages->where('receiver_id',$auth_id);
                }else{
                    $messages->withTrashed();
                }
                $messages = $messages->with(['sender:id,custom_id'])
                    ->whereHas('room', function ($q) use ($request) {
                        $q->whereCustomId($request->room)->whereIsActive('y');
                    })->latest();

                $count      =   $messages->count();
                $messages   =   $messages->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                $callLog    =   CallLog::select('id', 'room_id', 'remaining_time')->where('date', now()->format('Y-m-d'))
                    ->whereHas('room', function ($q) use ($request) {
                        $q->whereCustomId($request->room)->whereIsActive('y');
                    })->latest()->first();

                if ($messages->isNotEmpty()) {
                    return (ChatMessageResource::Collection($messages))->additional([
                        'meta'  =>  [
                            'remaining_time'    =>  $callLog ? $callLog->remaining_time : config('utility.twillio.allow_call_time'),
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'is_system_room' =>  ($room->id == config('utility.chat.system_chat_room')),
                            'vanish_mode' =>  (($room->vanish_mode ?? 'n') == 'y'),
                            'disappear_mode' =>  $room->disappear_mode ?? 'off',
                            'last_online' => (($user_type == 'creator') ? $room->participator : $room->creator)->lastOnlineDiff(),
                            'message'   =>  trans('api.list', ['entity' => __('Chat history')])
                        ],
                    ]);
                } else {
                    $this->response['meta']['remaining_time']  = $callLog ? $callLog->remaining_time : config('utility.twillio.allow_call_time');
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Chat history')]);
                    $this->response['meta']['is_ban'] = false;
                    $this->status = Response::HTTP_OK;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\ChatMessage':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat history")]);
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
                $this->storeErrorLog($e, 'get_chat_messages');
            }
        }
        return $this->returnResponse();
    }

    // Delete only the messages from the Chat Room
    public function clearChatRoom(Request $request)
    {
        $clearRoomRequest = new ClearRoomRequest();
        if ($this->apiValidator($request->all(), $clearRoomRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $room = ChatRoom::whereCustomId($request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id)
                            ->orWhere('id', config('utility.chat.system_chat_room'));
                    })->firstOrFail();

                if($room->id == config('utility.chat.system_chat_room')){
                    ChatMessage::where('room_id',$room->id)->where('receiver_id',$auth_id)->delete();
                }else{
                    if($room->creator_id == $auth_id){
                        $room->creator_cleared_at = now();
                    }

                    if($room->participate_id == $auth_id){
                        $room->participate_cleared_at = now();
                    }
                    if(!empty($request->clear_for_both) && $request->clear_for_both != 'false'){
                        $room->creator_cleared_at = now();
                        $room->participate_cleared_at = now();
                    }

                    $room->save();
                }

                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.chat_room.delete'),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.chat_room.not_found');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'delete_chat_room');
            }
        }
        return $this->returnResponse();
    }

    // Delete Chat Room
    public function deleteChatRoom(Request $request)
    {
        $deleteRoomRequest = new DeleteRoomRequest();
        if ($this->apiValidator($request->all(), $deleteRoomRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $room = ChatRoom::whereCustomId($request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id)
                            ->orWhere('id', config('utility.chat.system_chat_room'));
                    })->firstOrFail();

                if($room->id == config('utility.chat.system_chat_room')){
                    ChatMessage::where('room_id',$room->id)->where('receiver_id',$auth_id)->delete();
                }else{
                
                    if($room->creator_id == $auth_id){
                        $room->creator_deleted_at = now();
                    }

                    if($room->participate_id == $auth_id){
                        $room->participate_deleted_at = now();
                    }
                    
                    if(!empty($request->delete_for_both) && $request->delete_for_both != 'false'){
                        $room->creator_deleted_at = now();
                        $room->participate_deleted_at = now();
                    }

                    $room->save();
                }

                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.chat_room.delete'),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.chat_room.not_found');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'delete_chat_room');
            }
        }
        return $this->returnResponse();
    }

    public function setVanishMode(Request $request){
        $vanishModeRequest = new VanishModeRequest();
        if ($this->apiValidator($request->all(), $vanishModeRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $room = ChatRoom::whereCustomId($request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id);
                    })->firstOrFail();
                if(!empty($request->vanish_mode) && $request->vanish_mode != 'false'){
                    $room->vanish_mode = 'y';
                }else{
                    $room->vanish_mode = 'n';
                }
                $room->vanish_mode_by = $auth_id;
                $room->save();
                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  [
                        'room_id'     => $request->room_id,
                        'vanish_mode' => (($room->vanish_mode ?? 'n') == 'y'),
                        'disappear_mode' => $room->disappear_mode ?? 'off',
                    ],
                    'meta' => [
                        'url'         =>  url()->current(),
                        'api'         =>  $this->getVersion(),
                        'language'    =>  app()->getLocale(),
                        'is_ban'      =>  false,
                        'message'     =>  trans('api.chat_room.vanish_mode'),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.chat_room.not_found');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_vanish_mode');
            }
        }else{
            $this->response['meta']['message'] = trans('api.went_wrong');
            $this->response['meta']['is_ban'] = false;
        }
        return $this->returnResponse();
    }

    public function setDisappearMode(Request $request){
        $disappearModeRequest = new DisappearModeRequest();
        if ($this->apiValidator($request->all(), $disappearModeRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $room = ChatRoom::whereCustomId($request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id);
                    })->firstOrFail();
                $room->disappear_mode = $request->disappear_mode;
                $room->disappear_mode_by = $auth_id;
                $room->save();
                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  [
                        'room_id'     => $request->room_id,
                        'disappear_mode' => $room->disappear_mode,
                        'vanish_mode' => (($room->vanish_mode ?? 'n') == 'y'),
                    ],
                    'meta' => [
                        'url'         =>  url()->current(),
                        'api'         =>  $this->getVersion(),
                        'language'    =>  app()->getLocale(),
                        'is_ban'      =>  false,
                        'message'     =>  trans('api.chat_room.disappear_mode'),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.chat_room.not_found');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_disappear_mode');
            }
        }else{
            $this->response['meta']['message'] = trans('api.went_wrong');
            $this->response['meta']['is_ban'] = false;
        }
        return $this->returnResponse();
    }

    public function sendChatPush(Request $request, $chatmessage, $message = "")
    {
        $chatMessage = ChatMessage::where('custom_id', $chatmessage)->firstOrFail();
        if ($message != "") $message =  str_limit($message, 70);

        $chatMessage->notifyChatMessageToUser($message);
        $this->status = Response::HTTP_OK;
        $this->response['meta']['message'] = __("Notification sent successfully");
        return $this->returnResponse();
    }
}
