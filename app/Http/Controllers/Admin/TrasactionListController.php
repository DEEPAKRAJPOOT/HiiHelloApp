<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

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

        $from_date         = ($request->from_date) ? $request->from_date." 00:00:00" : "";
        $to_date           = ($request->to_date) ? $request->to_date." 23:59:59" : "";
        $search_status     = ($request->search_status) ? $request->search_status : "";


        $records = [];
        $transactions = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $transactions->where(function ($query) use ($search, $transactions) {
                $query->where('amount', 'like', "%{$search}%")
                    ->orWhere('payment_type', 'like', "%{$search}%")
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

        // ST - Filter
        if($from_date != "" && $to_date != "") {
            $transactions = $transactions->whereBetween('transactions.purchase_date', [$from_date, $to_date]);
        }

        if($request->search_status != '') {
            $transactions = $transactions->where('transactions.payment_type',$request->search_status);
        }

        $count = $transactions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $transactions = $transactions->where('transactions.status','success');
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
                'payment_type' => isset($transaction->payment_type) && !empty($transaction->payment_type) ? $transaction->payment_type : "N/A",
                'purchase_date' => isset($transaction->purchase_date) && !empty($transaction->purchase_date) ? date("d-m-Y",strtotime($transaction->purchase_date)) : "N/A",

                'subscription_end_date' => isset($transaction->subscription_end_date) && !empty($transaction->subscription_end_date) ? date("d-m-Y",strtotime($transaction->subscription_end_date)) : "N/A",

                'original_purchase_date' => isset($transaction->original_purchase_date) && !empty($transaction->original_purchase_date) ? date("d-m-Y",strtotime($transaction->original_purchase_date)) : "N/A",

                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscriptions', 'id' => $transaction->custom_id], $transaction)->render(),

            ];
        }
        return $records;
    }

    public function filters(Request $request)
    {
        $google_play = 'Google Play';
        $upi = 'UPI';
        $IOS = 'IOS';

        $total_google_play = Transaction::where("payment_type","LIKE","%{$google_play}%")->count();
        $total_upi = Transaction::where("payment_type","LIKE","%{$upi}%")->count();
        $total_ios = Transaction::where("payment_type","LIKE","%{$IOS}%")->count();

        $records['total_google_play'] = number_format($total_google_play);
        $records['total_upi'] = number_format($total_upi);
        $records['total_ios'] = number_format($total_ios);

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
                    'Razorpay Payment Id'              =>  $transaction->razorpay_payment_id ?? "",
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
                
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id', 'Name', 'Email', 'Plan Name', 'Months', 'Amount', 'Payment Type', 'Razorpay Order Id','Razorpay Paymentb Id','Transaction Id',
                'Original Transaction Id','Web Order Line Item Id','Purchase Date','Original Purchase Date','Subscription End DAte','Payment Date', 'Receipt Data',
                'In App Ownership Type','Subscription Group Identifier','Status','Created at'  
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Name'], $row['Email'], $row['Plan Name'], $row['Months'], $row['Amount'],
                    $row['Payment Type'], $row['Razorpay Order Id'], $row['Razorpay Payment Id'], $row['Razorpay Signature'], $row['Transaction Id'],
                    $row['Original Transaction Id'],$row['Subscription End DAte'], $row['Receipt Data'],$row['In App Ownership Type'],
                    $row['Subscription Group Identifier'], $row['Status'], $row['Created at']
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
        return redirect(route('admin.transaction-lists.index'));
    }
}
