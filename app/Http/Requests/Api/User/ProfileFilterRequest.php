<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest;
use App\Models\Location;
use App\Models\Language;

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
    public function rules()
    {
        $interest_ids = Interest::whereIsActive('y')->pluck('custom_id')->toArray();
        $location_ids = Location::whereIsActive('y')->pluck('custom_id')->toArray();
        $language_ids = Language::whereIsActive('y')->pluck('lang_code')->toArray();

        return [
            'limit'             =>  'nullable|numeric',
            'offset'            =>  'nullable|numeric',
            'start_age'         =>  'nullable|numeric',
            'end_age'           =>  'required_with:start_age|numeric',
            'gender'            =>  'nullable|in:Male,Female',
            'interests'         =>  'nullable|array',
            'interests.*'       =>  'nullable|in:'.implode(',', $interest_ids),
            'location'          =>  'nullable|in:'.implode(',', $location_ids),
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'nullable|in:'.implode(',', $language_ids),
        ];
    }
}
