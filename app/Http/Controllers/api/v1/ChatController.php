<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Models\ { ChatRoom, ChatMessage, User };
use App\Http\Resources\v1\ { ChatRoomResource, ChatMessageResource };
use App\Http\Requests\Api\Chat\ { CreateRoomRequest, ChatMessagesRequest };

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
                $rooms = ChatRoom::whereCreatorId($user->id)->orWhere('participate_id',$user->id)->latest();
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
                $user = $request->user();
                $room = ChatRoom::whereCustomId($request->room)
                                ->whereCreatorId($user->id)
                                ->orWhere('participate_id',$user->id)
                                ->whereIsActive('y')
                                ->firstOrFail();
                $messages   =   ChatMessage::whereRoomId($room->id)->latest();
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
                            'message'   =>  trans('api.list', ['entity' => 'Chat history'])
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
}
