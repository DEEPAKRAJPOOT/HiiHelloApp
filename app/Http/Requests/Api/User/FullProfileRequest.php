<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FullProfileRequest extends FormRequest
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
            'email'                     =>  'nullable|email|max:150|unique:users,email,'.Auth::id(),
            'about_me'                  =>  'nullable|min:3|max:1000',
            'fav_movie'                 =>  'nullable|min:1|max:250',

            'personalities'             =>  'nullable|array', // Multiple
            'remove_personalities'      =>  'nullable|array', // Multiple

            'education'                 =>  'nullable|max:100',
            'university_college'        =>  'nullable|max:100',
            'profession'                =>  'nullable|max:100',
            'religion'                  =>  'nullable|max:100',

            'relationship_status'       =>  'nullable|max:100',
            'i_am_here'                 =>  'nullable|max:100',
            'food_preference'           =>  'nullable|max:100',
            'drinking'                  =>  'nullable|max:100',
            'smoking'                   =>  'nullable|max:100',
            'pet'                       =>  'nullable|max:100',
            'star_sign'                 =>  'nullable|max:100',
            'community'                 =>  'nullable|max:100',

            // Main Image (To Upload New Image & Change Extra Image As Main Image)
            'profile_photo'             =>  'nullable|mimes:jpg,jpeg,png',
            'old_profile_photo'         =>  'nullable',

            // Extra Images (To Upload New Images & Add Sequence)
            'images'                    =>  'nullable|array|max:4',
            'images.file.*'             =>  'nullable|mimes:jpg,jpeg,png',
            'images.sequence.*'         =>  'required_with:images|numeric',

            // Extra Images (To Change Sequqence Of Old Images)
            'old_images'                =>  'nullable|array',
            'old_images.file.*'         =>  'nullable',
            'old_images.sequence.*'     =>  'required_with:old_images|numeric',

            // Video Upload
            'videos'                    =>  'nullable|array|max:1',
            'videos.*'                  =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',

            // Voice Upload
            'voice'                     =>  'nullable|mimes:audio/mpeg,mpga,mp3,wav,aac,m4a',
            'voice_answer'              =>  'required_with:voice|string|max:500',
        ];
    }
}
