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
            'is_active'     =>  $this->is_active,
            'creator'  =>  [
                'id'            =>  $this->creator ? $this->creator->custom_id : "",
                'first_name'    =>  $this->creator ? $this->creator->first_name : "",
                'last_name'     =>  $this->creator ? $this->creator->last_name : "",
                'profile'       =>  $this->creator ? generateURL($this->creator->profile_photo) : "",
            ],
            'participator'  =>  [
                'id'            =>  $this->participator ? $this->participator->custom_id : "",
                'first_name'    =>  $this->participator ? $this->participator->first_name : "",
                'last_name'     =>  $this->participator ? $this->participator->last_name : "",
                'profile'       =>  $this->participator ? generateURL($this->participator->profile_photo) : "",
            ],
            'message'   =>  [
                'id'            =>  $this->latestMessage ? $this->latestMessage->custom_id : "",
                'value'         =>  $this->latestMessage ? $this->latestMessage->message : "",
                'status'        =>  $this->latestMessage ? $this->latestMessage->status : "",
                'updated_at'    =>  $this->latestMessage ? $this->latestMessage->updated_at : "",
            ]
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
