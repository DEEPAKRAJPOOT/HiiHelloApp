<?php

namespace App\Http\Controllers\api\v1;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CouponResource;
use App\Http\Requests\Api\Coupon\ValidateCouponRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CouponController extends Controller
{
    private $version = "v.1.0";

    public function getVersion()
    {
        return $this->version;
    }
    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }

    public function getCoupons(Request $request)
    {
        try {
            $coupons = Coupon::where('is_self_hosted', 'y')
                ->where('is_active', "y")
                ->with('plan');
            $count = $coupons->count();
            $limit = $request->limit ?? config('utility.pagination.limit');
            $offset = $request->offset ?? config('utility.pagination.offset');
            $coupons = $coupons->limit($limit)
                ->offset($offset)
                ->get();
            // dd($coupons);
            return CouponResource::collection($coupons)
                ->additional([
                    'meta' => [
                        'limit'     =>  $limit,
                        'offset'    =>  $offset,
                        'total'     =>  $count,
                        'is_ban'    =>  false
                    ]
                ]);
        } catch (\Exception $e) {
            $this->response['meta']['message'] = trans('api.went_wrong');
            $this->response['meta']['is_ban'] = false;
            $this->storeErrorLog($e, 'get_coupons');
        }
        return $this->returnResponse();
    }

    public function validateCoupon(Request $request)
    {
        $validate_coupon = new ValidateCouponRequest();
        if ($this->apiValidator($request->all(), $validate_coupon->rules())) {
            try {
                $user   = $request->user();
                $current_date = Carbon::now()->format('Y-m-d');
                $is_subscribed = Subscription::where('user_id', $user->id)
                    ->where('end_date', '>', $current_date)
                    ->first();

                if ($is_subscribed) {
                    $this->response['meta']['message']  =   trans('api.coupon.already_subscribed');
                    return $this->returnResponse();
                }

                $coupon = Coupon::where('coupon', $request->coupon)
                    ->where('is_active', 'y')
                    ->first();

                $this->response['meta']['is_ban'] = false;
                $this->status = Response::HTTP_NOT_FOUND;

                if (!$coupon) {
                    $this->response['meta']['message']  =   trans('api.coupon.invalid');
                    return $this->returnResponse();
                }



                $is_expired = Carbon::parse($coupon->expired_at)->isPast();
                if ($is_expired) {
                    $this->response['meta']['message']  =   trans('api.coupon.expired');
                    return $this->returnResponse();
                }

                $is_used = Transaction::where('coupon_id', $coupon->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'success')
                    ->first();

                if ($is_used && $coupon->is_reusable === 'n') {
                    $this->response['meta']['message']  =   trans('api.coupon.used');
                    return $this->returnResponse();
                }
                if ($coupon->plan && $coupon->type === "full") {
                    $plan = $coupon->plan;
                    $new_sub_start_date = \Carbon\Carbon::now()->format('Y-m-d');

                    $new_sub_end_date = !empty($user->subscription_end_date)
                        ? \Carbon\Carbon::parse($user->subscription_end_date)->addDays($plan->day)->format('Y-m-d')
                        : \Carbon\Carbon::now()->addDays($plan->day)->format('Y-m-d');

                    $date_format = 'Y-m-d H:i:s';
                    $new_subscription_start_date = \Carbon\Carbon::now()->format($date_format);
                    if ($user->subscription_end_date >= $new_subscription_start_date) {
                        $new_subscription_start_date = $user->subscription_end_date;
                    }

                    $subscription_end_date = !empty($user->subscription_end_date)
                        ? \Carbon\Carbon::parse($new_subscription_start_date)->addDays($plan->day)->format($date_format)
                        : \Carbon\Carbon::now()->addDays($plan->day)->format($date_format);

                    DB::beginTransaction();

                    $transaction_id = generateTransactionId();
                    $subscription =  Subscription::firstOrCreate([
                        'user_id'       =>  $user->id ?? null,
                        'custom_id'     =>  getUniqueString('subscriptions'),
                        'coupon_id'     =>  $coupon->id,
                        'coupon_name'     =>  $coupon->coupon,
                        'plan_id'       =>  $plan->id ?? null,
                        'months'        =>  $plan->months,
                        'original_transaction_id'        =>  $transaction_id,
                        'day'           =>  $plan->day,
                        'amount'        =>  $plan->amount,
                        'start_date'    =>  $new_sub_start_date,
                        'end_date'      =>  $new_sub_end_date,
                        'payment_date'  =>  null,

                        'payment_type'  =>   'COUPON',

                        'status'        =>  'active',
                    ]);
                    $user->is_subscribed = 'y';
                    $user->subscription_end_date = $subscription_end_date;
                    $user->save();

                    Transaction::firstOrCreate([
                        'custom_id'             =>  getUniqueString('transactions'),
                        'user_id'               =>  $user->id ?? null,
                        'plan_id'               =>  $plan->id ?? null,
                        'subscription_id'       =>  $subscription ? $subscription->id : null,
                        'coupon_id'     =>  $coupon->id,
                        'coupon_name'     =>  $coupon->coupon,
                        'coupon_type'     =>  $coupon->type,
                        'payment_type'          =>  'COUPON',

                        'razorpay_order_id'     =>  $transaction_id,
                        'amount'                =>  $plan->amount,
                        'purchase_date'         =>  $new_subscription_start_date,
                        'original_purchase_date' =>  $new_subscription_start_date,
                        'subscription_end_date' =>  $subscription_end_date,
                        'in_app_ownership_type' =>  'PURCHASED',
                        'status'                =>  'success',
                    ]);
                    DB::commit();
                }


                return (new CouponResource($coupon))
                    ->additional([
                        'meta' => [
                            'is_ban'    =>  false,
                            'is_subscribed' => $user->is_subscribed === 'y',
                            'subscription_end_date' => $user->subscription_end_date
                        ]
                    ]);
            } catch (ModelNotFoundException $exception) {
                DB::rollback();
                switch ($exception->getModel()) {
                    case 'App\Models\Coupon':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Coupon")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();
                $this->storeErrorLog($e, 'validate_coupon');
            }
        }
        $this->status = Response::HTTP_OK;
        return $this->returnResponse();
    }
}
