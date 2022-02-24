<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\ { Auth };
use App\Models\ { ChatRoom };

class ChatMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $auth_id = Auth::id();
        $chat_rooms_ids = ChatRoom::whereCreatorId($auth_id)->orWhere('participate_id',$auth_id)->whereIsActive('y')->pluck('custom_id')->toArray();
        
        return [
            'room'      =>  'required|min:2|max:150|in:'.implode(',',$chat_rooms_ids),
            'limit'     =>  'nullable|numeric|min:5',
            'offset'    =>  'nullable|numeric|min:0',
        ];

    }
}
