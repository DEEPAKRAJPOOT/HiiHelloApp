<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;

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

        return [
            'first_name'        =>  'required|min:2|max:100',
            'last_name'         =>  'required|min:2|max:100',
            // 'full_name'         =>  'required|min:4|max:100',
            'email'             =>  'nullable|email|max:150',
            'birth_date'        =>  'required|date|before:'.$min_birth_date,
            'gender'            =>  'required|in:Male,Female',
            'interest'          =>  'required|in:Male,Female,Both',
            // 'location'          =>  'required|max:100',
            'language'          =>  'required|max:100',
            'profile_photo'     =>  'required|mimes:jpg,jpeg,png',
            'country_code'      =>  'nullable|max:100',
            'contact_no'        =>  'nullable|digits_between:6,16',
            'latitude'          =>  'required|max:250',
            'longitude'         =>  'required|max:250',
            'otp_less_id'       => 'nullable|min:6'
        ];
    }
}
