<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Exception;

class SubscriptionListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // echo "hello";die;
        return view('admin.pages.subscription-list.index')->with(['custom_title' => 'Subscriptions']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($custom_id)
    {
        $subscription = Subscription::with([
            'user', 'user.userTransDefault',
            'subscriptionPlan', 'subscriptionPlan.subscriptionPlanTransDefault'
        ])
            ->whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.subscription-list.view', ["sub" => $subscription])->with(['custom_title' => 'Subscription']);
    }

    public function listing(Request $request)
    {
        DB::enableQueryLog();
        extract($this->DTFilters($request->all()));
        $records = [];
        // dd($request->all());
        // if($from_date != '' && $to_date != ''){
        //     if($country_filter != ''){

        //         $locationsql .= " WHERE (locations.is_active = 'y' AND a.name LIKE '%".$search."%' AND DATE(a.created_at) BETWEEN '".$from_date."' AND '".$to_date."' AND a.country = '".$country_filter."') OR (locations.is_active = 'y' AND a.state LIKE '%".$search."%' AND DATE(a.created_at) BETWEEN '".$from_date."' AND '".$to_date."' AND a.country = '".$country_filter."')";
        //     }else{
        //         $locationsql .= " WHERE (locations.is_active = 'y' AND a.name LIKE '%".$search."%' AND DATE(a.created_at) BETWEEN '".$from_date."' AND '".$to_date."') OR (locations.is_active = 'y' AND a.state LIKE '%".$search."%' AND DATE(a.created_at) BETWEEN '".$from_date."' AND '".$to_date."')";
        //     }
            
        // }else{
        $subscriptions = Subscription::select('subscriptions.*','users.gender as gender');
        $subscriptions = $subscriptions->join("users","users.id","=","subscriptions.user_id");
        $subscriptions = $subscriptions->with([
            'subscriptionPlan', 'subscriptionPlan.subscriptionPlanTranslation',
            'user', 'user.userTransDefault'
        ]);
        

        $subscriptions = $subscriptions->where("users.gender","Male");
        $subscriptions = $subscriptions->orderBy($sort_column, $sort_order);
        if ($search != '') {
            $subscriptions->where(function ($query) use ($search) {
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

        if($request->from_date != '' && $request->to_date != ''){
            if ($request->status_filter != '') {
                $subscriptions = $subscriptions->where("subscriptions.status",$request->status_filter);
                $subscriptions = $subscriptions->whereBetween("subscriptions.start_date",[$request->from_date,$request->to_date]);
            }else{
                $subscriptions = $subscriptions->whereBetween("subscriptions.start_date",[$request->from_date,$request->to_date]);
            }
       }
       
        $count = $subscriptions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $subscriptions = $subscriptions->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $subscriptions = $subscriptions->get();
        // dd(DB::getQueryLog());
        // echo "<pre>"; print_r($subscriptions->toArray()); die();
        foreach ($subscriptions as $subscription) {
            // dd($subscription->user);
            $records['data'][] = [
                'id' => $subscription->id,
                'account_id' => $subscription->user ? ($subscription->user->account_id ?? "") : "",
                'user_id' => $subscription->user ? ($subscription->user->userTransDefault ? $subscription->user->userTransDefault->full_name : "N/A") : "",
                'plan_id' => $subscription->subscriptionPlan ? ($subscription->subscriptionPlan->subscriptionPlanTranslation ? $subscription->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'months' => $subscription->months,
                'day' => $subscription->day,
                'amount' => $subscription->amount,
                'start_date' => $subscription->start_date,
                'end_date' => $subscription->end_date ? $subscription->end_date : '',
                'payment_type' => $subscription->payment_type,
                'status' => $subscription->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscriptions', 'id' => $subscription->custom_id], $subscription)->render(),

            ];
        }
        return $records;
    }
    public function csvDownload(Request $request)
    {
        $down_file_name = 'Subscriptions';
        $subscriptions = Subscription::with('user.userTransEn', 'subscriptionPlan.subscriptionPlanTransEn')->get();
        if (!$subscriptions->isEmpty()) {
            foreach ($subscriptions as $subscription) {
                $data[] = [
                    'Account Id'                =>  $subscription->user ? $subscription->user->account_id ?? "" : "",
                    'Name'                      =>  $subscription->user ? ($subscription->user->userTransEn ? $subscription->user->userTransEn->full_name ?? "" : "") : "",
                    'Email'                     =>  $subscription->email ?? "",
                    'Subscription Plan Name'    =>  $subscription->subscriptionPlan ? ($subscription->subscriptionPlan->subscriptionPlanTransEn ? $subscription->subscriptionPlan->subscriptionPlanTransEn->name ?? "" : "") : "",
                    'Months'                    =>  $subscription->months ?? "",
                    'day'                       =>  $subscription->day,
                    'Amount'                    =>  $subscription->amount ?? "",
                    'Start date'                =>  $subscription->start_date ?? "",
                    'End date'                  =>  $subscription->end_date ?? "",
                    'Payment Type'              =>  $subscription->payment_type ?? "",
                    'Payment Date'              =>  $subscription->payment_date ?? "",
                    'Receipt Data'              =>  $subscription->receipt_data ?? "",
                    'Original Transaction Id'   =>  $subscription->original_transaction_id ?? "",
                    'Status'                    =>  $subscription->status ?? "",
                    'Created at'                =>  $subscription->created_at ? Carbon::parse($subscription->created_at)->format('Y-m-d') : ""
                ];
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            try{
                chmod($filename,0777);
            }catch(Exception $e){}
            fputcsv($handle, array(
                'Account Id', 'Name', 'Email', 'Subscription Plan Name', 'Months', 'Day', 'Amount', 'Start date', 'End date', 'Payment Type', 'Payment Date', 'Receipt Data', 'Original Transaction Id', 'Status', 'Created at'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Name'], $row['Email'], $row['Subscription Plan Name'], $row['Months'], $row['day'], $row['Amount'], $row['Start date'], $row['End date'], $row['Payment Type'], $row['Payment Date'], $row['Receipt Data'], $row['Original Transaction Id'], $row['Status'], $row['Created at'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate subscription csv file. Try again later')->error();
        }
        return redirect(route('admin.subscription-lists.index'));
    }
}
