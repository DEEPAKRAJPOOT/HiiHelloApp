<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class AddDeviceTokenRequest extends FormRequest
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
            'device'        =>  'required|min:2',
            'token'         =>  'required|min:5',
            'type'          =>  'required|in:ios,android',
            'version'       =>  'required|string',
            'os'            =>  'required|string',
            'app_version'   =>  'required|string'
        ];
    }
}
