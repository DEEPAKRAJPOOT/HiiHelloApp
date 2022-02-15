<?php

namespace App\Http\Requests\api\general;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
            'limit'         =>  'nullable|numeric',
            'offset'        =>  'nullable|numeric',
            'search'        =>  'nullable|max:150',
        ];
    }
}
