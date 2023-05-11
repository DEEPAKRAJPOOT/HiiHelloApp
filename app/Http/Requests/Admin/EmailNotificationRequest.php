<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmailNotificationRequest extends FormRequest
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
        return [
            'user_type'     =>  'required_unless:action,'.$unless,
            'subject'       =>  'required_unless:action,'.$unless.'|min:3|max:500',
            'message'       =>  'required_unless:action,'.$unless.'|min:3',
        ];
    }
}
