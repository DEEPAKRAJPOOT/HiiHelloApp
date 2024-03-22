<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class GameChallengeResource extends JsonResource
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
            'id'        =>  $this->challengerUser->custom_id ?? "",
            'full_name' =>  $this->challengerUser->userTranslation ? $this->challengerUser->userTranslation->full_name : "",
            'gender'            =>  $this->challengerUser->gender ?? "",
            'trusted_score'     =>  $this->challengerUser->trusted_score,
            'location'          =>  new LocationResource($this->challengerUser->location),
            'interests'         =>  HomeInterestResource::collection($this->challengerUser->interests),
            'profile_photo'     =>  generateURL($this->challengerUser->profile_photo) ?? "",
            'flags'             =>  [
                'verified_status'  =>  $this->challengerUser->verify_status,
                'distance'         =>  $this->challengerUser->distance ?? 0,
                'last_seen'        =>  strtotime($this->challengerUser->last_online) * 1000,
                'likes_count'      => $this->challengerUser->likes_count??0
            ],

        ];

        //return parent::toArray($request);
    }
}
