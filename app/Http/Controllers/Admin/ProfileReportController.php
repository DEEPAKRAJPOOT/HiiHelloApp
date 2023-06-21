<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileReport;
use App\Models\User;
use App\Models\BlockUser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
class ProfileReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.profile-reports.index')->with(['custom_title' => 'Profile Reports']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileReport $profile_report)
    {
        return view('admin.pages.profile-reports.view', compact('profile_report'))->with(['custom_title' => 'Profile Report']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileReport $profile_report)
    {
        return view('admin.pages.profile-reports.edit', compact('profile_report'))->with(['custom_title' => 'Profile Report']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // echo "<pre>"; print_r($request->all()); die();
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            // if($profile_report) {
            //     $profile_report->is_active = $request->value;
            //     if($profile_report->save()) {
            //         $content['status']=200;
            //         $content['message'] = "Status updated successfully.";
            //     }
            // }
            if(!empty($request->id) && !empty($request->value)) {
                User::where('custom_id',$request->id)->update([ 
                    'is_active' =>  $request->value,
                ]);
                $content['status']=200;
                $content['message'] = "Status updated successfully.";
            }
            return response()->json($content);
        } else {
            // $is_user_active = 'y';
            // $profile_report->update($request->all());
            // if($profile_report->status == 'Accepted'){
            //     $is_user_active = 'n';
            // }
            // if($profile_report->reportedUser){
            //     $profile_report->reportedUser->is_active = $is_user_active;
            //     $profile_report->reportedUser->save();
            // }

            // if( $profile_report->save() ) {
            //     flash('Profile Report details updated successfully!')->success();
            // } else {
            //     flash('Unable to profile report. Try again later')->error();
            // }
            flash('Unable to profile report. Try again later')->error();
            return redirect(route('admin.profile-reports.index'));
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        DB::enableQueryLog();

        $records = [];
        $users = User::select("users.id as id","users.custom_id as custom_id","users.account_id as account_id","users.profile_photo as profile_photo","users.gender as gender","users.country_code as country_code","users.contact_no as contact_no","users.is_active as is_active");
        // $users = $users->leftJoin("block_users","block_users.blocked_to","=","users.id");
        $users = $users->with('userTransDefault');

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('account_id', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhereHas('userTransDefault', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // EN - Filter
        $count = $users->count();
        // $users = $users->groupBy('users.id');

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

       
        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();

        // dd(DB::getQueryLog());
        // exit();

        foreach ($users as $user) {
            //search filter
            if ($request->filter_types == 1) {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')->count();
            }
            else if ($request->filter_types == 2) {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->whereBetween("created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->whereBetween("created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])->count();
            }
            else if ($request->filter_types == 3) {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('m').'%')->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('m').'%')->count();
            }
            else if ($request->filter_types == 4) {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('Y').'%')->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->where("created_at","LIKE",'%'.Carbon::now()->format('Y').'%')->count();
            }
            else if ($request->filter_types == 5 && !empty($request->from_date) && !empty($request->to_date)) {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();
            }
            else
            {
                $total_block = BlockUser::blockedOnly()->where("blocked_to",$user->id)->count();
                $total_reports = ProfileReport::where("reported_user_id",$user->id)->count();
            }

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->custom_id,
                'user_id' => $user->id,
            ];

            $records['data'][] = [
                'id' => $user->id, 
                'profile_photo' => view('admin.layouts.includes.photos_verify')->with(['user_id' => $user->id,'profile_photo' => $user->profile_photo  ?? 'N/A', 'is_profile_photo' => 1, 'is_verify_photo' => 0])->render(),
                'account_id' => $user->account_id ?? "N/A",
                'full_name' =>  $user->userTransDefault ? $user->userTransDefault->full_name : "N/A",
                'gender' => $user->gender ?? "N/A",
                'contact_no' => $user->contact_no ? '<a href="tel:' . $user->country_code . '' . $user->contact_no . '" >' . $user->country_code . '' . $user->contact_no . '</a>' : 'N/A',
                'active'            =>  view('admin.pages.profile-reports.switch', compact('params'))->render(),
                'total_block' => $total_block,
                'total_reports' => $total_reports,
                'action' => view('admin.layouts.includes.user_report')->with(['custom_title' => 'User Report Data', 'id' => $user->id], $user)->render(),
            ];

        }
        return $records;
    }
    public function csvDownload(Request $request)
    {
        $down_file_name = 'Profile Report';
        $profile_reports = User::select("users.id as id","users.account_id as account_id","users.profile_photo as profile_photo","users.gender as gender","users.country_code as country_code","users.contact_no as contact_no",DB::raw("(select count(block_users.id) from block_users where block_users.blocked_to = users.id) as total_block "), DB::raw("(select count(profile_reports.reported_user_id) as total_reports from profile_reports where profile_reports.reported_user_id = users.id) as total_reports"));
        $profile_reports = $profile_reports->with('userTransDefault');
        $profile_reports = $profile_reports->groupBy('users.id');
        $profile_reports = $profile_reports->orderBy("total_block","DESC");
        $profile_reports = $profile_reports->get();
        if (!$profile_reports->isEmpty()) {
            foreach ($profile_reports as $profile_report) {

                $data[] = [
                    'account_id'        =>  $profile_report->account_id,
                    'full_name'         =>  $profile_report->userTransDefault ? $profile_report->userTransDefault->full_name : "N/A",
                    'gender'            =>  $profile_report->gender ?? "N/A",
                    'contact_no'        =>  $profile_report->contact_no ? $profile_report->contact_no : "N/A",
                    'total_block'       =>  $profile_report->total_block,
                    'total_reports'     =>  $profile_report->total_reports,
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
                'Account Id', 'User name', 'Gender', 'Contact No','Total Block Users','Total Reports'  
            ));
            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['account_id'], $row['full_name'], $row['gender'], $row['contact_no'], $row['total_block'], $row['total_reports']
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

    public function filters(Request $request)
    {
        $total_block_user = 0;
        $total_report_users = 0;

        if ($request->filter_type == 1) {
            $total_block_user = BlockUser::blockedOnly()->where("created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')->count();
            $total_report_users = ProfileReport::where("created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')->count();
        }
        else if ($request->filter_type == 2) {
            $total_block_user = BlockUser::blockedOnly()->whereBetween("created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])->count();
            $total_report_users = ProfileReport::whereBetween("created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])->count();
        }
        else if ($request->filter_type == 3) {
            $total_block_user = BlockUser::blockedOnly()->where("created_at","LIKE",'%'.Carbon::now()->format('m').'%')->count();
            $total_report_users = ProfileReport::where("created_at","LIKE",'%'.Carbon::now()->format('m').'%')->count();
        }
        else if ($request->filter_type == 4) {
            $total_block_user = BlockUser::blockedOnly()->where("created_at","LIKE",'%'.Carbon::now()->format('Y').'%')->count();
            $total_report_users = ProfileReport::where("created_at","LIKE",'%'.Carbon::now()->format('Y').'%')->count();
        }
        else if ($request->filter_type == 5) {
            $total_block_user = BlockUser::blockedOnly()->where("created_at",">=",$request->fromdate_search)->where("created_at","<=",$request->todate_search)->count();
            $total_report_users = ProfileReport::where("created_at",">=",$request->fromdate_search)->where("created_at","<=",$request->todate_search)->count();
        }
        else {
            $total_block_user = BlockUser::blockedOnly()->count();
            $total_report_users = ProfileReport::count();
        }

        $records['total_block_user'] = number_format($total_block_user);
        $records['total_report_users'] = number_format($total_report_users);

        return $records;
    }

    public function get_user_report_data(Request $request)
    {
        $auth_id = $request->user_id;
        $profile_reports = array();
        $profile_blocks = array();
        if (!empty($auth_id)) {
            $profile_reports = ProfileReport::select("users.id as id","users.gender as gender","user_translations.full_name as full_name","profile_reports.message as message",DB::raw("DATE_FORMAT(profile_reports.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                    ->leftJoin("users","users.id","=","profile_reports.user_id")
                    ->leftJoin("user_translations","user_translations.user_id","=","users.id")
                    ->where("user_translations.locale","en")
                    ->where("profile_reports.reported_user_id",$auth_id)
                    ->groupBy('profile_reports.id')
                    ->get();
            $profile_blocks = BlockUser::blockedOnly()->select("users.id as id","users.gender as gender","user_translations.full_name as full_name","block_users.created_at")
                    ->leftJoin("users","users.id","=","block_users.block_by")
                    ->leftJoin("user_translations","user_translations.user_id","=","users.id")
                    ->where("user_translations.locale","en")
                    ->where("block_users.blocked_to",$auth_id)
                    ->groupBy('block_users.id')
                    ->get();
        }
        return compact('profile_reports','profile_blocks');
    }
}
