<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class StateRequest extends FormRequest
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
        $id = (!empty(Route::current()->parameters()['user']->id) ? ','.Route::current()->parameters()['user']->id : '');
        return [
            $default_lang.'_name'   =>  'required_unless:action,'.$unless.'|max:50',
            // 'name'         =>  'required_unless:action,'.$unless.'|max:50',
            'country_id'         =>  'required_unless:action,'.$unless,
        ];
    }
}
