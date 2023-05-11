<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfileFilterRequest extends FormRequest
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
            'limit'                     =>  'nullable|numeric',
            'offset'                    =>  'nullable|numeric',

            'relationship_status'       =>  'nullable|max:100',
            'college'                   =>  'nullable|max:100',
            'star_sign'                 =>  'nullable|max:100',
            'fav_movie'                 =>  'nullable|min:1|max:250',

            'personalities'             =>  'nullable|array',
            'interests'                 =>  'nullable|array',
            'interests.*'               =>  'nullable',
        ];
    }
}
