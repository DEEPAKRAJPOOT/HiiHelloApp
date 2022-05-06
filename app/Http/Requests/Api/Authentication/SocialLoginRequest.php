<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
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
            'full_name'                 =>  'nullable|min:2|max:100',
            'email'                     =>  'nullable|email|max:150',
            'profile_photo'             =>  'nullable|mimes:jpeg,png',
            'type'                      =>  'required|in:facebook,google,apple',
            $request->type.'_id'        =>  'required|string|min:5',
        ];
    }
}
