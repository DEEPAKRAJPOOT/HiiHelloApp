<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\ChatMessageMongoose;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatRoomResourceMongoose extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Log::channel('mongodb')->debug('Fetching latest message', [
        //     'room_id' => $this->_id,
        //     'creator_cleared_at' => $this->creator_cleared_at
        // ]);
        $this->authLatestMessage = null;
        $auth_id = $request->user() ? $request->user()->id : NULL;
        
        if($this->_id == config('utility.chat.system_chat_room')){
            // echo "System chat room";die;
            $this->authLatestMessage = ChatMessageMongoose::select('custom_id','status','created_at','updated_at','deleted_at','expired_at','is_vanished','message')->with(['sender' => function ($query) {
                $query->select('custom_id');
            }])->where('room_id',$this->id)->where('receiver_id',$auth_id)->orderBy('id','desc')->first();
        }
        elseif($this->participate_id == $auth_id){
            // echo "Participator chat room";die;
            $this->authLatestMessage = ChatMessageMongoose::select('custom_id','status','created_at','updated_at','deleted_at','expired_at','is_vanished','message')->with(['sender' => function ($query) {
                $query->select('custom_id');
            }])->withTrashed()->where('room_id',$this->id)
            // ->where('created_at','>',$this->participate_cleared_at ?? '')
            ->where(function($query){
                $query->where('is_vanished','n');
                $query->orWhere('status','!=','read');
            })->where(function($query)use($auth_id){
                $query->whereNull('sender_deleted_at');
                $query->orWhere('sender_id','!=',$auth_id);
            })->orderBy('id','desc')->first();
        }elseif($this->creator_id == $auth_id){
            DB::enableQueryLog();
            $this->authLatestMessage = ChatMessageMongoose::select('custom_id','status','created_at','updated_at','deleted_at','expired_at','is_vanished','message')->with(['sender' => function ($query) {
                $query->select('custom_id');
            }])->withTrashed()->where('room_id',$this->_id)
            // ->where('created_at','>',$this->creator_cleared_at ?? '')
            ->where(function($query){
                $query->where('is_vanished','n');
                $query->orWhere('status','!=','read');
            })->where(function($query)use($auth_id){
                $query->whereNull('sender_deleted_at');
                $query->orWhere('sender_id','!=',$auth_id);
            })->orderBy('id','desc')->first();
            
            // Log the result
            // Log::channel('mongodb')->debug('Fetched latest message', [
            //     'authLatestMessage' => $this->authLatestMessage
            // ]);

            // dd($this->authLatestMessage);
        }
        
        return [
            'id'            =>  $this->_id,
            'is_active'     =>  $this->is_active ? $this->is_active == 'y' ? true : false : false,
            'is_blocked'    =>  (isset($this->block_by_count) && !empty($this->block_by_count))?$this->block_by_count:false,
            'creator'  =>  [
                'id'            =>  $this->creator ? $this->creator->custom_id : "",
                'full_name'     =>  $this->creator ?$this->creator->full_name : "",
                'profile'       =>  $this->creator ? $this->creator->profile_photo : "",
                'language'      =>  [
                    'lang_code' =>  $this->creator ? $this->creator->lang_code : "",
                ],
            ],
            'participator'  =>  [
                'id'            =>  $this->participator ? $this->participator->custom_id : "",
                'full_name'     =>  $this->participator ? 
                                        $this->participator->full_name : "",
                'profile'       =>  $this->participator ? $this->participator->profile_photo : "",
                'language'      =>  [
                    'lang_code' =>  $this->participator ? $this->participator->lang_code : "",
                ],
            ],
            'latest_message'    =>  $this->authLatestMessage ? [
                'id'        =>  $this->authLatestMessage->custom_id ?? '',
                'message'   =>  $this->authLatestMessage->getMessage() ?? null,
                'status'    =>  strtr($this->authLatestMessage->status ?? '',['send'=>'sent','read'=>'seen']),
                'sender'  =>  [
                    'id'    =>  $this->authLatestMessage->sender ? $this->authLatestMessage->sender->custom_id : '',
                ],
                'chat_messages_count'   =>  $this->chat_messages_count ?? 0,
                'created_at'  =>  $this->authLatestMessage->created_at ?? '',
                'updated_at'  =>  $this->authLatestMessage->updated_at ?? '',
                'deleted_at'  =>  $this->authLatestMessage->deleted_at ?? '',
                'expired_at'  =>  $this->authLatestMessage->expired_at ?? '',
                'is_vanished'  =>  (($this->authLatestMessage->is_vanished ?? 'n') == 'y')
            ] : null,
            'is_system_room' =>  ($this->_id == config('utility.chat.system_chat_room')),
            'vanish_mode'    =>  (($this->vanish_mode ?? 'n') == 'y'),
            'disappear_mode' =>  $this->disappear_mode ?? 'off',
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api'           =>  'v.1.0',
                'url'           =>  url()->current(),
                'language'      =>  app()->getLocale(),
            ],
        ];
    }
}
