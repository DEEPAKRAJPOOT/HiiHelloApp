<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        $records = [];
        $apilogs = ApiLogs::select("api_logs.*","users.account_id as account_id","user_translations.full_name as full_name")
                        ->join("users","users.id","=","api_logs.user_id")
                        ->join("user_translations","user_translations.user_id","=","api_logs.user_id")
                        ->where("user_translations.locale","en");
                        // ->groupBy("api_logs.id");

        if ($search != '') {
            $apilogs->where(function ($query) use ($search) {
                $query->Where('users.account_id', 'like', "%{$search}%")
                    ->orWhere('api_logs.created_at', 'like', "%{$search}%")
                    ->orWhere('user_translations.full_name', 'like', "%{$search}%");
            });
        }


        $count = $apilogs->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
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
                'action' => view('admin.layouts.includes.user_match')->with(['custom_title' => 'User log data', 'id' => $apilog->user_id], $apilog)->render(),
            ];
        }
        return $records;
    }
}
