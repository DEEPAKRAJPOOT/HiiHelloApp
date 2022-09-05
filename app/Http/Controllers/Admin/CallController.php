<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class CallController extends Controller
{
    public function index()
    {
        return view('admin.pages.call-logs.index')->with(['custom_title' => 'Call Logs']);
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
        $call_logs = CallLog::with(['room','room.creator.userTransEn', 'room.participator.userTransEn', 'room.creator','room.participator'])->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $call_logs->where(function ($query) use ($search, $call_logs) {
                $query->where('date', 'like', "%{$search}%")
                    ->orWhere('start_time', 'like', "%{$search}%")
                    ->orWhere('end_time', 'like', "%{$search}%")
                    ->orWhere('remaining_time', 'like', "%{$search}%")
                    ->orWhereHas('room.creator.userTranslations', function ($query1) use ($search) {
                        $query1->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room.participator.userTranslations', function ($query1) use ($search) {
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
                'id' => $call_log->custom_id,
                'creator_name' =>  $call_log->room ? $call_log->room->creator ? $call_log->room->creator->userTransEn ? $call_log->room->creator->userTransEn->full_name : "-" : "-" : "-",
                'participator_name' =>  $call_log->room ? $call_log->room->participator ? $call_log->room->participator->userTransEn ? $call_log->room->participator->userTransEn->full_name : "-" : "-" : "-",
                'date' => $call_log->date ?? '--',
                'remaining_time' => $call_log->remaining_time ?? '--',
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Call Logs', 'id' => $call_log->custom_id], $call_log)->render(),

            ];
        }
        return $records;
    }

    public function csvDownload(Request $request)
    {
        $down_file_name = 'Call Log';
        $call_logs = CallLog::with('room','room.creator.userTransEn', 'room.participator.userTransEn', 'room.creator','room.participator')->get();

        if (!$call_logs->isEmpty()) {
            foreach ($call_logs as $call_log) {
                $data[] = [
                    'Creator name' =>  $call_log->room ? $call_log->room->creator ? $call_log->room->creator->userTransEn ? $call_log->room->creator->userTransEn->full_name : "" : "" : "",
                    'Participator name' =>  $call_log->room ? $call_log->room->participator ? $call_log->room->participator->userTransEn ? $call_log->room->participator->userTransEn->full_name : "" : "" : "",
                    'Date' => $call_log->date,
                    'Remaining time' => $call_log->start_time,
                    'Start time' => $call_log->end_time,
                    'End time' => $call_log->remaining_time,
                    'Created at' =>  $call_log->created_at ? Carbon::parse($call_log->created_at)->format('Y-m-d') : ""
                ];
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array('Creator name', 'Participator name', 'Date', 'Start time', 'End time', 'Remaining time','Created at'));

            foreach ($data as $row) {
                fputcsv($handle, array($row['Creator name'], $row['Participator name'], $row['Date'], $row['Start time'], $row['End time'], $row['Remaining time'], $row['Created at'],
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
