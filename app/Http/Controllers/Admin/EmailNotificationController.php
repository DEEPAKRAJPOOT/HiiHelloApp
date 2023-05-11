<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\EmailNotificationRequest;
use App\Models\User;
use App\Jobs\BulkEmailJob;

class EmailNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.email-notification.create')->with(['custom_title'=>'Email Notification']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmailNotificationRequest $request)
    {
        ini_set('max_execution_time',3600);
        set_time_limit(3600);
        if( $request->has('user_type') && $request->has('subject') && $request->has('message') ) {
            
            $today_date = date('Y-m-d');
            $users = User::whereIsActive('y')->whereNotNull('email')->with('userTransDefault');

            if(!empty($request->user_type) && $request->user_type != 'send_all'){
                if ($request->user_type == "send_male") {
                    $users = $users->where('gender','Male');
                }else if ($request->user_type == "send_female") {
                    $users = $users->where('gender','Female');
                }else if ($request->user_type == "send_empty_profile_image") {
                    $users = $users->whereNull("profile_photo");
                }else if ($request->user_type == "send_empty_location") {
                    $users = $users->whereNull("location_id");
                }else if ($request->user_type == "send_empty_college") {
                    $users = $users->whereNull('college_id');
                }else if ($request->user_type == "send_less_then_20_pr") {
                    $users = $users->where("profile_percentage","<","20");
                }else if ($request->user_type == "send_unverified_photo") {
                    $users = $users->whereNull("photo_verified_at");
                }else if ($request->user_type == "send_unverified_email") {
                    $users = $users->whereNull("email_verified_at");
                }else if ($request->user_type == "send_unverified_phone") {
                    $users = $users->whereNull("contact_verified_at");
                }else if ($request->user_type == "send_paid_male_subscription_not_expired") {
                    $users = $users->where('gender','Male')->where('is_subscribed','y')->where('subscription_end_date','>',$today_date);
                }else if ($request->user_type == "send_paid_male_subscription_expired") {
                    $users = $users->where('gender','Male')->where(function($query){
                        $query->where('is_subscribed','n');
                        $query->orWhere('subscription_end_date','<=',$today_date);
                    });
                }else if($request->user_type == "send_selected_users") {
                    if(!empty($request->users) && is_array($request->users)){
                        $users = $users->whereIn('custom_id',$request->users);
                    }else{
                        flash('Unable to send email. Please select some users.')->error();
                        return redirect()->route('admin.email-notification.index');
                    }
                }else if($request->user_type == "send_test_users") {
                    $users = $users->where('is_test_user','y');
                }else{
                    flash('Unable to send email. Please select valid type.')->error();
                    return redirect()->route('admin.email-notification.index');
                }
            }
            $email = [
                'subject'  =>  $request->subject,
                'message'  =>  $request->message
            ];
            $users->chunk(500,function($user_chunk)use($email){
                dispatch(new BulkEmailJob($email,$user_chunk->toArray()));
            });
        
            flash('Email Notification Sent successfully!')->success();
        } else {
            flash('Unable to send email notification. Please try again later.')->error();
        }
        return redirect()->route('admin.email-notification.index');
    }
}
