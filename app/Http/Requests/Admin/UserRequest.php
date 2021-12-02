<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
        $unless = "change_status";
        $id = (!empty(Route::current()->parameters()['user']->id) ? Route::current()->parameters()['user']->id : NULL);
        return [
            'first_name'        =>  'required_unless:action,'.$unless.'|max:50',
            'last_name'         =>  'required_unless:action,'.$unless.'|max:50',
            'email'             =>  'required_unless:action,'.$unless.'|max:150|unique:users,email,'.$id.',id,deleted_at,NULL',
            'contact_no'        =>  'nullable|min:6|max:16|unique:users,contact_no,'.$id.',id,deleted_at,NULL',
            'profile_photo'     =>  'nullable|mimes:jpeg,jpg,png',
        ];
    }
}
