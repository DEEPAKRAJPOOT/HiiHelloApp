<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageModerationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

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
    public function show($custom_id)
    {
        $call = CallLog::with(['room.creator.userTransDefault', 'room.participator.userTransDefault',])
                    ->whereCustomId($custom_id)->firstOrFail();
        return view('admin.pages.call-logs.view', compact('call'))->with(['custom_title' => 'Call Logs']);
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        //$call_logs = ImageModerationLog::with(['userDetails'])->orderBy($sort_column, $sort_order);

        $call_logs = ImageModerationLog::select('image_moderation_log.*','user_translations.full_name as full_name');
        $call_logs->leftJoin('user_translations', 'user_translations.user_id', '=', 'image_moderation_log.user_id')->where('locale','en')->orderBy($sort_column, $sort_order);

        

        if ($search != '') {
            $call_logs->where(function ($query) use ($search, $call_logs) {
                $query->where('created_at', 'like', "%{$search}%")
                    ->orWhereHas('userDetails.userTranslations', function ($query1) use ($search) {
                        $query1->where('full_name', 'like', "%{$search}%");
                    });
                    
            });
        }

        $count = $call_logs->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        // $call_logs = $call_logs->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $call_logs = $call_logs->get();

       

        foreach ($call_logs as $call_log) {

            
                $records['data'][] = [
                'full_name'             => $call_log->userDetails->full_name,
                'message'               => $call_log->message,
                'created_at'            => date('Y-m-d H:i:s',strtotime($call_log->created_at)),
                'is_approved'           => $call_log->is_approved > 0 ? "Approved" : "Un Approved",
                'total_face_detected'   => $call_log->total_face_detected,                
                'endpoint_url'          => $call_log->endpoint_url, 

            ];
        }
        return $records;
    }

    public function csvDownload(Request $request)
    {
        
        $down_file_name = 'Image Moderation Log';
        //$call_logs = CallLog::with('room','room.creator.userTransEn', 'room.participator.userTransEn', 'room.creator','room.participator')->get();


        $records = [];
        //$call_logs = ImageModerationLog::with(['userDetails'])->orderBy($sort_column, $sort_order);

        $call_logs = ImageModerationLog::select('image_moderation_log.*','user_translations.full_name as full_name','user_translations.full_name as full_name');
        $call_logs->leftJoin('user_translations', 'user_translations.user_id', '=', 'image_moderation_log.user_id')->where('locale','en');

        
        $call_logs = $call_logs->get();

        if (!$call_logs->isEmpty()) {
            foreach ($call_logs as $call_log) {
                $data[] = [
                    'user_id'               => $call_log->user_id,
                    'full_name'             => $call_log->full_name,
                    'message'               => $call_log->message,
                    'created_at'            => date('Y-m-d H:i:s',strtotime($call_log->created_at)),
                    'is_approved'           => $call_log->is_approved > 0 ? "Approved" : "Un Approved",
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
        return redirect(route('admin.call-logs.index'));
    }
}
