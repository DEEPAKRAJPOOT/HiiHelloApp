<?php

namespace App\Http\Requests\Api\Match;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DeleteMatchRequest extends FormRequest
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
        $user_ids = User::where('id','!=',Auth::id())->pluck('custom_id')->toArray();

        return [
            'user_id'      =>  'required|in:'.implode(',',$user_ids),
        ];
    }
}
