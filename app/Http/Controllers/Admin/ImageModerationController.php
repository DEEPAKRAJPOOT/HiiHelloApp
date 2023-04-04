<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use App\Models\ImageModerationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Exception;

class ImageModerationController extends Controller
{
    public function index()
    {        
        return view('admin.pages.image-moderation-logs.index')->with(['custom_title' => 'Image Moderation Logs']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        $from_date         = ($request->from_date) ? $request->from_date." 00:00:00" : "";
        $to_date           = ($request->to_date) ? $request->to_date." 23:59:59" : "";
        $search_status     = ($request->search_status) ? $request->search_status : "";


        $records = [];
        
        
        $query_field_approve = DB::raw("(CASE WHEN is_approved='1' THEN 'Approve' ELSE 'Decline' END) as approve_staus");


        $image_logs = ImageModerationLog::select('image_moderation_log.*','user_translations.full_name as full_name',$query_field_approve);
        $image_logs->leftJoin('user_translations', 'user_translations.user_id', '=', 'image_moderation_log.user_id');
        $image_logs->leftJoin('users', 'users.id', '=', 'image_moderation_log.user_id');
        $image_logs->where('locale','en')->orderBy($sort_column, $sort_order);

        // ST - Filter
        if($from_date != "" && $to_date != "") {
            $image_logs = $image_logs->whereBetween('image_moderation_log.created_at', [$from_date, $to_date]);
        }

        if($request->search_status != '' && $search_status == 1 || $search_status == '1') {
            $image_logs = $image_logs->where('image_moderation_log.is_approved',1);
        }

        if($request->search_status != '' && $search_status == 0 || $search_status == '0') {
            $image_logs = $image_logs->where('image_moderation_log.is_approved',0);
        }

        

        if ($search != '') {
            $image_logs->where(function ($query) use ($search, $image_logs) {
                $query->where('image_moderation_log.created_at', 'like', "%{$search}%")
                    ->orwhere('image_moderation_log.endpoint_url', 'like', "%{$search}%")
                    ->orwhere('image_moderation_log.message', 'like', "%{$search}%")
                    ->orwhere('users.account_id', 'like', "%{$search}%")
                    ->orWhereHas('userDetails.userTranslations', function ($query1) use ($search) {
                        $query1->where('full_name', 'like', "%{$search}%");
                    });
                    
            });
        }

        $count = $image_logs->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $image_logs = $image_logs->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $image_logs = $image_logs->get();
 

        foreach ($image_logs as $image_log) {
            
                $records['data'][] = [
                'account_id'            => $image_log->userDetails->account_id ?? "N/A",
                'full_name'             => $image_log->userDetails->full_name ?? "N/A",
                'message'               => $image_log->message,
                'created_at'            => date('Y-m-d H:i:s',strtotime($image_log->created_at)),
                'is_approved'           => $image_log->approve_staus,
                'total_face_detected'   => $image_log->total_face_detected,                
                'endpoint_url'          => $image_log->endpoint_url, 

            ];
        }

        //GET TODAYS TOTAL APPROVED RECORD
        $today_start_date = date("Y-m-d")." 00:00:00";        
        $today_end_date = date("Y-m-d")." 23:59:59";
        $count_today_approved = $this->getImageLogSummury($today_start_date,$today_end_date,1);
        $count_today_decline = $this->getImageLogSummury($today_start_date,$today_end_date,0);


        //GET WEEKLY TOTAL APPROVED RECORD
        $today_start_date =  date('Y-m-d', strtotime('-7 days'))." 00:00:00";        
        $today_end_date = date("Y-m-d")." 23:59:59";

        $count_weekly_approved = $this->getImageLogSummury($today_start_date,$today_end_date,1);
        $count_weekly_decline = $this->getImageLogSummury($today_start_date,$today_end_date,0);


        $records['summury']['today_approved'] = $count_today_approved;
        $records['summury']['today_decline'] = $count_today_decline;
        $records['summury']['weekly_approved'] = $count_weekly_approved;
        $records['summury']['weekly_decline'] = $count_weekly_decline;

        
        return $records;
        
    }

    public function csvDownload(Request $request)
    {

        $from_date         = ($request->from_date_hidden) ? $request->from_date_hidden." 00:00:00" : "";
        $to_date           = ($request->to_date_hidden) ? $request->to_date_hidden." 23:59:59" : "";
        $search            = ($request->search_data_hidden) ? $request->search_data_hidden : "";

        

        
        $down_file_name = 'Image Moderation Log';
        $records = [];

        
        $query_field_approve = DB::raw("(CASE WHEN is_approved='1' THEN 'Approve' ELSE 'Decline' END) as approve_staus");


        $image_logs = ImageModerationLog::select('image_moderation_log.*','user_translations.full_name as full_name',$query_field_approve);
        $image_logs->leftJoin('user_translations', 'user_translations.user_id', '=', 'image_moderation_log.user_id')->where('locale','en');

        // ST - Filter
        if($from_date != "" && $to_date != "") {
            $image_logs = $image_logs->whereBetween('created_at', [$from_date, $to_date]);
        }

        

        if ($search != '') {
            $image_logs->where(function ($query) use ($search, $image_logs) {
                $query->where('created_at', 'like', "%{$search}%")->orwhere('endpoint_url', 'like', "%{$search}%")->orwhere('message', 'like', "%{$search}%")
                    ->orWhereHas('userDetails.userTranslations', function ($query1) use ($search) {
                        $query1->where('full_name', 'like', "%{$search}%");
                    });
                    
            });
        }

        
        $image_logs = $image_logs->get();

        

        if (!$image_logs->isEmpty()) {
            foreach ($image_logs as $call_log) {
                $data[] = [
                    'user_id'               => $call_log->userDetails->account_id ?? "N/A",
                    'full_name'             => $call_log->full_name ?? "N/A",
                    'message'               => $call_log->message,
                    'created_at'            => date('Y-m-d H:i:s',strtotime($call_log->created_at)),
                    'is_approved'           => $call_log->approve_staus,
                    'total_face_detected'   => $call_log->total_face_detected,   
                    'endpoint_url'          => $call_log->endpoint_url,                                  
                    'image_type'            => Ucfirst(str_replace("_"," ",$call_log->image_type)),      

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
            fputcsv($handle, array('User Id','User name', 'Message', 'Date', 'Status', 'Total Face Detected', 'End Point', 'Image Type'));

            foreach ($data as $row) {
                fputcsv($handle, array($row['user_id'], $row['full_name'], $row['message'], $row['created_at'], $row['is_approved'], $row['total_face_detected'], $row['endpoint_url'], $row['image_type'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate Call logs csv file. Try again later')->error();
        }
        return redirect(route('admin.image-logs'));
    }

    public static function getImageLogSummury($summury_start_date,$summury_end_date,$is_approved)
    {

        $image_today_approve = ImageModerationLog::select('id')->where('is_approved',$is_approved);
        if($summury_start_date != "" && $summury_end_date != "") {
            $image_today_approve = $image_today_approve->whereBetween('created_at', [$summury_start_date, $summury_end_date]);
        }
        $summury_count = $image_today_approve->count();

        return $summury_count;

    }

}
