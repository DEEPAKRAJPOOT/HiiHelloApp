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
            'creator'  =>  [
                'id'            =>  $this->creator ? $this->creator->custom_id : "",
                'first_name'    =>  $this->creator ? $this->creator->first_name : "",
                'last_name'     =>  $this->creator ? $this->creator->last_name : "",
                'profile'       =>  $this->creator ? $this->creator->profile_photo : "",
            ],
            'participator'  =>  [
                'id'            =>  $this->participator ? $this->participator->custom_id : "",
                'first_name'    =>  $this->participator ? $this->participator->first_name : "",
                'last_name'     =>  $this->participator ? $this->participator->last_name : "",
                'profile'       =>  $this->participator ? $this->participator->profile_photo : "",
            ],
            'latest_message'    =>  new ChatMessageResource($this->latestMessage),
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
