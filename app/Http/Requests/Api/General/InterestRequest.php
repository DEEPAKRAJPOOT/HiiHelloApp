<?php

namespace App\Http\Requests\Api\General;

use Illuminate\Foundation\Http\FormRequest;

class InterestRequest extends FormRequest
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
    public function rules($request)
    {
        return [
            'search'        =>  'nullable|max:150',
            'parent_id'     =>  'nullable|max:100',
            'location_id'   =>  'nullable|max:100',
            'level'         =>  'required_with:parent_id|in:2,3',
            'limit'         =>  'nullable|numeric|min:5',
            'offset'        =>  'nullable|numeric|min:0',
        ];
    }
}
