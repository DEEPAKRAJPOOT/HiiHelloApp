<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfileDetail;
use App\Models\Interest;
use App\Models\Personality;

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
    public function rules($request)
    {
        $personality_ids = array();
        $profile_details = ProfileDetail::whereIsActive('y')->pluck('slug')->toArray();

        if(!empty($request->personalities) || $request->has('personalities') || !empty($request->remove_personalities) || $request->has('remove_personalities')){ 
            $personality_ids = Personality::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        return [
            'email'                     =>  'nullable|email|max:150|unique:users,email,'.Auth::id(),
            'about_me'                  =>  'nullable|min:3|max:1000',
            'fav_movie'                 =>  'nullable|min:1|max:250',

            'personalities'             =>  'nullable|array|in:'.implode(',', $personality_ids), // Multiple
            'remove_personalities'      =>  'nullable|array|in:'.implode(',', $personality_ids), // Multiple

            'education'                 =>  'nullable|in:'.implode(',', $profile_details),
            'university_college'        =>  'nullable|in:'.implode(',', $profile_details),
            'profession'                =>  'nullable|in:'.implode(',', $profile_details),
            'religion'                  =>  'nullable|in:'.implode(',', $profile_details),

            'relationship_status'       =>  'nullable|in:'.implode(',', $profile_details),
            'i_am_here'                 =>  'nullable|in:'.implode(',', $profile_details),
            'food_preference'           =>  'nullable|in:'.implode(',', $profile_details),
            'drinking'                  =>  'nullable|in:'.implode(',', $profile_details),
            'smoking'                   =>  'nullable|in:'.implode(',', $profile_details),
            'pet'                       =>  'nullable|in:'.implode(',', $profile_details),
            'star_sign'                 =>  'nullable|in:'.implode(',', $profile_details),
            'community'                 =>  'nullable|in:'.implode(',', $profile_details),

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
