<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{SubscriptionPlan,CouponVendor,Transaction};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\Payment\HDFCClass;
use Illuminate\Support\Facades\DB;
use Exception;

class TrasactionListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $SubscriptionPlans = SubscriptionPlan::all();
        $coupon_vendors = CouponVendor::orderBy('name','asc')->get();
        // echo "<pre>"; print_r($SubscriptionPlans->toArray()); exit();

        return view('admin.pages.transaction-lists.index')->with(['custom_title' => 'Transactions', 'subscription_plans' => $SubscriptionPlans,'coupon_vendors'=>$coupon_vendors]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($custom_id)
    {
        $transaction = Transaction::with(['user','user.userTransDefault','subscriptionPlan.subscriptionPlanTransDefault','couponVendor'])
                    ->whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.transaction-lists.view', ["tran" => $transaction])->with(['custom_title' => 'Trasaction']);
    }

    public function listing(Request $request)
    {
        DB::enableQueryLog();
        extract($this->DTFilters($request->all()));

        $from_date         = ($request->from_date) ? $request->from_date." 00:00:00" : "";
        $to_date           = ($request->to_date) ? $request->to_date." 23:59:59" : "";
        $search_mode       = ($request->search_mode) ? $request->search_mode : "";
        $search_status     = ($request->search_status) ? $request->search_status : "";
        $search_plan       = ($request->search_plan) ? $request->search_plan : "";
        $search_vendor     = ($request->search_vendor) ? $request->search_vendor : "";

        $records = [];
        $transactions = Transaction::with(['usersubscription','subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $transactions->where(function ($query) use ($search, $transactions) {
                $query->where('amount', 'like', "%{$search}%")
                    ->orWhere('payment_type', 'like', "%{$search}%")
                    ->orWhere('razorpay_order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('account_id', 'like', "%{$search}%");
                        $query->orWhere('email', 'like', "%{$search}%");
                        $query->orWhere('contact_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.userTranslations', function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.location.locationTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                        $query->orWhere('state', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subscriptionPlan.subscriptionPlanTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // ST - Filter
        if($from_date != "" && $to_date != "") {
            $transactions = $transactions->whereBetween('transactions.created_at', [$from_date, $to_date]);
        }

        if($search_status != '') {
            $transactions = $transactions->where('transactions.status',$search_status);
        }

        if($search_mode != '') {
            $transactions = $transactions->where('transactions.payment_type',$search_mode);
            if($search_mode == 'COUPON' && $request->search_vendor != '') {
                $transactions = $transactions->where('transactions.coupon_vendor_id',$request->search_vendor);
            }
        }

        if($request->search_plan != '') {
            $transactions = $transactions->where('transactions.plan_id',$request->search_plan);
        }

        $count = $transactions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $transactions = $transactions->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $transactions = $transactions->get();
        // dd(DB::getQueryLog());

        
        foreach ($transactions as $transaction) {
            $records['data'][] = [
                'id' => $transaction->id,
                'account_id' =>  $transaction->user ? ($transaction->user->account_id ?? "") :  "",
                'user_id' =>  $transaction->user ? ($transaction->user->userTransDefault ? $transaction->user->userTransDefault->full_name : "") : "",
                'plan_id' => $transaction->subscriptionPlan ? ($transaction->subscriptionPlan->subscriptionPlanTranslation ? $transaction->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'razorpay_order_id' => $transaction->usersubscription->order_id??"",
                'transaction_id'    => $transaction->transaction_id??"",
                'razorpay_vpa'      => $transaction->razorpay_vpa??"",
                'razorpay_contact'  => $transaction->razorpay_contact??"",
                'razorpay_email'  => $transaction->razorpay_email??"",
                'refund_id'       => $transaction->refund_id??"",
                'refunded_amount'       => $transaction->refunded_amount??"",
                'refunded_at'       => !empty($transaction->refunded_at) ? date('d-m-Y H:i:s',strtotime($transaction->refunded_at)) : 'N/A',
                'amount' => $transaction->amount,
                'status' => view('admin.layouts.includes.status-badge')->with('status',$transaction->status)->render(),
                'coupon_name' => $transaction->coupon_name,
                'payment_type' => isset($transaction->payment_type) && !empty($transaction->payment_type) ? $transaction->payment_type : "N/A",
                'purchase_date' => isset($transaction->usersubscription->start_date) && !empty($transaction->usersubscription->start_date) ? date("d-m-Y",strtotime($transaction->usersubscription->start_date)) : "N/A",
                'subscription_end_date' => isset($transaction->usersubscription->end_date) && !empty($transaction->usersubscription->end_date) ? date("d-m-Y",strtotime($transaction->usersubscription->end_date)) : "N/A",
                'created_at' => !empty($transaction->created_at) ? date('d-m-Y H:i:s',strtotime($transaction->created_at)) : 'N/A',
                'state' => !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->state ?? 'N/A') :  'N/A',
                'city' => !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->name ?? 'N/A') :  'N/A',
                'email' =>  $transaction->user ? ($transaction->user->email ?? 'N/A') :  'N/A',
                'phone' =>  $transaction->user ? ($transaction->user->contact_no ?? 'N/A') :  'N/A',
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscriptions', 'id' => $transaction->custom_id], $transaction)->render(),

            ];
        }
        //echo "test"; exit();
        return $records;
    }

    public function filters(Request $request)
    {
        $google_play = 'Google Play';
        $upi = 'UPI';
        $IOS = 'IOS';
        $coupon = 'COUPON';
        $razorPay = 'Razorpay';
        $cashfree = 'Cashfree';

        $total_google_play = Transaction::where("payment_type","LIKE","%{$google_play}%")->count();
        $total_upi = Transaction::where("payment_type","LIKE","%{$upi}%")->count();
        $total_ios = Transaction::where("payment_type","LIKE","%{$IOS}%")->count();
        $total_coupon = Transaction::where("payment_type","LIKE","%{$coupon}%")->count();
        $totalRazorPayment = Transaction::where("payment_type","LIKE","%{$razorPay}%")->where('status','success')->count();
        DB::enableQueryLog();
        $totalCashfreePayment = Transaction::where("payment_type","LIKE","%{$cashfree}%")->where('status','success')->count();
        $SubscriptionPlans = SubscriptionPlan::all();

        foreach($SubscriptionPlans as $val){
            $count = Transaction::where("plan_id","=",$val->id)
                                    ->where('status', '=', 'success') 
                                    ->whereNull('deleted_at')
                                    ->count();

            $records['plan'][$val->id] = number_format($count);
        }

        $records['total_google_play'] = number_format($total_google_play);
        $records['total_upi'] = number_format($total_upi);
        $records['total_ios'] = number_format($total_ios);
        $records['total_razorpay'] = number_format($totalRazorPayment);
        $records['total_cashfree'] = number_format($totalCashfreePayment);
        $records['total_coupon'] = number_format($total_coupon);

        return $records;

    }

    public function refundTransaction(Request $request){
        try{
            $transaction = Transaction::whereCustomId($request->custom_transaction_id)->wherePaymentType('UPI')->whereStatus('success')->firstOrFail();
            DB::beginTransaction();
            $transaction->refunded_at = now();
            $transaction->refunded_amount = floatval($transaction->refunded_amount ?? 0) + $request->amount_to_refund;
            $transaction->save();
            $hdfc = new HDFCClass();
            $trans_status = $hdfc->getTransactionStatus([
                'transaction_id' => $transaction->razorpay_order_id
            ]);
            $refundData = $trans_status['mapped_response'];
            $refundData['amount'] = $request->amount_to_refund;
            $refund_request = $hdfc->createRefundRequest($refundData);
            if(!empty($refund_request['mapped_response']['status']) && $refund_request['mapped_response']['status'] == 'success'){
                flash('Refund initiated successfully')->success();
                DB::commit();
            }else{
                flash('Unable to initiate refund. Error: '.$refund_request['mapped_response']['status_description'])->error();
                DB::rollback();
            }
            return redirect()->route('admin.transaction-lists.show',$request->custom_transaction_id);
        }catch(ModelNotFoundException $e){
            flash('Trasaction not found or not refundable.')->error();
        }catch(Exception $e){
            $this->customLogger([
                'file'=>$e->getFile(),
                'line'=>$e->getLine(),
                'message'=>$e->getMessage(),
            ], 'hdfc_refund');
            flash('Some Unknown error occured. Try again later')->error();
        }
        return redirect()->route('admin.transaction-lists.index');
    }

    public function csvDownload(Request $request)
    {
        $from_date         = ($request->from_date) ? $request->from_date : '';
        $to_date           = ($request->to_date) ? now()->create($request->to_date)->addDay()->format('Y-m-d') : '';
        $search_status     = ($request->search_status) ? $request->search_status : '';
        $search_mode       = ($request->search_mode) ? $request->search_mode : '';
        $search_plan       = ($request->search_plan) ? $request->search_plan : '';
        $search_vendor     = ($request->search_vendor) ? $request->search_vendor : '';
        $search_keyword    = ($request->search_keyword) ? $request->search_keyword : '';

        $down_file_name = 'Transactions';
        $transactions = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation']);

        if ($search_keyword != ''){
            $transactions->where(function($query)use($search_keyword,$transactions){
                $query->where('amount','like',"%{$search_keyword}%")
                    ->orWhere('payment_type','like',"%{$search_keyword}%")
                    ->orWhere('razorpay_order_id','like',"%{$search_keyword}%")
                    ->orWhereHas('user',function($query)use($search_keyword){
                        $query->where('account_id','like',"%{$search_keyword}%");
                    })
                    ->orWhereHas('user.userTranslations',function($query)use($search_keyword){
                        $query->where('full_name','like',"%{$search_keyword}%");
                    })
                    ->orWhereHas('subscriptionPlan.subscriptionPlanTranslations',function($query)use($search_keyword){
                        $query->where('name','like',"%{$search_keyword}%");
                    });
            });
        }
        if(empty($from_date) && empty($to_date)){
            $transactions = $transactions->where('transactions.purchase_date','>=',now()->subMonth());
        }else{
            if($from_date != ''){
                $transactions = $transactions->where('transactions.purchase_date','>=',$from_date);
            }
            if($to_date != ''){
                $transactions = $transactions->where('transactions.purchase_date','<',$to_date);
            }
        }
        if($search_mode != ''){
            $transactions = $transactions->where('transactions.payment_type',$search_mode);
            if($search_mode == 'COUPON' && $search_vendor != '') {
                $transactions = $transactions->where('transactions.coupon_vendor_id',$search_vendor);
            }
        }
        if($search_status != '') {
            $transactions = $transactions->where('transactions.status',$search_status);
        }
        if($search_plan != '') {
            $transactions = $transactions->where('transactions.plan_id',$search_plan);
        }

        $transactions = $transactions->get();
        if (!$transactions->isEmpty()) {
            foreach ($transactions as $transaction) {
                $data[] = [
                    'Account Id'                    =>  $transaction->user ? ($transaction->user->account_id ?? "") :  "",
                    'Name'                          =>  $transaction->user ? ($transaction->user->userTransDefault ? $transaction->user->userTransDefault->full_name : "") : "",
                    'Email'                         =>  $transaction->user ? ($transaction->user->email ?? '') :  '',
                    'Phone'                         =>  $transaction->user ? ($transaction->user->contact_no ?? '') :  '',
                    'Plan Name'                     =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan->subscriptionPlanTranslation ? $transaction->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                    'Months'                        =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan ? $transaction->subscriptionPlan->months : "N/A") : "",
                    'Amount'                        =>  $transaction->subscriptionPlan ? ($transaction->subscriptionPlan ? $transaction->subscriptionPlan->amount : "N/A") : "",                    
                    'Payment Type'                  =>  $transaction->payment_type ?? "",
                    'Razorpay Order Id'             =>  $transaction->razorpay_order_id ?? "",
                    'Razorpay Payment Id'           =>  $transaction->razorpay_payment_id ?? "",
                    'Razorpay Signature'            =>  $transaction->razorpay_signature ?? "",
                    'Transaction Id'                =>  $transaction->transaction_id ?? "",
                    'Original Transaction Id'       =>  $transaction->original_transaction_id ?? "",
                    'Web Order Line Item Id'        =>  $transaction->web_order_line_item_id ?? "",
                    'Purchase Date'                 =>  $transaction->purchase_date ?? "",
                    'Original Purchase Date'        =>  $transaction->original_purchase_date ?? "",
                    'Subscription End Date'         =>  $transaction->subscription_end_date ?? "",
                    'Receipt Data'                  =>  $transaction->receipt_data ?? "",
                    'In App Ownership Type'         =>  $transaction->in_app_ownership_type ?? "",
                    'Subscription Group Identifier' =>  $transaction->subscription_group_identifier ?? "",
                    'City'                          =>  !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->name ?? '') :  '',
                    'State'                         =>  !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->state ?? '') :  '',
                    'Status'                        =>  $transaction->status ?? "",
                    'Created at'                    =>  $transaction->created_at ? Carbon::parse($transaction->created_at)->format('Y-m-d') : ""
                ];
            }
            if(!File::exists(public_path()."/files")){
                File::makeDirectory(public_path()."/files");
            }
            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            try{
                chmod($filename,0777);
            }catch(Exception $e){}
            fputcsv($handle, array(
                'Account Id','Name','Email','Phone','Plan Name',
                'Months','Amount','Payment Type','Razorpay Order Id','Razorpay Payment Id',
                'Razorpay Signature','Transaction Id','Original Transaction Id','Web Order Line Item Id','Purchase Date',
                'Original Purchase Date','Subscription End Date','Receipt Data','In App Ownership Type','Subscription Group Identifier',
                'City','State','Status','Created at'
            ));
            foreach ($data as $row){
                fputcsv($handle,array(
                    $row['Account Id'],$row['Name'],$row['Email'],$row['Phone'],$row['Plan Name'],
                    $row['Months'],$row['Amount'],$row['Payment Type'],$row['Razorpay Order Id'],$row['Razorpay Payment Id'],
                    $row['Razorpay Signature'],$row['Transaction Id'],$row['Original Transaction Id'],$row['Web Order Line Item Id'],$row['Purchase Date'],
                    $row['Original Purchase Date'],$row['Subscription End Date'],$row['Receipt Data'],$row['In App Ownership Type'],$row['Subscription Group Identifier'],
                    $row['City'],$row['State'],$row['Status'],$row['Created at']
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
