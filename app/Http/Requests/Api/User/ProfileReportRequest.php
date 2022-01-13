<?php

namespace App\Http\Requests\api\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileReportRequest extends FormRequest
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
        $user_ids = User::where('id','!=',Auth::id())->whereIsActive('y')->pluck('custom_id')->toArray();

        return  [
            'reported_user'     =>  'required|in:'.implode(',', $user_ids),
            'message'           =>  'required|min:3|max:400',
        ];
    }
}
