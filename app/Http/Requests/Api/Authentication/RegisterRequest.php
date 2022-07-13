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
        $min_birth_date =   now()->subYears(config('utility.minimum_age'))->format('m/d/Y');
        $location_ids   =   Location::whereIsActive('y')->pluck('custom_id')->toArray();
        $language_ids   =   Language::whereIsActive('y')->pluck('lang_code')->toArray();
        $phone_codes    =   Country::whereIsActive('y')->pluck('phonecode')->toArray();

        return [
            'first_name'        =>  'required|min:2|max:100',
            'last_name'         =>  'required|min:2|max:100',
            // 'full_name'         =>  'required|min:4|max:100',
            'email'             =>  'nullable|email|max:150',
            'birth_date'        =>  'required|date|before:'.$min_birth_date,
            'gender'            =>  'required|in:Male,Female',
            'interest'          =>  'required|in:Male,Female,Both',
            'location'          =>  'required|in:'.implode(',', $location_ids),
            'language'          =>  'required|in:'.implode(',', $language_ids),
            'profile_photo'     =>  'required|mimes:jpg,jpeg,png',
            'country_code'      =>  'nullable|in:'.implode(',', $phone_codes),
            'contact_no'        =>  'nullable|digits_between:6,16',
        ];
    }
}
