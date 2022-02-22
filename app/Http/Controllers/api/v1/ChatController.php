<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Models\ { ChatRoom, ChatMessage, User };
use App\Http\Resources\v1\ { ChatRoomResource, ChatMessageResource };

class ChatController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function createRoom(Request $request)
    {
        $user = $request->user();
        $participant_ids = User::whereIsActive('y')->pluck('custom_id')->toArray();
        $rules = [
            'participant_id'      =>  'required|in:'.implode(',',$participant_ids),
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $participant = User::whereIsActive('y')->whereCustomId($request->participant_id)->firstOrFail();

                $room = ChatRoom::firstOrCreate([
                    'creator_id'        =>  $user->id,
                    'participate_id'    =>  $participant->id,
                ],[ 
                    'custom_id'         =>  getUniqueString('chat_rooms'),
                ]);

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

    public function getChatRooms(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $rooms = ChatRoom::has('chatMessages')->whereCreatorId($user->id);
                $count = $rooms->count();
                $rooms = $rooms->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->latest()
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

    public function getChatMessages(Request $request)
    {
        $user = $request->user();
        $chat_rooms_ids = ChatRoom::whereCreatorId($user->id)->pluck('custom_id')->toArray();
        $rules = [
            'room'      =>  'required|min:2|max:150|in:'.implode(',',$chat_rooms_ids),
            'limit'     =>  'nullable|numeric|min:5',
            'offset'    =>  'nullable|numeric|min:0',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try {
                $room = ChatRoom::whereCreatorId($user->id)->whereCustomId($request->room)->firstOrFail();
                $messages = ChatMessage::whereRoomId($room->id)->latest();
                $count = $messages->count();
                $messages = $messages->limit($request->limit ?? config('utility.pagination.limit'))
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
