<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\SystemMatch;
use App\Http\Requests\Admin\SystemMatchRequest;
use Illuminate\Database\QueryException;
use Exception;

class SystemMatchesController extends Controller {
	public function index() {
		return view('admin.pages.system-matches.index',$this->getChartData())->with(['custom_title'=>'System Matches']);
	}
	public function listing(Request $request) {
        session_write_close();
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$records = [
			'data' => []
		];
		$users = User::withAggregate('userTransDefault','full_name');
		$records['recordsTotal'] = $users->count();
		if($search != ''){
			$users->where(function($query)use($search){
				$query->whereHas('userTransDefault',function($query_translation)use($search){
					$query_translation->where('full_name','like','%'.$search.'%')->whereIsActive('y');
				});
			});
		}
		if($request->filled('user_created_after')) {
            $users->where('created_at','>=',$request->user_created_after);
        }
		if($request->filled('user_created_before')) {
            $users->where('created_at','<=',$request->user_created_before);
        }
		if($request->filled('gender_filter')) {
            $users->where('gender',$request->gender_filter);
        }
        if($sort_column == 'full_name'){
        	$sort_column = 'user_trans_default_full_name';
        }
        if($sort_column == 'user_created_at'){
        	$sort_column = 'created_at';
        }
		$records['recordsFiltered'] = $users->count();
		$users = $users
		->orderBy($sort_column,$sort_order)
		->offset($offset)->limit($limit)
		->get();
		foreach($users as $user){
			$total_system_matches = $user->system_matches_for()->whereHas('to_user')->count() + $user->system_matches_to()->whereHas('for_user')->count();
			$connected_system_matches = $user->system_matches_for()->whereHas('to_user')->where('is_connected',1)->count() + $user->system_matches_to()->whereHas('for_user')->where('is_connected',1)->count();
			$records['data'][] = [
				'full_name'                => $user->userTransDefault ? $user->userTransDefault->full_name : 'N/A',
				'total_system_matches'     => $total_system_matches,
				'connected_system_matches' => $connected_system_matches,
				'user_created_at'          => now()->parse($user->created_at,'UTC')->setTimezone('Asia/Kolkata')->format('Y-m-d').'<br><small>'.now()->create($user->created_at)->diffForHumans().'</small>',
				'action'                   => view('admin.layouts.includes.actions')->with(['custom_title'=>'User','id'=>$user->custom_id],$user)->render(),
			];
		}
		return $records;
	}
	public function show($user_id){
		return redirect()->route('admin.user-matches.show',$user_id);
	}
	public function create(){
		return view('admin.pages.system-matches.create')->with(['custom_title' => 'System Match']);
	}
	public function store(SystemMatchRequest $request){
		try{
			if($request->target_user == $request->match_user){
				return redirect()->back()->with('error','Target User and Match User cannot be same.');
			}
			$target_user = User::whereCustomId($request->target_user)->first();
			$match_user = User::whereCustomId($request->match_user)->first();
			if(empty($target_user) || empty($match_user)){
				return redirect()->back()->with('error','Unable to match selected users. Please Try again');
			}
			DB::beginTransaction();
			$match = SystemMatch::where('user_id',$target_user->id)->where('match_id',$match_user->id)->first();
			if(!empty($match)){
				$match->is_connected = 0;
				$match->save();
			}else{
				$match = SystemMatch::create([
					'custom_id'                 =>  getUniqueString('system_match'),
					'user_id'                   =>  $target_user->id,
					'match_id'                  =>  $match_user->id,
					'is_connected'              =>  0,
					'match_date'                =>  now()->format('Y-m-d'),
				]);
			}
			DB::commit();
			flash('Match created successfully!')->success();
			return redirect()->route('admin.system-matches.index');
		}catch (QueryException $e) {
			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage());
		}catch(Exception $e){
			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage());
		}
	}
	public function chartData(Request $request){
		$filters = [];
		if($request->filled('target_month_date') && $request->target_month_date != 'all'){
			$filters['start_date'] = now()->create($request->target_month_date)->format('Y-m-01');
			$filters['end_date'] = now()->create($request->target_month_date)->addMonth()->format('Y-m-01');
		}
		$chartData = $this->getChartData($filters);
		return response()->json([
			'options'=>[
				'series'=>array_values($chartData)
			]
		]);
	}
	private function getChartData($filters=[]){
		$active_system_matches = SystemMatch::select(DB::raw('COUNT(`id`) AS active_system_matches'))->where('is_connected',0);
		$connected_system_matches = SystemMatch::select(DB::raw('COUNT(`id`) AS connected_system_matches'))->where('is_connected',1);
		$expired_system_matches = SystemMatch::select(DB::raw('COUNT(`id`) AS expired_system_matches'))->where('is_connected',2);
		if(!empty($filters['start_date'])){
			$active_system_matches->where('created_at','>',$filters['start_date']);
			$connected_system_matches->where('created_at','>',$filters['start_date']);
			$expired_system_matches->where('created_at','>',$filters['start_date']);
		}
		if(!empty($filters['end_date'])){
			$active_system_matches->where('created_at','<',$filters['end_date']);
			$connected_system_matches->where('created_at','<',$filters['end_date']);
			$expired_system_matches->where('created_at','<',$filters['end_date']);
		}
		$active_system_matches = $active_system_matches->first()->active_system_matches;
		$connected_system_matches = $connected_system_matches->first()->connected_system_matches;
		$expired_system_matches = $expired_system_matches->first()->expired_system_matches;
		return compact('active_system_matches','connected_system_matches','expired_system_matches');
	}
}