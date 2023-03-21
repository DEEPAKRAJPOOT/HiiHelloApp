<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
        return [
            'plan_id'       =>  'nullable|exists:subscription_plans,id',
            'vendor_id'     =>  'nullable|exists:coupon_vendors,id',
            'title'         =>  'required_unless:action,'.$unless.'|max:150',
            'coupon'        =>  'required_unless:action,'.$unless.'|max:150',
            'expired_at'    =>  'required_unless:action,'.$unless.'|date_format:Y-m-d|after:yesterday',
            'description'   =>  'nullable|max:150',

            'type'          =>  [
                'required_unless:action,'.$unless,
                Rule::in(['percentage','full'])
            ],
            'value'         =>  [
                Rule::requiredIf((($this->action ?? '') != $unless) && $this->type == 'percentage')
            ],

            'is_universal'  =>  'required_unless:action,'.$unless,
            'is_reusable'   =>  'required_unless:action,'.$unless,
            'is_self_hosted'=>  'required_unless:action,'.$unless,
            'is_active'     =>  'required_unless:action,'.$unless,
        ];
    }
}
