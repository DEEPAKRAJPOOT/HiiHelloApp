<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CollegeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\{ChatMessage,User};

class SystemChatController extends Controller {
	public function index() {
		return view('admin.pages.system-chat.index')->with(['custom_title'=>'System Chats']);
	}

	public function listing(Request $request) {
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$records = [];
		$system_chats = ChatMessage::where('room_id',config('utility.chat.system_chat_room'));
		if($search != ''){
			$system_chats->where(function($query)use($search){
				$query->where('message','like','"value" : "%'.$search.'%"');
			});
		}
		$count = $system_chats->groupBy('receiver_id')->count();
		$records['recordsTotal'] = $count;
		$records['recordsFiltered'] = $count;
		$records['data'] = [];
		$system_chats = $system_chats->groupBy('receiver_id')->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order);
		$system_chats = $system_chats->get();
		foreach($system_chats as $system_chat){
			$records['data'][] = [
				'id' => $system_chat->receiver->custom_id,
				'full_name' => $system_chat->receiver ? ($system_chat->receiver->userTransDefault ? $system_chat->receiver->userTransDefault->full_name : 'N/A') : 'N/A',
				'latest_message' => Str::limit($system_chat->getLatest()->getMessage()->value,30),
				'created_at' => date('Y-m-d H:i:s',strtotime($system_chat->getLatest()->created_at)),
				'action' => view('admin.layouts.includes.actions')->with(['custom_title'=>'System Chat','id'=> $system_chat->receiver->custom_id,'forbid_delete'=>true],$system_chat)->render(),
			];
		}
		return $records;
	}

	public function create(){
		return view('admin.pages.system-chat.create')->with(['custom_title' => 'System Chats']);
	}

	public function show($user_id){
		$system_user = User::where('id',config('utility.system.system_user_id'))->first();
		$user = User::whereCustomId($user_id)->firstOrFail();
		$system_chats = ChatMessage::withTrashed()->where('room_id',config('utility.chat.system_chat_room'))->where('receiver_id',$user->id)->orderBy('created_at','asc')->get();
		return view('admin.pages.system-chat.view',compact('system_user','user','system_chats'))->with(['custom_title' => 'System Chat']);
	}

	public function store(Request $request){
		$user = User::whereCustomId($request->user_id)->first();
		$system_chat_room_id = config('utility.chat.system_chat_room');
		$system_user_id = config('utility.system.system_user_id');
		$message = $request->message;
		if(!empty($user) && !empty($message)){
			ChatMessage::create([
				'custom_id'     =>  getUniqueString('chat_messages'),
				'room_id'       =>  $system_chat_room_id,
	            'sender_id'     =>  $system_user_id,
	            'receiver_id'   =>  $user->id,
	            'message'       =>  json_encode([
	            	'type'      =>  'text',
	            	'value'     =>  $request->message,
	            	'other'     =>  (object)[]
	            ])
			]);
			flash('Message sent successfully!')->success();
		}else{
			flash('Unable to send Message. Please try again later.')->error();
		}
		return redirect()->back();		
	}

	public function destroy(Request $request, $id) {
		$chat_message = ChatMessage::where('custom_id',$id)->where('room_id',config('utility.chat.system_chat_room'))->firstOrFail();
		$chat_message->delete();
		$receiver_id = $chat_message->receiver_id;
		flash('Message deleted successfully.')->success();
		$user = User::whereId($receiver_id)->first();
		if(!empty($user)){
			return redirect()->route('admin.system-chat.show',$user->custom_id);
		}else{
			return redirect()->route('admin.system-chat.index');
		}
	}

	public function sendBulk(Request $request){
		ini_set('max_execution_time',3600);
        set_time_limit(3600);
        if($request->has('user_type') && $request->has('message')){
        	$today_date = date('Y-m-d');
        	$users = User::whereIsActive('y');
        	if($request->user_type != 'send_all'){
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
                        flash('Unable to send message. Please select some users.')->error();
                        return redirect()->route('admin.system-chat.create');
                    }
                }else if($request->user_type == "send_test_users") {
                    $users = $users->where('is_test_user','y');
                }else{
                    flash('Unable to send message. Please select valid type.')->error();
                    return redirect()->route('admin.system-chat.create');
                }
        	}
        	$system_chat_room_id = config('utility.chat.system_chat_room');
			$system_user_id = config('utility.system.system_user_id');
			DB::beginTransaction();
			try{
	            $users->chunk(1000,function($user_chunk)use($system_chat_room_id,$system_user_id,$request){
	            	$bulk_insert = [];
	            	foreach($user_chunk as $user){
	            		$bulk_insert[] = [
	            			'custom_id'     =>  getUniqueString('chat_messages'),
							'room_id'       =>  $system_chat_room_id,
				            'sender_id'     =>  $system_user_id,
				            'receiver_id'   =>  $user->id,
				            'message'       =>  json_encode([
				            	'type'      =>  'text',
				            	'value'     =>  $request->message,
				            	'other'     =>  (object)[]
				            ]),
				            'created_at'    =>  now(),
				            'updated_at'    =>  now()
	            		];
	            	}
	            	ChatMessage::insert($bulk_insert);
	            });
	            DB::commit();
        		flash('Message Sent successfully!')->success();
			}catch (\Exception $e) {
				DB::rollback();
				flash('Unable to send message. Please try again later.')->error();
        		return redirect()->route('admin.system-chat.create');
			}
        } else {
            flash('Unable to send message. Please try again later.')->error();
        	return redirect()->route('admin.system-chat.create');
        }
        return redirect()->route('admin.system-chat.index');
	}

}