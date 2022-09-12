<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CallLogResource extends JsonResource
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
            'id'                =>  $this->custom_id ?? "",
            'date'              =>  $this->callLog ? $this->callLog->date : now()->format('Y-m-d'),
            'start_time'        =>  $this->callLog ? $this->callLog->start_time : "",
            'end_time'          =>  $this->callLog ? $this->callLog->end_time : "",
            'remaining_time'    =>  $this->callLog ? $this->callLog->remaining_time : config('utility.twillio.allow_call_time'),
        ];
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api'           =>  'v.1.0',
                'url'           =>  url()->current(),
                'language'      =>  app()->getLocale(),
            ],
        ];
    }
}
