<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class SetMediaRequest extends FormRequest
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
            // Voice Details
            'voice'                     =>  'nullable|string|max:500',
            'voice_answer'              =>  'required_with:voice|string|max:500',

            // Video Details
            'videos'                    =>  'nullable|array|max:1',
            'videos.*'                  =>  'nullable',

            // Image Details
            'image_path'                =>  'nullable|string|max:500',
            'image_sequence'            =>  'nullable|array',
            'image_sequence.*'          =>  'nullable',
        ];
    }
}
