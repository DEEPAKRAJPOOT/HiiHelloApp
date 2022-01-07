<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $phone_codes = Country::whereIsActive('y')->pluck('phonecode')->toArray();
        $country_ids = Country::whereIsActive('y')->pluck('custom_id')->toArray();
        
        return [
            'first_name'        =>  'required|min:2|max:100',
            'last_name'         =>  'required|min:2|max:100',
            'email'             =>  'nullable|email|max:150',
            'country_code'      =>  'required|in:'.implode(',', $phone_codes),
            'country'           =>  'nullable|in:'.implode(',', $country_ids),
            'contact_no'        =>  'required|digits_between:6,16',
            'birth_date'        =>  'required|date|before:tomorrow',
            'gender'            =>  'required|in:'.implode(',', ['Male','Female']),
            'interest'          =>  'required|in:'.implode(',', ['Male','Female', 'Both']),
            'profile_photo'     =>  'required|mimes:jpg,jpeg,png',
            'images'            =>  'required|array|max:4',
            'images.*'          =>  'required|mimes:jpg,jpeg,png',
            'videos'            =>  'nullable|array|max:1',
            'videos.*'          =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',
        ];

    }
}
