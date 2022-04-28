<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\General\ { PaginationRequest };
use Illuminate\Support\Facades\ { Auth };
use App\Models\ { ChatRoom, ChatMessage, User };
use App\Http\Resources\v1\ { ChatRoomResource, ChatMessageResource };
use App\Http\Requests\Api\Chat\ { CreateRoomRequest, ChatMessagesRequest, DeleteRoomRequest };

class ChatController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Create New Chat Room 
    public function createRoom(Request $request)
    {
        $rules = CreateRoomRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $participant = User::whereIsActive('y')->whereCustomId($request->participant_id)->firstOrFail();
                $room = ChatRoom::whereCreatorId($participant->id)->whereParticipateId($user->id)->first();
                if(!$room){ 
                    $room = ChatRoom::whereCreatorId($user->id)->whereParticipateId($participant->id)->first();
                    if(!$room){
                        $room = ChatRoom::firstOrCreate([
                            'creator_id'        =>  $user->id,
                            'participate_id'    =>  $participant->id,
                        ],[ 
                            'custom_id'         =>  getUniqueString('chat_rooms'),
                        ]);
                    }
                };

                $this->status = Response::HTTP_OK;     
                return (new ChatRoomResource($room))->additional([
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
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $rooms = ChatRoom::with(['creator:id,custom_id,full_name,profile_photo',
                                'participator:id,custom_id,full_name,profile_photo',
                                'latestMessage.sender:id,custom_id'])
                        ->whereHas('chatMessages')
                        ->selectRaw("chat_rooms.*, (SELECT MAX(created_at) from chat_messages WHERE chat_messages.room_id=chat_rooms.id) as latest_message_on")
                        ->orderBy("latest_message_on", "DESC")
                        ->withCount(['chatMessages' => function ($query) {
                            $query->where('status','!=' ,'read');
                        }])
                        ->whereIsActive('y')
                        ->whereCreatorId($user->id)
                        ->orWhere('participate_id',$user->id);
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
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Chat rooms')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
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

                if($messages->isNotEmpty()){
                    return (ChatMessageResource::Collection($messages))->additional([
                        'meta'  =>  [
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
                        'message'   =>  trans('api.delete', ['entity' =>  __('Chat room')]),
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
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
