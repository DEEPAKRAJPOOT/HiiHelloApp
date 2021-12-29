<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CmsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [];
        foreach ($this as $key => $value) {
            $data[] = [
                'id'                =>  $value->custom_id ?? "",
                'title'             =>  $value->getTitle() ?? "",
                'description'       =>  $value->getDescription() ?? "",
                'hint'              =>  $value->hint ?? "",
                'image'             =>  generateURL($value->file) ?? "",
            ];
        }
        return $data;
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
