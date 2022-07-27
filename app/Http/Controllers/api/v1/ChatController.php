<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\General\ { PaginationRequest };
use Illuminate\Support\Facades\ { Auth };
use App\Models\ { ChatRoom, ChatMessage, User, CallLog };
use App\Http\Resources\v1\ { ChatRoomResource, ChatMessageResource };
use App\Http\Requests\Api\Chat\ { CreateRoomRequest, ChatMessagesRequest, DeleteRoomRequest, GetRoomRequest };

class ChatController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Create New Chat Room 
    public function createChatRoom(Request $request)
    {
        $rules = CreateRoomRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user(); $auth_id = $user ? $user->id : NULL;
                $participant = User::whereCustomId($request->participant_id)->whereIsActive('y')->firstOrFail();
                $participant_id = $participant ? $participant->id : NULL;

                $chat_room = ChatRoom::with(['creator:id,custom_id,profile_photo,language_id',
                                'participator:id,custom_id,profile_photo,language_id',
                                'creator.userTranslation','creator.language:id,lang_code',
                                'participator.userTranslation','participator.language:id,lang_code',
                                'latestMessage.sender:id,custom_id'])
                        ->where(function ($query) use ($auth_id,$participant_id) {
                            $query->whereCreatorId($auth_id)->where('participate_id',$participant_id);
                        })->orWhere(function ($query) use ($auth_id,$participant_id) {
                            $query->whereCreatorId($participant_id)->where('participate_id',$auth_id);
                        })->first();

                if(empty($chat_room)){
                    $chat_room = ChatRoom::firstOrCreate([
                        'creator_id'        =>  $auth_id,
                        'participate_id'    =>  $participant_id,
                    ],[ 
                        'custom_id'         =>  getUniqueString('chat_rooms'),
                    ]);
                }

                $this->status = Response::HTTP_OK;     
                return (new ChatRoomResource($chat_room))->additional([
                    'meta'  =>  [
                        'message'   =>  trans('api.save', ['entity' =>  __('Chat room')]),
                    ]
                ]);
            } catch(ModelNotFoundException $exception) {                
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
                $this->storeErrorLog($e,'create_chat_room');
            }
        }
        return $this->returnResponse();
    }

    // Get Chat Rooms Details
    public function getChatRooms(Request $request)
    {
        $rules = GetRoomRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $search = $request->search;

                $rooms = ChatRoom::with(['creator:id,custom_id,profile_photo,language_id',
                                'participator:id,custom_id,profile_photo,language_id',
                                'creator.language:id,lang_code','participator.language:id,lang_code',
                                'creator.userTranslation','participator.userTranslation',
                                'latestMessage.sender:id,custom_id'])
                        ->whereHas('chatMessages')
                        ->selectRaw("chat_rooms.*, (SELECT MAX(created_at) from chat_messages WHERE deleted_at is null and chat_messages.room_id=chat_rooms.id) as latest_message_on")
                        ->orderBy("latest_message_on", "DESC")
                        ->withCount(['chatMessages' => function ($query) {
                            $query->where('status','!=' ,'read');
                        }])
                        ->withCount('blockBy')
                        ->where(function ($query) use ($auth_id) {
                            $query->whereCreatorId($auth_id)->orWhere('participate_id',$auth_id);
                        });

                if(!empty($search)){
                    $rooms = $rooms->where(function ($query) use ($search) {
                                $query->whereHas('creator.userTranslations', function ($q1) use ($search){
                                    $q1->where('full_name', 'like', '%'.$search.'%');
                                })->orWhereHas('participator.userTranslations', function ($q2) use ($search){
                                    $q2->where('full_name', 'like', '%'.$search.'%');
                                });
                            });
                }

                $count = $rooms->count();
                $rooms = $rooms->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();

                if($rooms->isNotEmpty()){
                    return (ChatRoomResource::Collection($rooms))->additional([
                        'meta'  =>  [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' =>  __('Chat rooms')]),
                        ]
                    ]);
                }else{
                    $this->status = Response::HTTP_OK;  // Return 200 because android can handle popup screen
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Chat rooms')]); 
                }
            } catch(ModelNotFoundException $exception) {     
                $this->status = Response::HTTP_OK;     
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->status = Response::HTTP_OK;     
                $this->storeErrorLog($e,'get_chat_rooms');
            }
        }
        return $this->returnResponse();
    }

    // Get Chat Messages Of The Room
    public function getChatMessages(Request $request)
    {
        $rules = ChatMessagesRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try {
                $messages   =   ChatMessage::select('id','custom_id','room_id','sender_id','message','status','created_at','updated_at')->with(['sender:id,custom_id'])
                                ->whereHas('room', function($q) use ($request){
                                    $q->whereCustomId($request->room)->whereIsActive('y');
                                })->latest();
                                
                $count      =   $messages->count();
                $messages   =   $messages->limit($request->limit ?? config('utility.pagination.limit'))
                                    ->offset($request->offset ?? config('utility.pagination.offset'))
                                    ->get();

                $callLog    =   CallLog::select('id','room_id','remaining_time')->where('date',now()->format('Y-m-d'))
                                    ->whereHas('room', function($q) use ($request){
                                        $q->whereCustomId($request->room)->whereIsActive('y');
                                    })->latest()->first();

                if($messages->isNotEmpty()){
                    return (ChatMessageResource::Collection($messages))->additional([
                        'meta'  =>  [
                            'remaining_time'    =>  $callLog ? $callLog->remaining_time : config('utility.twillio.allow_call_time'),
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Chat history')])
                        ],
                    ]);
                }else{
                    $this->response['meta']['remaining_time']  = $callLog ? $callLog->remaining_time : config('utility.twillio.allow_call_time'); 
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Chat history')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
           } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
                        break;
                    case 'App\Models\ChatMessage':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat history")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_chat_messages');
            }
        }
        return $this->returnResponse();
    }

    // Delete Chat Room
    public function deleteChatRoom(Request $request)
    {
        $rules = DeleteRoomRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $room = ChatRoom::with('chatMessages')->whereCustomId($request->room_id)->firstOrFail();
                if($room->chatMessages){ $room->chatMessages->each->delete(); }
                $room->delete();

                $this->status = Response::HTTP_OK;     
                return (['data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.chat_room.delete'),
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.chat_room.not_found');
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'delete_chat_room');
            }
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
