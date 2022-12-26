<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->custom_id ?? "",
            'language'      =>  $this->language ?? "",
            'lang_code'     =>  $this->lang_code ?? "",
            'hint'          =>  $this->hint ?? "",
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api'               =>  'v.2.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
