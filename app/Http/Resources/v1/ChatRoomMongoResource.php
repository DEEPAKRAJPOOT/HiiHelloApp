<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{ChatMessageMongoose, UsersMongoose};
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime as MongoDateTime;

class ChatRoomMongoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id'            =>  $this->_id,
            'is_active'     =>  $this->is_active,
            'is_blocked'    =>  isset($this->block_by_count)?$this->block_by_count ? $this->block_by_count > 0 ? true : false : false:false,
            'creator'  =>  $this->transformUser($this->creator_id ?? null),
            'participator'  =>  $this->transformUser($this->participate_id ?? null),
            'latest_message'    =>  [
                'id'        =>  $this->latestMessage->id ?? '',
                'message'   =>  $this->latestMessage ? ($this->latestMessage->getMessage() ?? null) : null,
                'status'    =>  strtr($this->latestMessage->status ?? '',['send'=>'sent','read'=>'seen']),
                'sender'  =>  [
                    'id'    =>  $this->latestMessage ? ($this->latestMessage->sender ? $this->latestMessage->sender->custom_id : '') : '',
                ],
                'chat_messages_count'   =>  $this->chat_messages_count ?? 0,
                'created_at'  =>  $this->latestMessage ? Carbon::parse($this->latestMessage->created_at)->setTimezone('Asia/Kolkata')->toDateTimeString() ?? '':'',
                'updated_at'  =>  $this->latestMessage ? Carbon::parse($this->latestMessage->updated_at)->setTimezone('Asia/Kolkata')->toDateTimeString() ?? '':'',
            ],
            'is_system_room' =>  ($this->id == config('utility.chat.system_chat_room')),
            'vanish_mode'    =>  (($this->vanish_mode ?? 'n') == 'y'),
            'disappear_mode'    =>  $this->disappear_mode ?? 'off',
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


    private function transformUser($user_id)
    {
        // dd($user_id);
        $user = UsersMongoose::where('user_id',$user_id)->first();
        if (!$user || empty($user)) {
            return [
                'id' => '',
                'full_name' => '',
                'profile' => '',
                'language' => ['lang_code' => ''],
            ];
        }

        // Ensure $user is an array and has the first element
        $userData = is_array($user) && isset($user[0]) ? $user[0] : $user;

        return [
            'id'            =>  $userData['custom_id'] ?? "",
            'full_name'     =>  $userData['full_name'] ?? "",
            'profile'       =>  $userData['profile_photo'] ?? "",
            'language'      =>  [
                'lang_code' =>  $userData['lang_code'] ?? "",
            ],
        ];
    }
}
