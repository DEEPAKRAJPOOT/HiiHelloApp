<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CollegeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\College;

class CollegesController extends Controller {
	public function index() {
		$states = $this->getAllStates();
		return view('admin.pages.colleges.index',compact('states'))->with(['custom_title'=>'Colleges']);
	}

	public function listing(Request $request) {
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$records = [];
		$colleges = College::query();
		if($search != ''){
			$colleges->where(function($query)use($search){
				$query->where('name','like',"%{$search}%")
				->orWhere('university','like',"%{$search}%")
				->orWhere('abbreviation','like','%'.preg_replace('/[^a-z]/i','',$search).'%')
				->orWhere('district','like',"%{$search}%")
				->orWhere('state','like',"%{$search}%");
			});
		}
		if(!empty($request->get('college_filter'))){
			switch ($request->get('college_filter')) {
				case 'unapproved':
				$colleges->whereNull('approved_at');
				break;
			}
		}
		if ($request->filled('state_filter')) {
            $colleges->where('state',$request->state_filter);
        }
		$count = $colleges->count();
		$records['recordsTotal'] = $count;
		$records['recordsFiltered'] = $count;
		$records['data'] = [];
		$colleges = $colleges->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order);
		$colleges = $colleges->get();
		foreach($colleges as $college){
			$params = [
				'checked' => !empty($college->approved_at) ? 'checked' : '',
				'id' => $college->custom_id,
			];
			$records['data'][] = [
				'id' => $college->custom_id,
				'name' => $college->name,
				'university' => $college->university,
				'abbreviation' => $college->abbreviation ? $college->abbreviation : '-',
				'district' => $college->district,
				'state' => $college->state,
				'created_at' => date('Y-m-d H:i:s',strtotime($college->created_at)),
				'action' => view('admin.layouts.includes.actions')->with(['custom_title'=>'College','id'=> $college->custom_id],$college)->render(),
				'checkbox' => view('admin.layouts.includes.checkbox', compact('params'))->with('id', $college->custom_id)->render(),
				'approval_status' => view('admin.layouts.includes.switch', compact('params'))->render()
			];
		}
		return $records;
	}

	public function create(){
		$states = $this->getAllStates();
		return view('admin.pages.colleges.create',compact('states'))->with(['custom_title' => 'College']);
	}

	public function show(College $college){
		$college = College::whereId($college->id)->withCount('users')->firstOrFail();
		return view('admin.pages.colleges.view',compact('college'))->with(['custom_title' => 'College']);
	}

	public function edit(College $college){
		$states = $this->getAllStates();
		return view('admin.pages.colleges.edit',compact('college','states'))->with(['custom_title' => 'College']);
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

	public function bulkApprove(Request $request){
		if(!empty($request->ids)){
			$colleges = College::whereIn('custom_id',explode(',',$request->ids))->get();
			foreach($colleges as $college){
				$college->approved_at = now();
				$college->save();
			}
		}
		$content['status'] = 200;
		$content['message'] = 'Colleges approved successfully';
		$content['count'] = College::all()->count();
		return response()->json($content);
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

	public function update(CollegeRequest $request, College $college){
		if(!empty($request->action) && $request->action == 'change_status'){
			$content = ['status'=>204,'message'=>'Something went wrong'];
			if($college){
				$college->approved_at = ($request->value == 'y') ? now() : null;
				if ($college->save()) {
					$content['status'] = 200;
					$content['message'] = 'Status updated successfully';
				}
			}
			return response()->json($content);
		}else{
			if($college){
				$college->name = $request->college_name;
				$college->university = $request->university_name;
				$college->district = $request->district_name;
				$college->state = $request->state_name;
				$college->abbreviation = preg_replace('/[^a-z]/i','',$request->abbreviation ?? '');
				if($college->save()){
					flash('College updated successfully!')->success();
					return redirect(route('admin.colleges.index'));
				}
			}
		}
		flash('Unable to update college. Please try again later.')->error();
		return redirect(route('admin.colleges.index'));
	}

	private function getAllStates(){
		return College::select('state')->whereNotNull('approved_at')->groupBy('state')->orderBy('state','asc')->pluck('state');	
	}

}