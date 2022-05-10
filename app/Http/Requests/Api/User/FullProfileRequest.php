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
            !empty($request->hobby) || $request->has('hobby') ||
            !empty($request->fav_game) || $request->has('fav_game') ||
            !empty($request->fav_sport) || $request->has('fav_sport')) { 
                $interest_ids = Interest::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        if(!empty($request->personality) || $request->has('personality')){ 
            $personality_ids = Personality::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        return [
            'email'                     =>  'nullable|email|max:150|unique:users,email,'.Auth::id(),
            'relationship_status'       =>  'nullable|in:'.implode(',', $profile_details),
            'you_are_here'              =>  'nullable|in:'.implode(',', $profile_details),
            'food_preference'           =>  'nullable|in:'.implode(',', $profile_details),
            'drinking'                  =>  'nullable|in:'.implode(',', $profile_details),
            'smoking'                   =>  'nullable|in:'.implode(',', $profile_details),
            'star_sign'                 =>  'nullable|in:'.implode(',', $profile_details),
            'religion'                  =>  'nullable|in:'.implode(',', $profile_details),
            'community'                 =>  'nullable|in:'.implode(',', $profile_details),
            'education'                 =>  'nullable|in:'.implode(',', $profile_details),
            'occupation'                =>  'nullable|in:'.implode(',', $profile_details),
            'date_idea'                 =>  'nullable|in:'.implode(',', $profile_details),
            'social_cause'              =>  'nullable|in:'.implode(',', $profile_details),
            'risk_taken'                =>  'nullable|in:'.implode(',', $profile_details),
            'perfect_relation_things'   =>  'nullable|in:'.implode(',', $profile_details),
            'my_mantra'                 =>  'nullable|in:'.implode(',', $profile_details),
            'one_thing_know'            =>  'nullable|in:'.implode(',', $profile_details),
            'worst_date'                =>  'nullable|in:'.implode(',', $profile_details),
            'introduce_to_family'       =>  'nullable|in:'.implode(',', $profile_details),
            'found_the_one'             =>  'nullable|in:'.implode(',', $profile_details),
            'about_me_surprises'        =>  'nullable|in:'.implode(',', $profile_details),
            'political_views'           =>  'nullable|in:'.implode(',', $profile_details),

            'personality'               =>  'nullable|in:'.implode(',', $personality_ids),
            'university'                =>  'nullable|in:'.implode(',', $profile_details),
            'profession'                =>  'nullable|in:'.implode(',', $profile_details),
            'hobby'                     =>  'nullable|in:'.implode(',', $interest_ids), 
            'fav_game'                  =>  'nullable|in:'.implode(',', $interest_ids),
            'fav_sport'                 =>  'nullable|in:'.implode(',', $interest_ids),

            'interests'                 =>  'nullable|array',
            'interests.*.*'             =>  'nullable|in:'.implode(',', $interest_ids),
            'fav_festivals'             =>  'nullable|array',
            'fav_festivals.*'           =>  'nullable|in:'.implode(',', $profile_details),
            'pets'                      =>  'nullable|array',
            'pets.*'                    =>  'nullable|in:'.implode(',', $profile_details),
            'images'                    =>  'nullable|array|max:4',
            'images.*'                  =>  'nullable|mimes:jpg,jpeg,png',
            'videos'                    =>  'nullable|array|max:1',
            'videos.*'                  =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',

            'about_me'                  =>  'nullable|min:3',
            'voices'                    =>  'nullable|array|max:1',
            'voices.*'                  =>  'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac,m4a',
        ];
    }
}
