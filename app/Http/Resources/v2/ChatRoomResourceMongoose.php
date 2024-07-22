<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\{ChatMessageMongoose, UsersMongoose};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

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
        //     'room_id' => $this->id,
        //     'creator_cleared_at' => $this->creator_cleared_at
        // ]);
        $this->authLatestMessage = null;
        // dd($this->_id);
        $auth_id = $request->user() ? $request->user()->id : NULL;

        // Convert room_id to ObjectId if necessary
        $roomId = $this->_id instanceof ObjectId ? $this->_id : new ObjectId($this->_id);
        
        if($this->_id == config('utility.chat.system_chat_room')){
            $this->authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->where('room_id', $roomId)
                ->where('receiver_id', $auth_id)
                ->orderBy('_id', 'desc')
                ->first();
        } elseif($this->participate_id == $auth_id) {

                $this->authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->withTrashed()
                ->where('room_id', $roomId)
                ->where(function($query){
                    $query->where('is_vanished', true);
                    $query->orWhere('status', '!=', 'read');
                })
                ->where(function($query) use ($auth_id) {
                    $query->whereNull('sender_deleted_at');
                    $query->orWhere('sender_id', '!=', $auth_id);
                })
                ->orderBy('_id', 'desc')
                ->first();


            Log::channel('mongodb')->debug('Fetched latest message', [
                'authLatestMessage' => $this->authLatestMessage
            ]);
            
        } elseif($this->creator_id == $auth_id) {
            
            $this->authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
            ->with(['sender' => function ($query) {
                $query->select('custom_id');
            }])
            ->withTrashed()
            ->where('room_id', $roomId)
            ->where(function($query){
                $query->where('is_vanished', true);
                $query->orWhere('status', '!=', 'read');
            })
            ->where(function($query) use ($auth_id) {
                $query->whereNull('sender_deleted_at');
                $query->orWhere('sender_id', '!=', $auth_id);
            })
            ->orderBy('_id', 'desc')
            ->first();


            Log::channel('mongodb')->debug('Fetched latest message', [
                'authLatestMessage' => $this->authLatestMessage
            ]);
        }
        
        $lastMessageTimestamp = $this->convertTimeZone($this->authLatestMessage);
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
                    'id'    =>  $this->getSender($this->authLatestMessage->sender_id)?? '',
                ],
                'chat_messages_count'   =>  $this->chat_messages_count ?? 0,
                'created_at'  =>  $this->authLatestMessage->created_at??'',
                'updated_at'  =>  $this->authLatestMessage->updated_at??'',
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

    public function convertTimeZone($document) {
        // Ensure $document is an object and contains created_at and updated_at
        if (is_object($document) && isset($document->created_at) && isset($document->updated_at)) {
            // Check if created_at and updated_at are instances of MongoDB\BSON\UTCDateTime
            if ($document->created_at instanceof UTCDateTime) {
                $created_at = $document->created_at->toDateTime();
            } else {
                // Handle the error or log it
                // dd('Invalid created_at format', gettype($document->created_at), $document->created_at);
                $created_at = null;
            }
    
            if ($document->updated_at instanceof UTCDateTime) {
                $updated_at = $document->updated_at->toDateTime();
            } else {
                // Handle the error or log it
                // dd('Invalid updated_at format', gettype($document->updated_at), $document->updated_at);
                $updated_at = null;
            }
    
            // Convert to Carbon instance and set to IST if dates are valid
            if ($created_at && $updated_at) {
                $created_at_ist = Carbon::parse($created_at)->setTimezone('Asia/Kolkata');
                $updated_at_ist = Carbon::parse($updated_at)->setTimezone('Asia/Kolkata');
                
                // Format the date
                $created_at_formatted = $created_at_ist->format('Y-m-d H:i:s');
                $updated_at_formatted = $updated_at_ist->format('Y-m-d H:i:s');
                
                return array('created_at'=>$created_at_formatted, 'updated_at'=>$updated_at_formatted);
            } else {
                return [];
            }
        } else {
            // Handle the case where the document doesn't have the necessary fields
            return [];
        }
    }

    public function getSender($sender_id){
        if(!is_null($sender_id)){
            $senderData = UsersMongoose::select('custom_id')->where('user_id',$sender_id)->first();
            if(!empty($senderData)){
                return $senderData->custom_id;
            }
        }

        return "";
    }
}
