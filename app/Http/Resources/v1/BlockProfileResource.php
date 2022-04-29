<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockProfileResource extends JsonResource
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
            'id'            =>  $this->custom_id ?? "",
            'blocked_to'    =>  [
                'id'                =>  $this->blockedTo ? $this->blockedTo->custom_id : "",
                'full_name'         =>  $this->blockedTo ? $this->blockedTo->full_name : "",
                'age'               =>  $this->blockedTo ? $this->blockedTo->getAge() : 0,
                'profile_photo'     =>  $this->blockedTo ? generateURL($this->blockedTo->profile_photo) : "",
                'location'          =>  $this->blockedTo ? new LocationResource($this->blockedTo->location) : "",
            ],
            'created_at'    =>  $this->created_at ?? "",
            'updated_at'    =>  $this->updated_at ?? "",
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
