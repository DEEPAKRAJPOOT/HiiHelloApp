<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileReport;

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
    public function update(Request $request, ProfileReport $profile_report)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($profile_report) {
                $profile_report->is_active = $request->value;
                if($profile_report->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $is_user_active = 'y';
            $profile_report->update($request->all());
            if($profile_report->status == 'Accepted'){
                $is_user_active = 'n';
            }
            if($profile_report->reportedUser){
                $profile_report->reportedUser->is_active = $is_user_active;
                $profile_report->reportedUser->save();
            }

            if( $profile_report->save() ) {
                flash('Profile Report details updated successfully!')->success();
            } else {
                flash('Unable to profile report. Try again later')->error();
            }
            return redirect(route('admin.profile-reports.index'));
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $profile_reports = ProfileReport::with('user','reportedUser')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $profile_reports->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reportedUser', function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        $count = $profile_reports->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $profile_reports = $profile_reports->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $profile_reports = $profile_reports->get();

        foreach ($profile_reports as $profile_report) {
            $params = [
                'checked'       =>  ($profile_report->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $profile_report->is_active,
                'class'         =>  '',
                'id'            =>  $profile_report->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $profile_report->id,
                'user_id'       =>  $profile_report->user ? $profile_report->user->full_name : "",
                'reported_user_id' =>  $profile_report->reportedUser ? $profile_report->reportedUser->full_name : "",
                'message'       =>  $profile_report->message,
                'status'        =>  $profile_report->status,
                'created_at'    =>  $profile_report->created_at,
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Profile Report', 'id' => $profile_report->custom_id], $profile_report)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $profile_report->custom_id)->render(),
            ];
        }
        return $records;
    }
}
