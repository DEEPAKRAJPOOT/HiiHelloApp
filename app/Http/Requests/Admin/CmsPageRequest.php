<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CmsPageRequest extends FormRequest
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
        $unless = "change_status";
        $default_lang = config('utility.default_lang_code');

        return [
            $default_lang.'_title'          =>  'required_unless:action,'.$unless.'|max:500',
            $default_lang.'_description'    =>  'required_unless:action,'.$unless,
        ];
    }
}
