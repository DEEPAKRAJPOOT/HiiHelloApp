<?php

namespace App\Http\Requests\Api\Twillio;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChatRoom;

class StoreCallLogRequest extends FormRequest
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
        return [
            'room'              =>  'required|max:100',
            'start_time'        =>  'required',
            'end_time'          =>  'required',
            'remaining_time'    =>  'required',
        ];
    }
}
