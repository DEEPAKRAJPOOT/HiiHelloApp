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
            'message'   =>  $this->getMessage() ?? NULL,
            'status'    =>  strtr($this->status ?? "",['send'=>'sent','read'=>'seen']),
            'sender'  =>  [
                'id'    =>  $this->sender ? $this->sender->custom_id : "",
            ],
            'created_at'  =>  $this->created_at ?? "",
            'updated_at'  =>  $this->updated_at ?? "",
            'deleted_at'  =>  $this->deleted_at ?? "",
            'is_disappearing_message' => !empty($this->expired_at)
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
