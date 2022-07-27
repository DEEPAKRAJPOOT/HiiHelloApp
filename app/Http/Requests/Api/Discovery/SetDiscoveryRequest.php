<?php

namespace App\Http\Requests\Api\Discovery;

use Illuminate\Foundation\Http\FormRequest;

class SetDiscoveryRequest extends FormRequest
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
            'distance'          =>  'required|numeric|min:1|max:500',
            'start_age'         =>  'required|numeric|min:1|max:100',
            'end_age'           =>  'required_with:start_age|numeric|min:1|max:100',
            'interest'          =>  'required|in:Male,Female,Both',
            'location'          =>  'required|max:100',
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'required',
        ];
    }
}
