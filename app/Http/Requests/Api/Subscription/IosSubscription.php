<?php

namespace App\Http\Requests\Api\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SubscriptionPlan;

class IosSubscription extends FormRequest
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
        $plan_ids = SubscriptionPlan::whereIsActive('y')->pluck('custom_id')->toArray();

        return [
            'plan_id'                   =>  'required|in:'.implode(',', $plan_ids),
            'receipt_data'              =>  'required',
            'transaction_id'            =>  'required',
            'original_transaction_id'   =>  'nullable',
        ];
    }
}
