<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PushNotificationRequest;
use App\Http\Traits\FirebaseTrait;
use App\Models\User;

class PushNotificationController extends Controller
{
    use FirebaseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.push-notification.create')->with(['custom_title' => 'Push Notifiaction']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PushNotificationRequest $request)
    {
        if(  $request->has('user_type') && $request->has('subject') && $request->has('message') ) {
            $title = $request->subject;

            $users = User::with('deviceToken')->whereIsActive('y');
            if(!empty($request->user_type) && $request->user_type != 'send_all'){
                if ($request->user_type == "send_male") {
                    $users = $users->where("Gender","Male");
                }else if ($request->user_type == "send_female") {
                    $users = $users->where("Gender","Female");
                }else if ($request->user_type == "send_empty_profile_image") {
                    $users = $users->whereNull("profile_photo");
                }else if ($request->user_type == "send_less_then_15_pr") {
                    $users = $users->where("profile_percentage","<","15");
                }else if ($request->user_type == "send_unverified_photo") {
                    $users = $users->whereNull("photo_verified_at");
                }else if ($request->user_type == "send_unverified_photo") {
                    $users = $users->whereNull("email_verified_at");
                }else if ($request->user_type == "send_unverified_photo") {
                    $users = $users->whereNull("contact_verified_at");
                }else{
                    flash('Unable to send push notification Please select valid type.')->error();
                    return redirect(route('admin.push-notification.index'));
                }
            }
            $users = $users->limit(1);
            $users = $users->get();
            // echo "<pre>"; print_r($users->toArray()); die();

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'push_notification',
                'value'         =>  'Push Notification Send By Admin',
                'user_id'       =>  $request->user_type,
                'title'         =>  $title,
                'message'       =>  $request->message,
                'image'         =>  '',
                'type'          =>  config('utility.notification.type.send_by_admin'),
            ];
                
            $this->sendPushNotificationToAll($notification, $users);
            
            flash('Push Notification Send successfully!')->success();
        } else {
            flash('Unable to send push notification Please try again later.')->error();
        }
        return redirect(route('admin.push-notification.index'));
    }
}
