<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
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
            'limit'             =>  'nullable|numeric',
            'offset'            =>  'nullable|numeric',
            'gender'            =>  'nullable|in:'.implode(',', ['Male','Female']),
        ];
    }
}
