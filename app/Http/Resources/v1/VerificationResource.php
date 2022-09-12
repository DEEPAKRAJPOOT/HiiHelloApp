<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class VerificationResource extends JsonResource
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
            'flags'             =>  [
                'photo' =>   [
                    'verified_status'   =>  $this->verify_photo_status ?? "",
                    'verified_at'       =>  $this->photo_verified_at ?? "",
                    'suggestion'        =>  $this->photo_suggestion ?? "",
                ],
                'video' =>   [
                    'verified_status'   =>  $this->verify_video_status ?? "",
                    'verified_at'       =>  $this->video_verified_at ?? "",
                    'suggestion'        =>  $this->video_suggestion ?? "",
                ],
                'email' =>   [
                    'verified_status'   =>  $this->emailVerifyStatus(),
                    'verified_at'       =>  $this->email_verified_at ?? "",
                ],
                'contact_no' =>   [
                    'verified_status'   =>  $this->contactVerifyStatus(),
                    'verified_at'       =>  $this->contact_verified_at ?? "",
                ],
            ],
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
