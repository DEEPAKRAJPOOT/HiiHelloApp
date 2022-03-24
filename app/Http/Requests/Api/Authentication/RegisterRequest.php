<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country;
use App\Models\Location;
use App\Models\Interest;
use App\Models\Language;

class RegisterRequest extends FormRequest
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
        $location_ids   =   Location::whereIsActive('y')->pluck('custom_id')->toArray();
        $language_ids   =   Language::whereIsActive('y')->pluck('lang_code')->toArray();
        $min_birth_date =   now()->subYears(config('utility.minimum_age'))->format('m/d/Y');
        $phone_codes    =   Country::whereIsActive('y')->pluck('phonecode')->toArray();
        
        // $interest_ids   =   Interest::whereIsActive('y')->pluck('custom_id')->toArray();

        return [
            'full_name'         =>  'required|min:2|max:100',
            'birth_date'        =>  'required|date|before:'.$min_birth_date,
            'gender'            =>  'required|in:Male,Female',
            'interest'          =>  'required|in:Male,Female,Both',
            'location'          =>  'required|in:'.implode(',', $location_ids),
            'language'          =>  'required|in:'.implode(',', $language_ids),
            'profile_photo'     =>  'required|mimes:jpg,jpeg,png',
                     
            // 'first_name'        =>  'required|min:2|max:100',
            // 'last_name'         =>  'required|min:2|max:100',
            // 'email'             =>  'nullable|email|max:150',
            'country_code'      =>  'nullable|in:'.implode(',', $phone_codes),
            'contact_no'        =>  'nullable|digits_between:6,16',
            // 'interests'         =>  'required|array',
            // 'interests.*'       =>  'required|in:'.implode(',', $interest_ids),
            // 'images'            =>  'required|array|max:4',
            // 'images.*'          =>  'required|mimes:jpg,jpeg,png',
            // 'videos'            =>  'nullable|array|max:1',
            // 'videos.*'          =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',
        ];
    }
}
