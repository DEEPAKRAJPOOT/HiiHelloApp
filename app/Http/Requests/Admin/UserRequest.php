<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
        $unless = "change_status";
        $id = (!empty(Route::current()->parameters()['user']->id) ? Route::current()->parameters()['user']->id : NULL);
        $min_birth_date = now()->subYears(config('utility.minimum_age'))->format('m/d/Y');

        return [
            'full_name'                 =>  'required_unless:action,'.$unless.'|min:2|max:100',
            'email'                     =>  'nullable|max:150|unique:users,email,'.$id.',id,deleted_at,NULL',
            'country_code'              =>  'required_unless:action,'.$unless.'|exists:countries,phonecode',
            'contact_no'                =>  'required_unless:action,'.$unless.'|digits_between:6,16|unique:users,contact_no,'.$id.',id,deleted_at,NULL',
            'birth_date'                =>  'required_unless:action,'.$unless.'|date|before:'.$min_birth_date,
            'gender'                    =>  'required_unless:action,'.$unless.'|in:'.implode(',', ['Male','Female']),
            'interest'                  =>  'nullable|in:'.implode(',', ['Male','Female', 'Both']),
            'profession_id'             =>  'required_unless:action,'.$unless,
            'personality_id'            =>  'required_unless:action,'.$unless,
            'education_id'              =>  'required_unless:action,'.$unless,
            'university_id'             =>  'required_unless:action,'.$unless,
            'pet_id'                    =>  'required_unless:action,'.$unless,
            'religion_id'               =>  'required_unless:action,'.$unless,
            'community_id'              =>  'required_unless:action,'.$unless,
            'star_sign_id'              =>  'required_unless:action,'.$unless,
            'relationship_status_id'    =>  'required_unless:action,'.$unless,
            'drinking_id'               =>  'required_unless:action,'.$unless,
            'smoking_id'                =>  'required_unless:action,'.$unless,
            'you_are_here_id'           =>  'required_unless:action,'.$unless,
            'food_preference_id'        =>  'required_unless:action,'.$unless,
            'profile_photo'             =>  'nullable|mimes:jpg,jpeg,png',
            'photo_suggestion'          =>  'nullable|min:3|max:150',
            'video_suggestion'          =>  'nullable|min:3|max:150',
            'verify_photo_status'       =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
            'verify_video_status'       =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
            'verify_status'             =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
        ];
    }
}
