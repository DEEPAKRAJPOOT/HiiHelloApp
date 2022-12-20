<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use App\Models\Language;
use Illuminate\Support\Facades\Response;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.subscription-plans.index')->with(['custom_title' => 'Subscription Plans']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.subscription-plans.create', compact('languages'))->with(['custom_title' => 'Subscription Plan', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionPlanRequest $request)
    {
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('subscription_plans');
        $data['months'] = $request->months;
        $data['day']    = $request->day;
        $data['amount'] = $request->amount;
        $data['android_product'] = $request->android_product;
        $data['ios_product'] = $request->ios_product;
        $data['is_popular'] = $request->is_popular;

        $subscriptionPlan = SubscriptionPlan::create($data);
        $subscriptionPlan->is_popular = $request->is_popular;

        if ($subscriptionPlan->save()) {
            flash('Subscription Plan created successfully!')->success();
        } else {
            flash('Unable to save subscription plan. Please try again later.')->error();
        }
        return redirect(route('admin.subscription-plans.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.subscription-plans.edit', compact('subscriptionPlan', 'languages'))->with(['custom_title' => 'Subscription Plan', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            if ($subscriptionPlan) {
                $subscriptionPlan->is_active = $request->value;
                if ($subscriptionPlan->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $data['months'] = $request->months;
            $data['day']    = $request->day;
            $data['amount'] = $request->amount;
            $data['android_product'] = $request->android_product;
            $data['ios_product'] = $request->ios_product;
            $data['is_popular'] = $request->is_popular;

            $subscriptionPlan->update($data);
            $subscriptionPlan->is_popular = $request->is_popular;

            if ($subscriptionPlan->save()) {
                flash('Subscription Plan details updated successfully!')->success();
            } else {
                flash('Unable to subscription plan. Try again later')->error();
            }
            return redirect(route('admin.subscription-plans.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $subscriptionPlans = SubscriptionPlan::whereIn('custom_id', explode(',', $request->ids))->get();
            foreach ($subscriptionPlans as $subscriptionPlan) {
                $subscriptionPlan->subscriptionPlanTranslations()->delete();
                $subscriptionPlan->delete();
            }
            $content['status'] = 200;
            $content['message'] = "subscription plan deleted successfully.";
            $content['count'] = SubscriptionPlan::all()->count();
            return response()->json($content);
        } else {
            $subscriptionPlan = SubscriptionPlan::where('custom_id', $id)->firstOrFail();
            $subscriptionPlan->subscriptionPlanTranslations()->delete();
            $subscriptionPlan->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "subscription plan deleted successfully.", 'count' => SubscriptionPlan::all()->count());
                return response()->json($content);
            } else {
                flash('subscription plan deleted successfully.')->success();
                return redirect()->route('admin.subscription-plans.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $subscriptionPlans = SubscriptionPlan::with(['subscriptionPlanTransDefault'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $subscriptionPlans->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhere('months', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('is_popular', 'like', "%{$search}%")
                    ->orWhereHas('subscriptionPlanTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('note', 'like', "%{$search}%");
                    });
            });
        }

        $count = $subscriptionPlans->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $subscriptionPlans = $subscriptionPlans->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $subscriptionPlans = $subscriptionPlans->get();

        foreach ($subscriptionPlans as $subscriptionPlan) {

            $params = [
                'checked' => ($subscriptionPlan->is_active == 'y' ? 'checked' : ''),
                'getaction' => $subscriptionPlan->is_active,
                'class' => '',
                'id' => $subscriptionPlan->custom_id,
            ];

            $records['data'][] = [
                'id' => $subscriptionPlan->id,
                'name' => $subscriptionPlan->subscriptionPlanTransDefault ? $subscriptionPlan->subscriptionPlanTransDefault->name : "",
                'amount' => $subscriptionPlan->amount,
                'is_popular' => $subscriptionPlan->is_popular,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscription Plans', 'id' => $subscriptionPlan->custom_id], $subscriptionPlan)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $subscriptionPlan->custom_id)->render(),
            ];
        }
        return $records;
    }
}
