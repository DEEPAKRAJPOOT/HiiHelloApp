<?php

namespace App\Http\Requests\Api\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class RestoreSubscription extends FormRequest
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
        return [
            'plan_id'   =>  'required|max:100',
        ];
    }
}
