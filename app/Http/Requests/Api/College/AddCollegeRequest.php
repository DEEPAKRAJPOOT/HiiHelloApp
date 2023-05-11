<?php

namespace App\Http\Requests\Api\College;

use Illuminate\Foundation\Http\FormRequest;

class AddCollegeRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            'name'         =>  'required|min:3|max:150',
            'university'   =>  'required|min:3|max:150',
            'district'     =>  'required|min:3|max:150',
            'state'        =>  'required|min:3|max:150',
            'abbreviation' =>  'nullable|min:2|max:150'
        ];
    }
}