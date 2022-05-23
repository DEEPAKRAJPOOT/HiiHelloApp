<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;

class VerifyContactRequest extends FormRequest
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
        $phone_codes    =   Country::whereIsActive('y')->pluck('phonecode')->toArray();

        return  [
            'country_code'      =>  'required|in:'.implode(',', $phone_codes),
            'contact_no'        =>  'required|numeric|digits_between:6,16|unique:users,contact_no,'.Auth::id(),
        ];
    }
}
