<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDetailResource extends JsonResource
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
            'id'            =>  $this->id,
            'slug'          =>  $this->slug,
            'attribute'     =>  $this->attribute,
            'type'          =>  $this->type,
            'value'         =>  $this->profileDetailTranslation ? $this->profileDetailTranslation->value : "",
            'is_active'     =>  $this->is_active,
        ];
        return parent::toArray($request);
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
