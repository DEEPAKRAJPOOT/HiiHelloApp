<?php

namespace App\Http\Requests\Api\College;

use Illuminate\Foundation\Http\FormRequest;

class CollegeListRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            'search'        =>  'nullable|max:150',
            'city'          =>  'nullable|max:150',
            'state'         =>  'nullable|max:150',
            'limit'         =>  'nullable|numeric|min:5',
            'offset'        =>  'nullable|numeric|min:0',
        ];
    }
}