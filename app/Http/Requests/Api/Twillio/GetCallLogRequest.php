<?php

namespace App\Http\Requests\Api\Twillio;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChatRoom;

class GetCallLogRequest extends FormRequest
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
        $chat_rooms_ids = ChatRoom::pluck('custom_id')->toArray();

        return [
            'room'      =>  'required|in:'.implode(',',$chat_rooms_ids),
        ];
    }
}
