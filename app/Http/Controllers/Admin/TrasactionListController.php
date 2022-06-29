<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TrasactionListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.pages.transaction-lists.index')->with(['custom_title' => 'Transaction List']);
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
    public function show(Transaction $transaction_list)
    {
        // dd($tran);
        return view('admin.pages.transaction-lists.view', ["tran" => $transaction_list])->with(['custom_title' => 'Trasaction']);
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
        $TransactionLists = Transaction::with(['subscriptionPlan', 'user', 'user.userTranslations', 'subscriptionPlan.subscriptionPlanTranslations'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $TransactionLists->where(function ($query) use ($search, $TransactionLists) {
                $query->where('amount', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('razorpay_order_id', 'like', "%{$search}%")
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

        $count = $TransactionLists->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];
        $TransactionLists = $TransactionLists->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $TransactionLists = $TransactionLists->get();

        foreach ($TransactionLists as $TransactionList) {
           
            if ($TransactionList->user) {
                $user_id = $TransactionList->user->userTransDefault ? $TransactionList->user->userTransDefault->full_name : "";
                $account_id = $TransactionList->user->account_id ?? "";
            } else {
                $user_id = "";
                $account_id = "";
            }

            if ($TransactionList->subscriptionPlan) {
                $plan_id = $TransactionList->subscriptionPlan->subscriptionPlanTranslation ? $TransactionList->subscriptionPlan->subscriptionPlanTranslation->name : "N/A";
            } else {
                $plan_id = "";
            }
            $records['data'][] = [
                'id' => $TransactionList->id,
                'account_id' => $account_id,
                'user_id' =>  $user_id,
                'plan_id' => $plan_id,
                'razorpay_order_id' => $TransactionList->razorpay_order_id,
                'amount' => $TransactionList->amount,
                'status' => $TransactionList->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscription Lists', 'id' => $TransactionList->custom_id], $TransactionList)->render(),

            ];
        }
        return $records;
    }
}
