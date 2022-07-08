<?php

namespace App\Http\Requests\Api\Discovery;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Location;
use App\Models\Language;

class SetDiscoveryRequest extends FormRequest
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
        $location_ids   =   Location::select('custom_id')->whereIsActive('y')->pluck('custom_id')->toArray();
        $language_ids   =   Language::whereIsActive('y')->pluck('lang_code')->toArray();

        return [
            'distance'          =>  'required|numeric|min:1|max:500',
            'start_age'         =>  'required|numeric|min:1|max:100',
            'end_age'           =>  'required_with:start_age|numeric|min:1|max:100',
            'interest'          =>  'required|in:Male,Female,Both',
            'location'          =>  'required|in:'.implode(',', $location_ids),
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'required|in:'.implode(',', $language_ids),
        ];
    }
}
