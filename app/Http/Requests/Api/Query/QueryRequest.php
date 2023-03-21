<?php

namespace App\Http\Requests\Api\Query;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class QueryRequest extends FormRequest
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
        return [
            'full_name' => 'nullable',
            'contact_no' => 'required|min:2|max:16',
            'email' => 'required|email',
            'help_option' => 'required',
            'description' => 'required',
            'file' => 'nullable'
        ];
    }
}
