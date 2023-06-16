<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'is_active'     =>  $this->is_active ? $this->is_active == 'y' ? true : false : false,
            'is_blocked'    =>  $this->block_by_count ? $this->block_by_count > 0 ? true : false : false,
            'creator'  =>  [
                'id'            =>  $this->creator ? $this->creator->custom_id : "",
                'full_name'     =>  $this->creator ? 
                                        $this->creator->userTranslation ? $this->creator->userTranslation->full_name : ""
                                    : "",
                'profile'       =>  $this->creator ? $this->creator->profile_photo : "",
                'language'      =>  [
                    'lang_code' =>  $this->creator ? $this->creator->language ? $this->creator->language->lang_code : "": "",
                ],
            ],
            'participator'  =>  [
                'id'            =>  $this->participator ? $this->participator->custom_id : "",
                'full_name'     =>  $this->participator ? 
                                        $this->participator->userTranslation ? $this->participator->userTranslation->full_name : ""
                                    : "",
                'profile'       =>  $this->participator ? $this->participator->profile_photo : "",
                'language'      =>  [
                    'lang_code' =>  $this->participator ? $this->participator->language ? $this->participator->language->lang_code : "": "",
                ],
            ],
            'latest_message'    =>  [
                'id'        =>  $this->latestMessage->custom_id ?? '',
                'message'   =>  $this->latestMessage ? ($this->latestMessage->getMessage() ?? null) : null,
                'status'    =>  strtr($this->latestMessage->status ?? '',['send'=>'sent','read'=>'seen']),
                'sender'  =>  [
                    'id'    =>  $this->latestMessage ? ($this->latestMessage->sender ? $this->latestMessage->sender->custom_id : '') : '',
                ],
                'chat_messages_count'   =>  $this->chat_messages_count ?? 0,
                'created_at'  =>  $this->latestMessage->created_at ?? '',
                'updated_at'  =>  $this->latestMessage->updated_at ?? '',
            ],
            'is_system_room' =>  ($this->id == config('utility.chat.system_chat_room')),
            'vanish_mode'    =>  (($this->vanish_mode ?? 'n') == 'y'),
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
