<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscoveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
public function toArray($request)
    {
        $languages = [];
        foreach($this->userSettings as $userSetting){
            $languages[] = new LanguageResource($userSetting->language);
        }

        return [
            'id'                =>  $this->custom_id ?? "",
            'distance'          =>  $this->discover_distance ?? 0,
            'start_age'         =>  $this->discover_start_age ?? 0,
            'end_age'           =>  $this->discover_end_age ?? 0,
            'interest'          =>  $this->interest ?? "",
            'location'          =>  new LocationResource($this->discoveryLocation),
            'languages'         =>  $languages,
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api'               =>  'v.1.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
