<?php

namespace App\Http\Requests\Api\General;

use Illuminate\Foundation\Http\FormRequest;

class ProfileDetailRequest extends FormRequest
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
            'search'        =>  'nullable|max:150',
            'attribute'     =>  'required|string',
            'limit'         =>  'nullable|numeric|min:5',
            'offset'        =>  'nullable|numeric|min:0',
        ];
    }
}
