<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Coupon,CouponVendor,LocationTranslation,Subscription,SubscriptionPlan,Transaction,User};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\Admin\CouponRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class CouponUsersController extends Controller {
    public function index()
    {
        $coupon_vendors = CouponVendor::whereIsActive('y')->orderBy('name','asc')->get();
        $subscription_plans = SubscriptionPlan::whereIsActive('y')->with(['subscriptionPlanTransDefault'])->get();
        $states = LocationTranslation::select(DB::raw('COUNT(users.id) as user_count,GROUP_CONCAT(DISTINCT location_translations.location_id) as loc_ids,location_translations.state'))
            ->join('users','users.location_id','location_translations.location_id')
            ->where('location_translations.locale','en')
            ->groupBy('location_translations.state')
            ->orderBy('user_count','desc')
            ->havingRaw('user_count > 0')
            ->get();
        $locations = LocationTranslation::select(DB::raw('COUNT(users.id) as user_count,GROUP_CONCAT(DISTINCT location_translations.location_id) as loc_ids,location_translations.name'))
            ->join('users','users.location_id','location_translations.location_id')
            ->where('location_translations.locale','en')
            ->groupBy('location_translations.name')
            ->groupBy('location_translations.state')
            ->orderBy('user_count','desc')
            ->havingRaw('user_count > 0')
            ->get();
        return view('admin.pages.coupon-users.index',compact('coupon_vendors','subscription_plans','states','locations'))->with(['custom_title' => 'Coupon Users']);
    }

    public function listing(Request $request){
        extract($this->DTFilters($request->all()));
        $from_date         = ($request->from_date) ? $request->from_date : '';
        $to_date           = ($request->to_date) ? now()->create($request->to_date)->addDay()->format('Y-m-d') : '';
        $search_status     = ($request->search_status) ? $request->search_status : '';
        $search_plan       = ($request->search_plan) ? $request->search_plan : '';
        $search_vendor     = ($request->search_vendor) ? $request->search_vendor : '';
        $records = [];
        $transactions = Transaction::with(['subscriptionPlan','user','user.userTransDefault','subscriptionPlan.subscriptionPlanTranslation'])->where('transactions.payment_type','COUPON')->orderBy($sort_column,$sort_order);
        if ($search != '') {
            $transactions->where(function ($query) use ($search, $transactions) {
                $query->where('amount','like',"%{$search}%")
                    ->orWhere('payment_type','like',"%{$search}%")
                    ->orWhere('razorpay_order_id','like',"%{$search}%")
                    ->orWhereHas('user',function($query)use($search){
                        $query->where('account_id','like',"%{$search}%");
                        $query->orWhere('email','like',"%{$search}%");
                        $query->orWhere('contact_no','like',"%{$search}%");
                    })
                    ->orWhereHas('user.userTranslations', function ($query) use ($search) {
                        $query->where('full_name','like',"%{$search}%");
                    })
                    ->orWhereHas('user.location.locationTranslations',function($query)use($search){
                        $query->where('name','like',"%{$search}%");
                        $query->orWhere('state','like',"%{$search}%");
                    })
                    ->orWhereHas('subscriptionPlan.subscriptionPlanTranslations',function($query)use($search){
                        $query->where('name','like',"%{$search}%");
                    });
            });
        }
        if($from_date != '') {
            $transactions = $transactions->where('transactions.purchase_date','>=',$from_date);
        }
        if($to_date != '') {
            $transactions = $transactions->where('transactions.purchase_date','<',$to_date);
        }
        if($search_status != '') {
            $transactions = $transactions->where('transactions.status',$search_status);
        }
        if($request->search_plan != '') {
            $transactions = $transactions->where('transactions.plan_id',$request->search_plan);
        }
        if($request->search_vendor != '') {
            $transactions = $transactions->where('transactions.coupon_vendor_id',$request->search_vendor);
        }
        $count = $transactions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];
        $transactions = $transactions->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order);
        $transactions = $transactions->get();
        foreach ($transactions as $transaction){
            $records['data'][] = [
                'id' => $transaction->id,
                'account_id' =>  $transaction->user ? ($transaction->user->account_id ?? "") :  "",
                'user_id' =>  $transaction->user ? ($transaction->user->userTransDefault ? $transaction->user->userTransDefault->full_name : "") : "",
                'plan_id' => $transaction->subscriptionPlan ? ($transaction->subscriptionPlan->subscriptionPlanTranslation ? $transaction->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'razorpay_order_id' => $transaction->razorpay_order_id,
                'amount' => $transaction->amount,
                'status' => view('admin.layouts.includes.status-badge')->with('status',$transaction->status)->render(),
                'coupon_name' => $transaction->coupon_name,
                'purchase_date' => isset($transaction->purchase_date) && !empty($transaction->purchase_date) ? date("d-m-Y",strtotime($transaction->purchase_date)) : "N/A",
                'subscription_end_date' => isset($transaction->subscription_end_date) && !empty($transaction->subscription_end_date) ? date("d-m-Y",strtotime($transaction->subscription_end_date)) : "N/A",
                'created_at' => !empty($transaction->created_at) ? date('d-m-Y H:i:s',strtotime($transaction->created_at)) : 'N/A',
                'revoked_at' => !empty($transaction->revoked_at) ? date('d-m-Y H:i:s',strtotime($transaction->revoked_at)) : 'N/A',
                'state' => !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->state ?? 'N/A') :  'N/A',
                'city' => !empty($transaction->user->location->locationTransDefault) ? ($transaction->user->location->locationTransDefault->name ?? 'N/A') :  'N/A',
                'email' =>  $transaction->user ? ($transaction->user->email ?? 'N/A') :  'N/A',
                'phone' =>  $transaction->user ? ($transaction->user->contact_no ?? 'N/A') :  'N/A',
                'action' => view('admin.layouts.includes.actions')->with(['custom_title'=>'Coupon Subscription','id'=>$transaction->custom_id,'routeNameOverride'=>'admin.transaction-lists'],$transaction)->render(),
            ];
        }
        return $records;
    }
    
    public function csvDownload(Request $request)
    {
        $from_date         = ($request->from_date) ? $request->from_date : '';
        $to_date           = ($request->to_date) ? now()->create($request->to_date)->addDay()->format('Y-m-d') : '';
        $search_plan       = ($request->search_plan) ? $request->search_plan : '';
        $search_vendor     = ($request->search_vendor) ? $request->search_vendor : '';
        $search_keyword    = ($request->search_keyword) ? $request->search_keyword : '';

        $down_file_name = 'Coupon-Users';
        $transactions = Transaction::with(['subscriptionPlan', 'user', 'user.userTransDefault', 'subscriptionPlan.subscriptionPlanTranslation'])->where('transactions.payment_type','COUPON');

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
        if($search_plan != '') {
            $transactions = $transactions->where('transactions.plan_id',$search_plan);
        }
        if($search_vendor != '') {
            $transactions = $transactions->where('transactions.coupon_vendor_id',$search_vendor);
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
            fputcsv($handle, array(
                'Account Id','Name','Email','Phone','Plan Name',
                'Months','Amount','Razorpay Order Id','Razorpay Payment Id','Razorpay Signature',
                'Transaction Id','Original Transaction Id','Web Order Line Item Id','Purchase Date','Original Purchase Date',
                'Subscription End Date','Receipt Data','In App Ownership Type','Subscription Group Identifier','City',
                'State','Status','Created at'
            ));
            foreach ($data as $row){
                fputcsv($handle,array(
                    $row['Account Id'],$row['Name'],$row['Email'],$row['Phone'],$row['Plan Name'],
                    $row['Months'],$row['Amount'],$row['Razorpay Order Id'],$row['Razorpay Payment Id'],$row['Razorpay Signature'],
                    $row['Transaction Id'],$row['Original Transaction Id'],$row['Web Order Line Item Id'],$row['Purchase Date'],$row['Original Purchase Date'],
                    $row['Subscription End Date'],$row['Receipt Data'],$row['In App Ownership Type'],$row['Subscription Group Identifier'],$row['City'],
                    $row['State'],$row['Status'],$row['Created at']
                ));
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return Response::download($filename,$down_file_name.".csv",$headers);
        } else {
            flash('Unable to generate transaction csv file. Try again later')->error();
        }
        return redirect(route('admin.coupon-users.index'));
    }
    public function destroy(Request $request,$id) {
        $content = ['status'=>204,'message'=>'Something went wrong'];
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('custom_id',$id)->firstOrFail();
            if(empty($transaction->coupon->type) || $transaction->coupon->type != 'full'){
                DB::rollback();
                $content = ['status'=>204,'message'=>'Coupon is not full type'];
                return response()->json($content);
            }
            $transaction->revoked_at = now();
            if($transaction->save()){
                $user = User::where('id',$transaction->user_id)->firstOrFail();
                $user->is_subscribed = 'n';
                $user->subscription_end_date = NULL;
                if($user->save()){
                    //delete default girls plan
                    Subscription::where('user_id',$user->id)->whereNull('end_date')->delete();
                    DB::commit();
                    $content['status'] = 200;
                    $content['message'] = 'Coupon revoked successfully.';
                }
            }
        }catch (ModelNotFoundException $e) {
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
        }
        return response()->json($content);
    }
}