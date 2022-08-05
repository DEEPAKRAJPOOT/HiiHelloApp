<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SubscriptionListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        extract($this->DTFilters($request->all()));
        $records = [];
        $subscriptions = Subscription::with([
            'subscriptionPlan', 'subscriptionPlan.subscriptionPlanTranslation',
            'user', 'user.userTransDefault'
        ])->orderBy($sort_column, $sort_order);

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

        $count = $subscriptions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $subscriptions = $subscriptions->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $subscriptions = $subscriptions->get();

        foreach ($subscriptions as $subscription) {
            $records['data'][] = [
                'id' => $subscription->id,
                'account_id' => $subscription->user ? ($subscription->user->account_id ?? "") : "",
                'user_id' => $subscription->user ? ($subscription->user->userTransDefault ? $subscription->user->userTransDefault->full_name : "N/A") : "",
                'plan_id' => $subscription->subscriptionPlan ? ($subscription->subscriptionPlan->subscriptionPlanTranslation ? $subscription->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "",
                'months' => $subscription->months,
                'amount' => $subscription->amount,
                'status' => $subscription->status,
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Subscriptions', 'id' => $subscription->custom_id], $subscription)->render(),

            ];
        }
        return $records;
    }
    public function csvDownload(Request $request)
    {
        $subscriptions = Subscription::with('user', 'subscriptionPlan')->get();
        if (!$subscriptions->isEmpty()) {
            foreach ($subscriptions as $key => $subscription) {
                $data[] = [
                    'Account Id'            =>  $subscription->user->account_id ?? "",
                    'Name'                  => $subscription->user->userTransEn->full_name ?? "",
                    'Email'                 =>  $subscription->email ?? "",
                    'Subscription Plan Name' => $subscription->subscriptionPlan->subscriptionPlanTransEn->name ?? '',
                    'Months'                 => $subscription->months ?? '',
                    'Amount'                => $subscription->amount ?? '',
                    'Start date'            => $subscription->start_date ?? '',
                    'End date'            => $subscription->end_date ?? '',
                    'Payment Type'          => $subscription->payment_type ?? '',
                    'Payment Date'            => $subscription->payment_date ?? '',
                    'Receipt Data'              => $subscription->receipt_data ?? '',
                    'Original Transaction Id'   => $subscription->original_transaction_id ?? '',
                    'Status'                    => $subscription->status ?? '',
                    'Created at'                => $subscription->created_at ? Carbon::parse($subscription->created_at)->format('o-m-d') : ''
                ];
            }

            $filename = "subscriptions-csv.csv";
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id',
                'Name',
                'Email',
                'Subscription Plan Name',
                'Months',
                'Amount',
                'Start date',
                'End date',
                'Payment Type',
                'Payment Date',
                'Receipt Data',
                'Original Transaction Id',
                'Status',
                'Created at'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'],
                    $row['Name'],
                    $row['Email'],
                    $row['Subscription Plan Name'],
                    $row['Months'],
                    $row['Amount'],
                    $row['Start date'],
                    $row['End date'],
                    $row['Payment Type'],
                    $row['Payment Date'],
                    $row['Receipt Data'],
                    $row['Original Transaction Id'],
                    $row['Status'],
                    $row['Created at'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            // flash('user csv generated successfully!')->success();
            return Response::download($filename, 'subscriptions-csv.csv', $headers);
        } else {
            flash('Unable to generate user csv. Try again later')->error();
        }
        return redirect(route('admin.subscription-lists.index'));
    }
}
