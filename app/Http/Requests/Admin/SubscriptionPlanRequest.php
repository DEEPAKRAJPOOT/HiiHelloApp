<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPlanRequest extends FormRequest
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
            $default_lang.'_name'           =>  'required_unless:action,'.$unless.'|max:150',
            $default_lang.'_description'    =>  'nullable|max:150',
            $default_lang.'_note'           =>  'nullable|max:150',
            'months'                        =>  'required_unless:action,'.$unless.'|numeric',
            'amount'                        =>  'required_unless:action,'.$unless.'|numeric',
            'android_product'               =>  'required_unless:action,'.$unless,
            'ios_product'                   =>  'required_unless:action,'.$unless,
            'is_popular'                    =>  'required_unless:action,'.$unless,
        ];
    }
}
