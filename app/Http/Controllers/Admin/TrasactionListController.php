<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Testing\File;

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
    public function csvDownload(Request $request)
    {
        $down_file_name = 'Transactions';
        $transactions = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->get();
        if (!$transactions->isEmpty()) {
            foreach ($transactions as $transaction) {
                $data[] = [
                    'Account Id'                =>  $transaction->user ? ($transaction->user->account_id ?? "") :  "",
                    'Name'                      =>  $transaction->user ? ($transaction->user->userTransDefault ? $transaction->user->userTransDefault->full_name : "") : "",
                    'Email'                     =>  $transaction->email ?? "--",
                    'Plan Name'                 =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan->subscriptionPlanTranslation ? $transaction->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                    'Months'                    =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan ? $transaction->subscriptionPlan->months : "N/A") : "",
                    'Amount'                    =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan ? $transaction->subscriptionPlan->amount : "N/A") : "",                    
                    'Payment Type'              =>  $transaction->payment_type ?? "",
                    'Razorpay Order Id'         =>  $transaction->razorpay_order_id ?? "",
                    'Razorpay Paymentb Id'              =>  $transaction->razorpay_payment_id ?? "",
                    'Razorpay Signature'              =>  $transaction->razorpay_signature ?? "",
                    'Transaction Id'              =>  $transaction->transaction_id ?? "",
                    'Original Transaction Id'              =>  $transaction->original_transaction_id ?? "",
                    'Web Order Line Item Id'              =>  $transaction->web_order_line_item_id ?? "",
                    'Purchase Date'              =>  $transaction->purchase_date ?? "",
                    'Original Purchase Date'              =>  $transaction->original_purchase_date ?? "",
                    'Subscription End DAte'              =>  $transaction->subscription_end_date ?? "",
                    'Receipt Data'              =>  $transaction->receipt_data ?? "",
                    'In App Ownership Type'              =>  $transaction->in_app_ownership_type ?? "",
                    'Subscription Group Identifier'              =>  $transaction->subscription_group_identifier ?? "",
                    'Status'                    =>  $transaction->status ?? "",
                    'Created at'                =>  $transaction->created_at ? Carbon::parse($transaction->created_at)->format('Y-m-d') : ""
                ];
                dd($data);
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id', 'Name', 'Email', 'Subscription Plan Name', 'Months', 'Amount', 'Start date', 'End date', 'Payment Type', 'Payment Date', 'Receipt Data', 'Original Transaction Id', 'Status', 'Created at'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Name'], $row['Email'], $row['Subscription Plan Name'], $row['Months'], $row['Amount'], $row['Start date'], $row['End date'], $row['Payment Type'], $row['Payment Date'], $row['Receipt Data'], $row['Original Transaction Id'], $row['Status'], $row['Created at'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate transaction csv file. Try again later')->error();
        }
        return redirect(route('admin.subscription-lists.index'));
    }
}
