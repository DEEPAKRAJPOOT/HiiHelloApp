<?php

namespace App\Http\Requests\Api\Twillio;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccessTokenRequest extends FormRequest
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
            'api_key'       =>  'required|string',
            'api_secret'    =>  'required|string',
            'room_name'     =>  'required|string',
            'identity'      =>  'required|string',
            'time_line'     =>  'nullable|numeric'
        ];
    }
}
