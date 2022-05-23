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
        $interest_ids = $personality_ids = array();
        $profile_details = ProfileDetail::whereIsActive('y')->pluck('slug')->toArray();

        if(!empty($request->interests) || $request->has('interests') || 
            !empty($request->remove_interests) || $request->has('remove_interests') || 
            !empty($request->hobby) || $request->has('hobby') ||
            !empty($request->fav_sport) || $request->has('fav_sport')) { 
                $interest_ids = Interest::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        if(!empty($request->personality) || $request->has('personality')){ 
            $personality_ids = Personality::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        return [
            'email'                     =>  'nullable|email|max:150|unique:users,email,'.Auth::id(),
            'about_me'                  =>  'nullable|min:3|max:1000',
            'fav_movie'                 =>  'nullable|min:1|max:250',

            'personality'               =>  'nullable|in:'.implode(',', $personality_ids),
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

            // Add / Remove Interests
            'interests'                 =>  'nullable|array',
            'interests.*'               =>  'nullable|in:'.implode(',', $interest_ids),
            'remove_interests'          =>  'nullable|array',
            'remove_interests.*'        =>  'nullable|in:'.implode(',', $interest_ids),

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
