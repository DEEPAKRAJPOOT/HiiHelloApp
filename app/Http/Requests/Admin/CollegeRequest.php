<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollegeRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules()
    {
        $unless = 'change_status';
        return [
            'college_name'    =>  'required_unless:action,'.$unless.'|max:150',
            'university_name' =>  'required_unless:action,'.$unless.'|max:150',
            'district_name'   =>  'required_unless:action,'.$unless.'|max:150',
            'state_name'      =>  'required_unless:action,'.$unless.'|max:150',
        ];
    }
}
