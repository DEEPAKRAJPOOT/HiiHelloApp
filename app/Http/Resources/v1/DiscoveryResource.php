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
            'profile_ranking' => $this->discover_profile_ranking??0,
            'has_photo' =>       $this->discover_has_photo??0,
            'search_near_me' => $this->discover_search_near_me??0,
            'search_by_state' => $this->discover_by_state??0,
            'state'    => $this->discover_state??"",
            'online_status' => $this->discover_online_status??0,
            'relationship_status' => $this->discover_relationship_status??0,
            'education' => $this->discover_education??0,
            'verified_profile'  => $this->discover_verified_profile??0,
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
