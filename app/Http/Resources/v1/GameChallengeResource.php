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
        $user = $this->getAuthUser();
        if($this->challenger_id != $user->id){

            return  [
                'id'        =>  $this->challengerUser->custom_id ?? "",
                'full_name' =>  $this->challengerUser->userTranslation ? $this->challengerUser->userTranslation->full_name : "",
                'gender'            =>  $this->challengerUser->gender ?? "",
                'age'               =>  $this->challengerUser->getAge(),
                'location'          =>  new LocationResource($this->challengerUser->location),
                'profile_photo'     =>  generateURL($this->challengerUser->profile_photo) ?? "",
                'Status'            => $this->getStatus(),
                'onlineStatus'      => $this->challengerUser->onlineStatus(),
                'isReadToPlay'      => $this->isReadToPlay($this->challengerUser->onlineStatus(),$this->getStatus()),
            ];

        }else{

            return  [
                'id'        =>  $this->challengeReceiverUser->custom_id ?? "",
                'full_name' =>  $this->challengeReceiverUser->userTranslation ? $this->challengeReceiverUser->userTranslation->full_name : "",
                'gender'            =>  $this->challengeReceiverUser->gender ?? "",
                'age'               =>  $this->challengeReceiverUser->getAge(),
                'location'          =>  new LocationResource($this->challengeReceiverUser->location),
                'profile_photo'     =>  generateURL($this->challengeReceiverUser->profile_photo) ?? "",
                'Status'            => $this->getStatus(),
                'onlineStatus'      => $this->challengeReceiverUser->onlineStatus(),
                'isReadToPlay'      => $this->isReadToPlay($this->challengeReceiverUser->onlineStatus(),$this->getStatus()),
            ];
        }

        //return parent::toArray($request);
    }

    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }

    public function isReadToPlay($onlineStatus,$status){
        if($onlineStatus === 'online' && $status === 'Accepted'){
            return true;
        }else{
            return false;
        }
    }
}
