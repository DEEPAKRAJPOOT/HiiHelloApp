<?php

namespace App\Http\Requests\Api\General;

use Illuminate\Foundation\Http\FormRequest;

class CheckLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location'     =>  'required|string',
        ];
    }
}
