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
        return view('admin.pages.transaction-lists.index')->with(['custom_title' => 'Transactions']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($custom_id)
    {
        $transaction = Transaction::with(['user','user.userTransDefault','subscriptionPlan.subscriptionPlanTransDefault'])
                    ->whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.transaction-lists.view', ["tran" => $transaction])->with(['custom_title' => 'Trasaction']);
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $transactions = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $transactions->where(function ($query) use ($search, $transactions) {
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

        $count = $transactions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $transactions = $transactions->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $transactions = $transactions->get();

        foreach ($transactions as $transaction) {
            $records['data'][] = [
                'id' => $transaction->id,
                'account_id' =>  $transaction->user ? ($transaction->user->account_id ?? "") :  "",
                'user_id' =>  $transaction->user ? ($transaction->user->userTransDefault ? $transaction->user->userTransDefault->full_name : "") : "",
                'plan_id' => $transaction->subscriptionPlan ? ($transaction->subscriptionPlan->subscriptionPlanTranslation ? $transaction->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'razorpay_order_id' => $transaction->razorpay_order_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscriptions', 'id' => $transaction->custom_id], $transaction)->render(),

            ];
        }
        return $records;
    }
}
