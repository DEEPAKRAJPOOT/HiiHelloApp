<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Models\ApiLogs;
use DB; 

class ApiLogController extends Controller
{
    public function index()
    {
        return view('admin.pages.apilog.index')->with(['custom_title' => 'Users Api log']);
    }

    public function listing(Request $request)
    {

        extract($this->DTFilters($request->all()));

        DB::enableQueryLog();

        $from_date         = ($request->from_date) ? $request->from_date." 00:00:00" : "";
        $to_date           = ($request->to_date) ? $request->to_date." 23:59:59" : "";
        $api_status        = ($request->api_status) ? $request->api_status : "";

        $records = [];

        $apilogscount = ApiLogs::select("api_logs.*","api_logs.api_status as api_status","users.account_id as account_id","user_translations.full_name as full_name")
                        ->leftJoin("users","api_logs.user_id","=","users.id")
                        ->leftJoin("user_translations","users.id","=","user_translations.user_id"," and ","user_translations.locale","=","en")
                        ->groupBy("api_logs.id")
                        ->orderBy("api_logs.created_at","DESC")
                        ->get();


        $apilogs = ApiLogs::select("api_logs.*","api_logs.api_status as api_status","users.account_id as account_id","user_translations.full_name as full_name")
                        ->leftJoin("users","api_logs.user_id","=","users.id")
                        ->leftJoin("user_translations","users.id","=","user_translations.user_id"," and ","user_translations.locale","=","en")
                        ->groupBy("api_logs.id");

        if ($search != '') {
            $apilogs->where(function ($query) use ($search) {
                $query->Where('users.account_id', 'like', "%{$search}%")
                    ->orWhere('api_logs.created_at', 'like', "%{$search}%")
                    ->orWhere('user_translations.full_name', 'like', "%{$search}%");
            });
        }

        // ST - Filter
        if($from_date != '') {
            $apilogs->whereBetween('api_logs.created_at', [$from_date, $to_date]);
        }

        if($api_status != '') {
            $apilogs->where('api_logs.api_status', $api_status);
        }

        $records['recordsTotal'] = count($apilogscount);
        $records['recordsFiltered'] = count($apilogscount);
        $records['data'] = [];

       
       
        $apilogs = $apilogs->offset($offset)->limit($limit)->orderBy("api_logs.created_at",$sort_order);
        $apilogs = $apilogs->get();

        // echo "<pre>"; print_r($apilogs->toArray()); die();
        // dd(DB::getQueryLog());
        // exit();

        foreach ($apilogs as $apilog) {
            $records['data'][] = [
                'account_id' => $apilog->account_id ?? "N/A",
                'full_name' =>  $apilog->full_name ?? "N/A",
                'created_at' => date('Y-m-d h:i A', strtotime($apilog->created_at)) ?? 'N/A',
                'api_status' =>  $apilog->api_status ?? "N/A",
                // 'action' => view('admin.layouts.includes.view_apilog')->with(['custom_title' => 'User log data', 'id' => $apilog->id], $apilog)->render(),
            ];
        }
        return $records;
    }

    public function get_single_apilog_data(Request $request)
    {
        $auth_id = $request->user_id;
        $apilogs = array();
        if (!empty($auth_id)) {
            $apilogs   = ApiLogs::select("api_logs.*","users.account_id as account_id","user_translations.full_name as full_name")
                        ->leftJoin("user_translations","user_translations.user_id","=","api_logs.user_id")
                        ->leftJoin("users","users.id","=","api_logs.user_id")
                        ->where("user_translations.locale","en")
                        ->where("api_logs.id",$auth_id)
                        ->first();
        }
        return $apilogs;
    }

    public function csvDownload(Request $request)
    {
        $down_file_name = 'Api log report';
        $apilogs = ApiLogs::select("api_logs.*","api_logs.api_status as api_status","users.account_id as account_id","user_translations.full_name as full_name")
                        ->leftJoin("users","api_logs.user_id","=","users.id")
                        ->leftJoin("user_translations","users.id","=","user_translations.user_id"," and ","user_translations.locale","=","en")
                        ->groupBy("api_logs.id")
                        ->orderBy("api_logs.created_at","DESC")
                        ->limit(10000);

        $apilogs = $apilogs->get();
        if (!$apilogs->isEmpty()) {
            foreach ($apilogs as $log) {
                $data[] = [
                    'Account Id'            =>  $log->account_id ?? "",
                    'Full Name'             =>  $log->full_name ?? "",
                    'URL'                   =>  $log->url,
                    'Request'               =>  $log->request ?? "",
                    'Response'              =>  $log->response ?? "",
                    'Status Code'           =>  $log->api_status ?? "",
                    'Created At'            =>  $log->created_at ?? "",
                ];
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id', 'Full Name', 'URL', 'Request', 'Response', 'Status Code', 'Created At'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Full Name'], $row['URL'], $row['Request'], $row['Response'], $row['Status Code'], $row['Created At'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate user csv. Try again later')->error();
        }
        return redirect(route('admin.apilog'));
    }
}
