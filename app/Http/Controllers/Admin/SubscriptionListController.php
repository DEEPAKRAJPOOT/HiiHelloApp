<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.subscription-list.index')->with(['custom_title' => 'Subscription List']);

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription_list)
    {
        return view('admin.pages.subscription-list.view', ["sub" => $subscription_list])->with(['custom_title' => 'Subscription']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $subscriptionPlans = Subscription::with(['subscriptionPlan', 'user', 'user.userTranslations', 'subscriptionPlan.subscriptionPlanTranslations'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $subscriptionPlans->where(function ($query) use ($search) {
                $query->where('months', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('account_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.userTranslations', function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subscriptionPlan.subscriptionPlanTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
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
            $records['data'][] = [
                'id' => $subscriptionPlan->id,
                'account_id' => $subscriptionPlan->user ? ($subscriptionPlan->user->account_id ?? "") : "",
                'user_id' => $subscriptionPlan->user ? ($subscriptionPlan->user->userTransDefault ? $subscriptionPlan->user->userTransDefault->full_name : "N/A") : "",
                'plan_id' => $subscriptionPlan->subscriptionPlan ? ($subscriptionPlan->subscriptionPlan->subscriptionPlanTranslation ? $subscriptionPlan->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'months' => $subscriptionPlan->months,
                'amount' => $subscriptionPlan->amount,
                'status' => $subscriptionPlan->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscription Lists', 'id' => $subscriptionPlan->custom_id], $subscriptionPlan)->render(),

            ];
        }
        return $records;
    }
}
