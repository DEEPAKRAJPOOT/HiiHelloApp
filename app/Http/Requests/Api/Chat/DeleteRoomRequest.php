<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatRoom;

class DeleteRoomRequest extends FormRequest
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
        $room_ids = ChatRoom::where('creator_id',$auth_id) ->orWhere('participate_id',$auth_id)->pluck('custom_id')->toArray();
        
        return [
            'room_id'      =>  'required|in:'.implode(',',$room_ids),
        ];
    }
}
