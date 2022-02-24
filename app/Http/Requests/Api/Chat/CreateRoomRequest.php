<?php

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\ { Auth };
use App\Models\ { User };

class CreateRoomRequest extends FormRequest
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
        $participant_ids = User::where('id','!=',Auth::id())->whereIsActive('y')->pluck('custom_id')->toArray();

        return [
            'participant_id'      =>  'required|in:'.implode(',',$participant_ids),
        ];
    }
}
