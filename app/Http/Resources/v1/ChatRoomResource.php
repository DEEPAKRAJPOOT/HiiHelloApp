<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\ChatMessageResource;

class ChatRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->custom_id,
            'is_active'     =>  $this->is_active,
            'is_blocked'    =>  $this->block_by_count > 0 ? "y" : "n",
            'creator'  =>  [
                'id'            =>  $this->creator ? $this->creator->custom_id : "",
                'full_name'     =>  $this->creator ? $this->creator->full_name : "",
                'profile'       =>  $this->creator ? $this->creator->profile_photo : "",
            ],
            'participator'  =>  [
                'id'            =>  $this->participator ? $this->participator->custom_id : "",
                'full_name'     =>  $this->participator ? $this->participator->full_name : "",
                'profile'       =>  $this->participator ? $this->participator->profile_photo : "",
            ],
            'latest_message'    =>  [
                'id'        =>  $this->latestMessage ? $this->latestMessage->custom_id ?? "" : "",
                'message'   =>  $this->latestMessage ? $this->latestMessage->getMessage() ?? "" : "",
                'status'    =>  $this->latestMessage ? $this->latestMessage->status ?? "" : "",
                'sender'  =>  [
                    'id'    =>  $this->latestMessage ? $this->latestMessage->sender ? $this->latestMessage->sender->custom_id : "" : "",
                ],
                'chat_messages_count'   =>  $this->chat_messages_count,
                'created_at'  =>  $this->latestMessage ? $this->latestMessage->created_at ?? "" : "",
                'updated_at'  =>  $this->latestMessage ? $this->latestMessage->updated_at ?? "" : "",
            ],
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
