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
        return view('admin.pages.transaction-lists.index')->with(['custom_title' => 'Transaction List']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($custom_id)
    {
        $transaction_list = Transaction::with(['user','user.userTransDefault','subscriptionPlan.subscriptionPlanTransDefault'])
                    ->whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.transaction-lists.view', ["tran" => $transaction_list])->with(['custom_title' => 'Trasaction']);
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $TransactionLists = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->orderBy($sort_column, $sort_order);

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

            $records['data'][] = [
                'id' => $TransactionList->id,
                'account_id' =>  $TransactionList->user ? ($TransactionList->user->account_id ?? "") :  "",
                'user_id' =>  $TransactionList->user ? ($TransactionList->user->userTransDefault ? $TransactionList->user->userTransDefault->full_name : "") : "",
                'plan_id' => $TransactionList->subscriptionPlan ? ($TransactionList->subscriptionPlan->subscriptionPlanTranslation ? $TransactionList->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'razorpay_order_id' => $TransactionList->razorpay_order_id,
                'amount' => $TransactionList->amount,
                'status' => $TransactionList->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscription Lists', 'id' => $TransactionList->custom_id], $TransactionList)->render(),

            ];
        }
        return $records;
    }
}
