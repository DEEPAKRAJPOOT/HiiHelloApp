<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CollegeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ChatMessage;

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
				'created_at' => date('Y-m-d H:i:s',strtotime($system_chat->created_at)),
				'action' => view('admin.layouts.includes.actions')->with(['custom_title'=>'System Chat','id'=> $system_chat->receiver->custom_id],$system_chat)->render(),
			];
		}
		return $records;
	}

	public function create(){
		return view('admin.pages.colleges.create',compact('states'))->with(['custom_title' => 'College']);
	}

	public function show(College $college){
		$college = College::whereId($college->id)->withCount('users')->firstOrFail();
		return view('admin.pages.colleges.view',compact('college'))->with(['custom_title' => 'College']);
	}

	public function destroy(Request $request, $id) {
		if (!empty($request->action) && $request->action == 'delete_all') {
			$content = ['status' => 204, 'message' => 'Something went wrong'];
			$colleges = College::whereIn('custom_id',explode(',',$request->ids))->get();
			foreach ($colleges as $college) {
				$college->delete();
			}
			$content['status'] = 200;
			$content['message'] = 'Colleges deleted successfully';
			$content['count'] = College::all()->count();
			return response()->json($content);
		} else {
			$college = College::where('custom_id',$id)->firstOrFail();
			$college->delete();
			if (request()->ajax()) {
				$content = array('status' => 200, 'message' => 'College deleted successfully', 'count' => College::all()->count());
				return response()->json($content);
			} else {
				flash('College deleted successfully.')->success();
				return redirect()->route('admin.colleges.index');
			}
		}
	}

	public function store(CollegeRequest $request){
		$college = College::create([
			'custom_id' => getUniqueString('colleges'),
			'name' => $request->college_name,
			'university' => $request->university_name,
			'district' => $request->district_name,
			'state' => $request->state_name,
			'abbreviation' => preg_replace('/[^a-z]/i','',$request->abbreviation ?? ''),
			'approved_at' => now()
		]);
		if($college){
			flash('College created successfully!')->success();
		} else {
			flash('Unable to create College. Please try again later.')->error();
		}
		return redirect(route('admin.colleges.index'));
	}

}