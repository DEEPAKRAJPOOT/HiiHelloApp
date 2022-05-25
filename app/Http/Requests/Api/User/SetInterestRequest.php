<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest;

class SetInterestRequest extends FormRequest
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
        
        /* Add & Remove Interests */
        return [
            'interests'                 =>  'required_without:remove_interests|array',
            'interests.*'               =>  'required_without:remove_interests|in:'.implode(',', $interest_ids),
            'remove_interests'          =>  'required_without:interests|array',
            'remove_interests.*'        =>  'required_without:interests|in:'.implode(',', $interest_ids),
        ];
    }
}
