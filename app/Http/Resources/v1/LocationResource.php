<?php

namespace App\Http\Resources\v1;

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
        if(isset($this->location_translation)){
            $this->locationTranslation = $this->location_translation;
        }
        
        return [
            'id'            =>  $this->custom_id ?? "",
            'name'          =>  (!empty($this->locationTranslation)) ? $this->locationTranslation->name : "",
            'is_active'     =>  $this->is_active??"",
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
