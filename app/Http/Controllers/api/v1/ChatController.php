<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use MongoDB\BSON\ObjectId;
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Requests\Api\General\{PaginationRequest};
use Illuminate\Support\Facades\{Auth};
use Illuminate\Support\Facades\Cache;
use App\Models\{ChatRoom, ChatMessage, User, CallLog,ChatMessageMongoose, ChatRoomMongoose, UsersMongoose};
use App\Http\Resources\v1\{ChatRoomResource, ChatMessageResource, ChatRoomMongoResource, ChatMessageMongoResource};
use App\Http\Requests\Api\Chat\{CreateRoomRequest, ChatMessagesRequest, ClearRoomRequest, DeleteRoomRequest, GetRoomRequest, DisappearModeRequest, VanishModeRequest};
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use DB;

class ChatController extends Controller
{
    private $version = "v.1.0";
    protected $redis;

    function __construct(Request $request,Redis $redis) {
        $this->redis = Redis::connection();
    }

    public function getVersion(){ return $this->version; }

    //Delete redis key by pattern
    public function deleteCacheByPattern($pattern)
    {
        $cursor = '0';
        do {
            list($cursor, $keys) = Redis::scan($cursor, ['match' => $pattern, 'count' => 100]);

            if (!empty($keys)) {
                
                $addedPrefix = str_replace('hi_hello_database_chat', 'chat', $keys[0]);
                $deleted = Redis::del($addedPrefix);
                
            }
        } while ($cursor != '0');

        return response()->json(['message' => 'Cache deleted successfully']);
    }

    // Create New Chat Room 
    public function createChatRoom(Request $request)
    {
        $createRoomRequest = new CreateRoomRequest();
        if ($this->apiValidator($request->all(), $createRoomRequest->rules())) {
            // try {
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;

                $pattern = 'hi_hello_database_chat/room/roomList/'.$auth_id.':*';
                $totalroomkey = $auth_id.'_totalroom:*';
               
                $this->deleteCacheByPattern($pattern);
                $this->deleteCacheByPattern($totalroomkey);
                $participant = User::whereCustomId($request->participant_id)
                    ->where('id', '!=', $auth_id)->where('id', '!=', config('utility.system.system_user_id'))->whereIsActive('y')->firstOrFail();
                $participant_id = $participant ? $participant->id : NULL;
                //Here need to check data from mongo and fetch creator and participator data from mysql and send the response

                  $chat_room = ChatRoomMongoose::with([
                    'creator:id,user_id,custom_id,profile_photo,language_id,lang_code,full_name',
                    'participator:id,user_id,custom_id,profile_photo,language_id,lang_code,full_name',
                    'latestMessage.receiver:id,custom_id',
                    'latestMessage.sender:id,custom_id'
                   ])->where(function ($query) use ($auth_id, $participant_id) {
                        $query->whereCreatorId($auth_id)->where('participate_id', $participant_id);
                    })->orWhere(function ($query) use ($auth_id, $participant_id) {
                        $query->whereCreatorId($participant_id)->where('participate_id', $auth_id);
                    })->first();
                    
                if (empty($chat_room)) {
                    $chat_room = ChatRoomMongoose::firstOrCreate([
                        'creator_id'        =>  $auth_id,
                        'participate_id'    =>  $participant_id,
                        'block_by'          => NULL,
                        'is_active'         => true,
                        'vanish_mode'       => false,
                        'vanish_mode_by'    => NULL,
                        'disappear_mode'    => 'off',
                        'disappear_mode_by' => NULL,
                        'creator_cleared_at'=> NULL,
                        'participate_cleared_at'=>NULL,
                        'creator_deleted_at'=>NULL,
                        'participate_deleted_at'=>NULL,
                        'deleted_at'=>NULL,
                    ], [
                        'custom_id'         =>  getUniqueString('chat_rooms'),
                    ]);
                    // dd($chat_room);

                }

                $this->status = Response::HTTP_OK;
                return (new ChatRoomMongoResource($chat_room))->additional([
                    'meta'  =>  [
                        'message'   =>  trans('api.save', ['entity' =>  __('Chat room')]),
                        'is_ban'    =>  false,
                    ]
                ]);
            // } catch (ModelNotFoundException $exception) {
            //     switch ($exception->getModel()) {
            //         case 'App\Models\ChatRoom':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         case 'App\Models\User':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         default:
            //             $this->response['meta']['message'] = trans('api.went_wrong');
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //     };
            // } catch (\Exception $e) {
            //     $this->storeErrorLog($e, 'create_chat_room');
            // }
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
            
            // try {
                $user_type = 'participant';
                $cleared_time = '';
                $auth_id = $request->user() ? $request->user()->id : NULL;
                // $paginate = (int)$request->limit+(int)$request->offset;
                // $chatRoomKey = 'chat/room/'.$auth_id.':'.$request->room.'-'.$auth_id.'chatmessageChatRoom';
                // $key = 'chat/message/'.$auth_id.':'.$request->room.'-'.$auth_id.'chatmessage'.$paginate;
                // $callLogKey = 'chat/calllog/'.$auth_id.':'.$request->room.'-'.$auth_id.'chatmessageCallLog';
                // $totalChatKey = 'chat/message/'.$auth_id.':'.$request->room.'-'.$auth_id.'totalchat';
                
                // $chatRoomData = $this->redis->get($chatRoomKey);
                $participatorTime = '';$creatorTime = '';
                // if($chatRoomData){
                //     $room = json_decode($chatRoomData);
                //     if(!empty($room)){
                //         // dd($room);
                //         $participatorTime = $room->participatorTime;
                //         $creatorTime = $room->creatorTime;
                //     }
                // }else{
                      $roomId = new ObjectId($request->room);
                    //   Log::channel('mongodb')->debug('Fetching latest message', [
                    //     'id' => $roomId
                    //   ]);
                    $room = ChatRoomMongoose::where('_id',$roomId)->whereIsActive(true)->firstOrFail();
                    if(!empty($room)){
                        // dd($room->creator_id,$room->participate_id,$auth_id);
                        if($room->creator_id == $auth_id){
                            $participatorTime =  $room->participator->lastOnlineTimeStamp();
                        }
                        
                        if($room->participate_id == $auth_id){

                            $creatorTime = $room->creator->lastOnlineTimeStamp();
                        }
                        $roomData = $room->toArray();
                        $roomData['participatorTime'] = $participatorTime;
                        $roomData['creatorTime'] = $creatorTime;
                        // Log the result
                        // Log::channel('mongodb')->debug('Fetched message', [
                        //     'authMessage' => $room
                        // ]);
                        // dd($roomData);
                        // $this->redis->set($chatRoomKey, json_encode((object)$roomData), 'EX', 3600);
                    } 
                //}
                if($room->creator_id == $auth_id){
                    $user_type = 'creator';
                    $cleared_time  = $room->creator_cleared_at;
                }

                if($room->participate_id == $auth_id){
                    $cleared_time = $room->participate_cleared_at;
                }
                
                // $jsonData = $this->redis->get($key);
                // $messages=[];
                // if($jsonData){
                    
                //     $messages = json_decode($jsonData);
                //     $count = $this->redis->get($totalChatKey); 
                // }else{
                    // ->orWhere('status', '!=', 'read')
                    // DB::enableQueryLog();
                    $messagesQuery = ChatMessageMongoose::select(
                        'id', 'custom_id', 'room_id', 'sender_id', 'message', 'status', 'created_at',
                        'updated_at', 'deleted_at', 'is_vanished', 'reply_sender_id', 'reply_sender_name',
                        'reply_message_id', 'reply_type', 'reply_value', 'reply_message_file_path',
                        'reply_message_file_type'
                    )
                    ->where(function($expiredQuery) {
                        $expiredQuery->where('is_vanished', false);
                    })
                    ->where(function($senderDeletedQuery) use ($auth_id) {
                        $senderDeletedQuery->whereNull('sender_deleted_at')
                                           ->where('sender_id', '!=', $auth_id);
                    })->orWhere(function($senderDeletedQuery) use ($auth_id) {
                        $senderDeletedQuery->whereNull('sender_deleted_at')
                                           ->where('receiver_id', '!=', $auth_id);
                    });
                    
                    if (!empty($cleared_time)) {
                        $messagesQuery->where('created_at', '>', $cleared_time);
                    }
                    
                    if ($room->id == config('utility.chat.system_chat_room')) {
                        $messagesQuery->where('receiver_id', $auth_id);
                    } else {
                        $messagesQuery->withTrashed();
                    }
                    // Log the query being executed
                    Log::channel('mongodb')->debug('Executing query', [
                        'query' => $messagesQuery->toSql(),
                        'bindings' => $messagesQuery->getBindings()
                    ]);
                    
                    
                    // Execute the query and fetch results
                    $count = $messagesQuery->count();
                    $messages = $messagesQuery->limit($request->limit ?? config('utility.pagination.limit'))
                                            ->offset($request->offset ?? config('utility.pagination.offset'))
                                            ->get();

                    // Log::channel('mongodb')->debug('Fetched messages', [
                    //     'count' => $count,
                    //     'messages' => $messages
                    // ]);
                    //    dd($messages); 
                    if($messages->isNotEmpty()){
                        $jsonData = json_encode($messages->toArray());
                        // $this->redis->set($totalChatKey, $count, 'EX', 3600); 
                        // $this->redis->set($key, $jsonData, 'EX', 3600); 
                    }   
                //}
                
                $callLog = null;
                // $callLogData = $this->redis->get($callLogKey);
                // if($callLogData){
                //     $callLog = $callLogData;
                // }else{
                //     $callLog    =   CallLog::select('id', 'room_id', 'remaining_time')->where('date', now()->format('Y-m-d'))
                //     ->whereHas('room', function ($q) use ($request) {
                //         $q->whereCustomId($request->room)->whereIsActive('y');
                //     })->latest()->first();
                //     if(!empty($callLog)){
                //         $this->redis->set($callLogKey, json_encode((object)$callLog->toArray()), 'EX', 3600);
                //     }
                     
                // }
                if (!empty($messages)) {
                    return (ChatMessageMongoResource::Collection($messages))->additional([
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
                            'last_online' => ($user_type == 'creator') ? $participatorTime : $creatorTime,
                            'message'   =>  trans('api.list', ['entity' => __('Chat history')])
                        ],
                    ]);

                } else {
                    $this->response['meta']['remaining_time']  = $callLog ? $callLog->remaining_time : config('utility.twillio.allow_call_time');
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Chat history')]);
                    $this->response['meta']['is_ban'] = false;
                    $this->status = Response::HTTP_OK;
                }
            // } catch (ModelNotFoundException $exception) {
            //     switch ($exception->getModel()) {
            //         case 'App\Models\ChatRoom':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat rooms")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         case 'App\Models\ChatMessage':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat history")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         case 'App\Models\User':
            //             $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //         default:
            //             $this->response['meta']['message'] = trans('api.went_wrong');
            //             $this->response['meta']['is_ban'] = false;
            //             break;
            //     };
            // } catch (\Exception $e) {
            //     $this->storeErrorLog($e, 'get_chat_messages');
            // }
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
                $pattern = 'hi_hello_database_chat/room/roomList/'.$auth_id.':*';
                $totalroomkey = $auth_id.'_totalroom:*';
               
                $this->deleteCacheByPattern($pattern);
                $this->deleteCacheByPattern($totalroomkey);
                $room = ChatRoomMongoose::where('_id',$request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id)
                            ->orWhere('_id', config('utility.chat.system_chat_room'));
                    })->firstOrFail();

                if($room->id == config('utility.chat.system_chat_room')){
                    ChatMessageMongoose::where('room_id',$room->id)->where('receiver_id',$auth_id)->delete();
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
                $pattern = 'hi_hello_database_chat/room/roomList/'.$auth_id.':*';
                $totalroomkey = $auth_id.'_totalroom:*';
               
                $this->deleteCacheByPattern($pattern);
                $this->deleteCacheByPattern($totalroomkey);
                $room = ChatRoomMongoose::where('_id',$request->room_id)
                    ->where(function ($query) use ($auth_id) {
                        $query->where('creator_id', $auth_id)
                            ->orWhere('participate_id', $auth_id)
                            ->orWhere('_id', config('utility.chat.system_chat_room'));
                    })->firstOrFail();

                if($room->id == config('utility.chat.system_chat_room')){
                    ChatMessageMongoose::where('room_id',$room->id)->where('receiver_id',$auth_id)->delete();
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
