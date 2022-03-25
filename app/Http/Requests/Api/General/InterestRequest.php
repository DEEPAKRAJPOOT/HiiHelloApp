<?php

namespace App\Http\Requests\Api\General;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest;
use App\Models\Location;

class InterestRequest extends FormRequest
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
        $parent_ids = Interest::select('custom_id')->whereNull('parent_id')->whereIsActive('y')->pluck('custom_id')->toArray();
        $location_ids = Location::select('custom_id')->whereIsActive('y')->pluck('custom_id')->toArray();

        return [
            'limit'         =>  'nullable|numeric|min:5',
            'offset'        =>  'nullable|numeric|min:0',
            'parent_id'     =>  'nullable|in:'.implode(',', $parent_ids),
            'location_id'   =>  'required|in:'.implode(',', $location_ids),
        ];
    }
}
