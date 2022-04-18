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
            if(!empty($request->user_type) && $request->user_type != 'All'){
                $users = User::whereGender($request->user_type);
            }
            $users = $users->get();

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'push_notification',
                'value'         =>  'Push Notification Send By Admin',
                'user_id'       =>  $request->user_type,
                'title'         =>  $title,
                'message'       =>  $request->message,
                'image'         =>  '',
                'type'          =>  'notification',
            ];
            $this->sendPushNotificationToAll($notification, $users);
            
            flash('Push Notification Send successfully!')->success();
        } else {
            flash('Unable to send push notification Please try again later.')->error();
        }
        return redirect(route('admin.push-notification.index'));
    }
}
