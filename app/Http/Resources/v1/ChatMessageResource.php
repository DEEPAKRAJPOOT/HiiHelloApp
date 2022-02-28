<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
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
            'id'        =>  $this->custom_id ?? "",
            'message'   =>  $this->getMessage() ?? "",
            'status'    =>  $this->status ?? "",
            // 'flags'     =>  [
            //     'is_sender' =>  $this->isSender(),
            // ],
            'sender'  =>  [
                'id'            =>  $this->sender ? $this->sender->custom_id : "",
                // 'first_name'    =>  $this->sender ? $this->sender->first_name : "",
                // 'last_name'     =>  $this->sender ? $this->sender->last_name : "",
                // 'profile'       =>  $this->sender ? generateURL($this->sender->profile_photo) : "",
            ],
            // 'receiver'  =>  [
            //     'id'            =>  $this->receiver ? $this->receiver->custom_id : "",
            //     'first_name'    =>  $this->receiver ? $this->receiver->first_name : "",
            //     'last_name'     =>  $this->receiver ? $this->receiver->last_name : "",
            //     'profile'       =>  $this->receiver ? generateURL($this->receiver->profile_photo) : "",
            // ],
            'updated_at'  =>  $this->updated_at->format('y-m-d h:m:s') ?? "",
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
