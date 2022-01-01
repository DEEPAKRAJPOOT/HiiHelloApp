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
            'first_name'        =>  'required_unless:action,'.$unless.'|min:2|max:100',
            'last_name'         =>  'required_unless:action,'.$unless.'|min:2|max:100',
            'email'             =>  'nullable|max:150|unique:users,email,'.$id.',id,deleted_at,NULL',
            'country_code'      =>  'required_unless:action,'.$unless.'|exists:countries,phonecode',
            'contact_no'        =>  'required_unless:action,'.$unless.'|digits_between:6,16|unique:users,contact_no,'.$id.',id,deleted_at,NULL',
            'birth_date'        =>  'required_unless:action,'.$unless.'|date|before:tomorrow',
            'gender'            =>  'required_unless:action,'.$unless.'|in:'.implode(',', ['Male','Female']),
            'interest'          =>  'nullable|in:'.implode(',', ['Male','Female', 'Both']),
            'profile_photo'     =>  'nullable|mimes:jpg,jpeg,png',
        ];
    }
}
