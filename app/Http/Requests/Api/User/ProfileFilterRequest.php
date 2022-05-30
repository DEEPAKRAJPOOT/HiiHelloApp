<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProfileDetail;
use App\Models\Personality;
use App\Models\Interest;

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
    public function rules($request)
    {
        $personality_ids = $interest_ids = array();
        $profile_details = ProfileDetail::whereIsActive('y')->pluck('slug')->toArray();

        if(!empty($request->personality) || $request->has('personality')){ 
            $personality_ids = Personality::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }
        if(!empty($request->interests) || $request->has('interests')){ 
            $interest_ids = Interest::whereIsActive('y')->pluck('custom_id')->toArray(); 
        }

        return [
            'limit'                     =>  'nullable|numeric',
            'offset'                    =>  'nullable|numeric',

            'relationship_status'       =>  'nullable|in:'.implode(',', $profile_details),
            'personality'               =>  'nullable|in:'.implode(',', $personality_ids),
            'star_sign'                 =>  'nullable|in:'.implode(',', $profile_details),
            'fav_movie'                 =>  'nullable|min:1|max:250',
            
            'interests'                 =>  'nullable|array',
            'interests.*'               =>  'nullable|in:'.implode(',', $interest_ids),
        ];
    }
}
