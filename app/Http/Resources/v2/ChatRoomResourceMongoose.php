<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{ChatMessageMongoose, UsersMongoose};
use Carbon\Carbon;
use MongoDB\BSON\ObjectId;

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
        $auth_id = $request->user() ? $request->user()->id : null;
        // dd($this->getBlockByCount($this));
        // Convert room_id to ObjectId if necessary
        $roomId = $this->_id instanceof ObjectId ? $this->_id : new ObjectId($this->_id);

        $authLatestMessage = null;

        if ($this->_id == config('utility.chat.system_chat_room')) {
            $authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message', 'sender_id', 'created_on', 'updated_on')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->where('room_id', $roomId)
                ->where('receiver_id', $auth_id)
                ->orderBy('_id', 'desc')
                ->first();
        } elseif ($this->participate_id == $auth_id) {
            $authLatestMessage = $this->getLatestMessage($roomId, $auth_id, $this->participate_cleared_at);
        } elseif ($this->creator_id == $auth_id) {
            $authLatestMessage = $this->getLatestMessage($roomId, $auth_id, $this->creator_cleared_at);
        }
        $blockStatus = $this->getBlockByCount($this);
        // $is_block = (isset($this->blockByCount) && $this->blockByCount > 0) ? true : false;
        // dd($this->blockByCount);
        return [
            'id'            =>  $this->_id,
            'is_active'     =>  $this->is_active ? $this->is_active == 'y' ? true : false : false,
            'is_blocked'    =>  $blockStatus,
            'creator'       =>  $this->transformUser($this->creator_id ?? null),
            'participator'  =>  $this->transformUser($this->participate_id ?? null),
            'latest_message'=>  $authLatestMessage ? $this->transformMessage($authLatestMessage) : null,
            'is_system_room'=>  ($this->_id == config('utility.chat.system_chat_room')),
            'vanish_mode'   =>  (($this->vanish_mode ?? 'n') == 'y'),
            'disappear_mode'=>  $this->disappear_mode ?? 'off',
        ];
    }

    public function getBlockByCount($roomData){
        $blockStatus = false;
         if(!is_null($roomData->block_by)){
             if($roomData->block_by == $roomData->creator_id){
                $blockStatus = true;
             }else if($roomData->block_by == $roomData->participate_id){
                $blockStatus = true;
             }
         }
         return $blockStatus;
    }

    private function getLatestMessage($roomId, $auth_id, $cleared_at)
    {
        $authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message', 'sender_id', 'created_on', 'updated_on')
            ->with(['sender' => function ($query) {
                $query->select('custom_id');
            }])
            ->withTrashed()
            ->where('room_id', $roomId);

        if (!is_null($cleared_at)) {
            $authLatestMessage->where('created_at', '>', new \MongoDB\BSON\UTCDateTime(new \DateTime($cleared_at)));
        }
        
        $authLatestMessage = $authLatestMessage->where(function ($query) {
                $query->where('is_vanished', false);
                $query->orWhere('status', '!=', 'read');
            })
            ->where(function ($query) use ($auth_id) {
                $query->whereNull('sender_deleted_at');
                $query->orWhere('sender_id', '!=', $auth_id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return $authLatestMessage;
    }

    private function transformMessage($message)
    {
        // Ensure $message is an instance of the model
        if ($message instanceof ChatMessageMongoose) {
            return [
                'id'            =>  $message->custom_id ?? '',
                'message'       =>  $message->getMessage() ?? null,
                'status'        =>  strtr($message->status ?? '', ['send' => 'sent', 'read' => 'seen']),
                'sender'        =>  [
                    'id'        =>  $this->getSender($message->sender_id) ?? '',
                ],
                'chat_messages_count' =>  $this->chat_messages_count ?? 0,
                'created_at'    =>  $message->created_on ? $this->convertTimeZone($message->created_on) : '',
                'updated_at'    =>  $message->updated_on ? $this->convertTimeZone($message->updated_on) : '',
                'deleted_at'    =>  $message->deleted_at ? $this->convertTimeZone($message->deleted_at) : '',
                'expired_at'    =>  $message->expired_at ? $this->convertTimeZone($message->expired_at) : '',
                'is_vanished'   =>  (($message->is_vanished ?? 'n') == 'y')
            ];
        }

        return null;
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api'       =>  'v.1.0',
                'url'       =>  url()->current(),
                'language'  =>  app()->getLocale(),
            ],
        ];
    }

    private function transformUser($user_id)
    {
        $user = UsersMongoose::where('user_id', $user_id)->first();
        if (!$user || empty($user)) {
            return [
                'id'            => '',
                'full_name'     => '',
                'profile'       => '',
                'language'      => ['lang_code' => ''],
            ];
        }

        return [
            'id'            =>  $user->custom_id ?? "",
            'full_name'     =>  $user->full_name ?? "",
            'profile'       =>  $user->profile_photo ?? "",
            'language'      =>  [
                'lang_code' =>  $user->lang_code ?? "",
            ],
        ];
    }

    public function convertTimeZone($utcTimestamp)
    {
        $istTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $utcTimestamp, 'UTC')->setTimezone('Asia/Kolkata');
        return $istTimestamp->toDateTimeString();
    }

    public function getSender($sender_id)
    {
        if (!is_null($sender_id)) {
            $senderData = UsersMongoose::select('custom_id')->where('user_id', $sender_id)->first();
            if (!empty($senderData)) {
                return $senderData->custom_id;
            }
        }

        return "";
    }
}
