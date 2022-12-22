<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        // $lang_name = "";
        // if ($this->locationTranslation) {
        //     if (isset($this->locationTranslation->location_id)) {
        //         $result = DB::table('location_translations')->select('name')->where('location_id',$this->locationTranslation->location_id)->where('locale','en')->first();
        //         $lang_name = $result ? $result->name : '';
        //     }
        // }
        return [
            'id'            =>  $this->custom_id,
            'name'          =>  $this->locationTranslation ? $this->locationTranslation->name : "",
            'is_active'     =>  $this->is_active,
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
