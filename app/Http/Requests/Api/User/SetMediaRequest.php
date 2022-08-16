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
            'remove_voice'              =>  'nullable|in:y,n',

            // Video Details
            'video'                     =>  'nullable|string|max:500',
            'remove_video'              =>  'nullable|max:100',

            // Image Details
            'image_path'                =>  'nullable|string|max:500',
            'remove_image'              =>  'nullable|string|max:500',

            // Image Sequence
            'image_sequence'            =>  'nullable|array',
            'image_sequence.*'          =>  'nullable',
        ];
    }
}
