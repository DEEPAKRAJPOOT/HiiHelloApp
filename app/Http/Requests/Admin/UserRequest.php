<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use App\Models\Location;
use App\Models\Language;
use App\Models\Country;
use App\Models\Personality;

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

        // $location_ids = Location::whereIsActive('y')->pluck('id')->toArray();
        $language_ids = Language::whereIsActive('y')->pluck('lang_code')->toArray();
        $phone_codes = Country::whereIsActive('y')->pluck('phonecode')->toArray();
        $personality_ids = Personality::whereIsActive('y')->pluck('id')->toArray(); 

        return [
            // Sort Profile
            'full_name'                 =>  'required_unless:action,'.$unless.'|min:4|max:100',
            'email'                     =>  'nullable|max:150|unique:users,email,'.$id.',id,deleted_at,NULL',
            /*'country_code'              =>  'required_unless:action,'.$unless.'|in:'.implode(',', $phone_codes),
            'contact_no'                =>  'required_unless:action,'.$unless.'|digits_between:6,16|unique:users,contact_no,'.$id.',id,deleted_at,NULL',*/
            'birth_date'                =>  'required_unless:action,'.$unless.'|date|before:'.$min_birth_date,
            'gender'                    =>  'required_unless:action,'.$unless.'|in:'.implode(',', ['Male','Female']),
            'interest'                  =>  'required_unless:action,'.$unless.'|in:'.implode(',', ['Male','Female', 'Both']),
            // 'location'                  =>  'required_unless:action,'.$unless.'|in:'.implode(',', $location_ids),
            'location'                  =>  'nullable',
            'language'                  =>  'required_unless:action,'.$unless.'|in:'.implode(',', $language_ids),
            'profile_photo'             =>  'nullable|mimes:jpg,jpeg,png',

            // Full Profile
            'about_me'                  =>  'nullable|min:3|max:1000',
            'fav_movie'                 =>  'nullable|min:1|max:250',

            'personalities'             =>  'nullable|array|in:'.implode(',', $personality_ids),
            'education_id'              =>  'nullable',
            'university_id'             =>  'nullable',
            'profession_id'             =>  'nullable',
            'religion_id'               =>  'nullable',

            'relationship_status_id'    =>  'nullable',
            'you_are_here_id'           =>  'nullable',
            'food_preference_id'        =>  'nullable',
            'drinking_id'               =>  'nullable',
            'smoking_id'                =>  'nullable',
            'pet_id'                    =>  'nullable',
            'star_sign_id'              =>  'nullable',
            'community_id'              =>  'nullable',

            'traveling_id'              =>  'nullable|array',
            'music_id'                  =>  'nullable|array',
            'hobbie_id'                 =>  'nullable|array',
            'game_id'                   =>  'nullable|array',
            'sport_id'                  =>  'nullable|array',
            'food_id'                   =>  'nullable|array',
            'actor_id'                  =>  'nullable|array',
            'singer_id'                 =>  'nullable|array',

            // Verification
            'photo_suggestion'          =>  'nullable|min:3|max:150',
            'video_suggestion'          =>  'nullable|min:3|max:150',
            'verify_photo_status'       =>  'nullable|in:under_review,verified,unverified,pending',
            'verify_video_status'       =>  'nullable|in:under_review,verified,unverified,pending',
            'verify_status'             =>  'nullable|in:under_review,verified,unverified,pending',
        ];
    }
}
