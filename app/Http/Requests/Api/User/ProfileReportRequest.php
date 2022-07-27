<?php

namespace App\Http\Requests\api\user;

use Illuminate\Foundation\Http\FormRequest;

class ProfileReportRequest extends FormRequest
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
        return  [
            'reported_user'     =>  'required|max:100',
            'message'           =>  'required|min:3|max:400',
            'image'             =>  'nullable|mimes:jpg,jpeg,png',
        ];
    }
}
