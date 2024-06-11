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
        // dd($this);
        $resource =  [
            'id'        =>  $this->custom_id ?? "",
            'message'   =>  $this->getMessage($this->message) ?? NULL,
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

    public function getMessage($message){
        $message = json_decode( preg_replace("/\r|\n/", " ", $this->message) );
        if(!empty($message) && !empty($message->type)){            
            if($message->type == 'location'){
                if(!empty($message->other) && !empty($message->other->lat && !empty($message->other->lng) ) ){
                    $message->other->url = 'https://maps.googleapis.com/maps/api/staticmap?center='.$message->other->lat.','.$message->other->lng.'&zoom=14&size=400x400&markers='.$message->other->lat.','.$message->other->lng.'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ';
                }
            }
        }
        return $message;
    }
}
