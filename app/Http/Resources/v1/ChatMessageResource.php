<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource =  [
            'id'        =>  $this->custom_id ?? "",
            'message'   =>  $this->getMessage() ?? NULL,
            'status'    =>  strtr($this->status ?? "",['send'=>'sent','read'=>'seen']),
            'sender'  =>  [
                'id'    =>  $this->sender ? $this->sender->custom_id : "",
            ],
            'created_at'  =>  $this->created_at ?? "",
            'updated_at'  =>  $this->updated_at ?? "",
            'deleted_at'  =>  $this->deleted_at ?? "",
            'is_vanished' =>  (($this->is_vanished ?? 'n') == 'y')
        ];
        if($this->reply_sender_id !== null){
            // $resource['message']->reply_message->reply_sender_id=1;
            $resource['message'] = (array)$resource['message'];
            $resource['message']['reply_message']['reply_sender_id']= $this->reply_sender_id;
            $resource['message']['reply_message']['reply_message_id']= $this->reply_message_id;
            $resource['message']['reply_message']['reply_type'] = $this->reply_type;
            $resource['message']['reply_message']['reply_value'] = $this->reply_value;
            
            if($this->reply_type == 'file'){
                $resource['message']['reply_message']['reply_other']['reply_message_file_path'] = $this->reply_message_file_path;
                $resource['message']['reply_message']['reply_other']['reply_message_file_type']=$this->reply_message_file_type;
            }
            // dd($resource['message']);
        }
        
        return $resource;
        
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
