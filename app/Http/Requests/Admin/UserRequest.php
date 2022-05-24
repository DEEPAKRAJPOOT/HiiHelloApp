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
            'profession_id'             =>  'nullable',
            'personality_id'            =>  'nullable',
            'education_id'              =>  'nullable',
            'university_id'             =>  'nullable',
            'pet_id'                    =>  'nullable',
            'religion_id'               =>  'nullable',
            'community_id'              =>  'nullable',
            'star_sign_id'              =>  'nullable',
            'relationship_status_id'    =>  'nullable',
            'drinking_id'               =>  'nullable',
            'smoking_id'                =>  'nullable',
            'you_are_here_id'           =>  'nullable',
            'food_preference_id'        =>  'nullable',
            'fav_movie'                 =>  'nullable',
            'traveling_id'              =>  'nullable',
            'music_id'                  =>  'nullable',
            'hobbie_id'                 =>  'nullable',
            'game_id'                   =>  'nullable',
            'sport_id'                  =>  'nullable',
            'film_id'                   =>  'nullable',
            'actor_id'                  =>  'nullable',
            'depend_id'                 =>  'nullable',
            'singer_male_id'            =>  'nullable',
            'singer_id'                 =>  'nullable',
            'food_id'                   =>  'nullable',
            'profile_photo'             =>  'nullable|mimes:jpg,jpeg,png',
            'photo_suggestion'          =>  'nullable|min:3|max:150',
            'video_suggestion'          =>  'nullable|min:3|max:150',
            'verify_photo_status'       =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
            'verify_video_status'       =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
            'verify_status'             =>  'required_unless:action,'.$unless.'|in:under_review,verified,unverified',
        ];
    }
}
