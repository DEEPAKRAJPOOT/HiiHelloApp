<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CollegeResource extends JsonResource {
    public function toArray($request) {
        return [
            'id'            =>  $this->custom_id,
            'name'          =>  $this->name,
            'university'    =>  $this->university,
            'district'      =>  $this->district,
            'state'         =>  $this->state,
            'abbreviation'  =>  $this->abbreviation ?? '',
        ];
        return parent::toArray($request);
    }

    public function with($request) {
        return [
            'meta' => [
                'api'               =>  'v.1.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
