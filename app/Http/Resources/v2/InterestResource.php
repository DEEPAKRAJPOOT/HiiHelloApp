<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;

class InterestResource extends JsonResource
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
            'id'                    =>  $this->custom_id ?? "",
            'title'                 =>  $this->interestTranslation ? $this->interestTranslation->title : "",
            'parent_id'             =>  $this->parentInterest ? $this->parentInterest->custom_id : "",
            'master_parent_id'      =>  $this->masterInterest ? $this->masterInterest->custom_id : "",
            'level'                 =>  $this->level ?? 0,
            'sub_interests_count'   =>  $this->sub_interests_count ?? 0,
            'is_required'           =>  $this->is_required ?? "",
            'is_active'             =>  $this->is_active ?? "",
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
