<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CollegeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SystemMatch;

class UserMatchesController extends Controller {
	public function index() {
		return view('admin.pages.user-matches.index',$this->getChartData())->with(['custom_title'=>'User Matches']);
	}
	public function listing(Request $request) {
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$records = [
			'data' => []
		];
		$users = User::withAggregate('userTransDefault','full_name')
		->withCount('likes','likes_done','dislikes','dislikes_done','unmatches','unmatches_done');
		$records['recordsTotal'] = $users->count();
		if($search != ''){
			$users->where(function($query)use($search){
				$query->whereHas('userTransDefault',function($query_translation)use($search){
					$query_translation->where('full_name','like','%'.$search.'%')->whereIsActive('y');
				});
			});
		}
		if($request->filled('gender_filter')) {
            $users->where('gender',$request->gender_filter);
        }
        if($sort_column == 'full_name'){
        	$sort_column = 'user_trans_default_full_name';
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
				'unmatches_done'           => $user->unmatches_done_count,
				'unmatches_received'       => $user->unmatches_count,
				'likes_done'               => $user->likes_done_count,
				'dislikes_done'            => $user->dislikes_done_count,
				'likes_received'           => $user->likes_count,
				'dislikes_received'        => $user->dislikes_count,
				'system_matches'           => $total_system_matches,
				'system_matches_connected' => (!empty($total_system_matches) && !empty($connected_system_matches)) ? $connected_system_matches.' (~'.round($connected_system_matches / $total_system_matches * 100,2).'%)' : '0',
				'created_at'               => now()->create($user->created_at)->format('Y-m-d').'<br><small>'.now()->create($user->created_at)->diffForHumans().'</small>',
				'action'                   => view('admin.layouts.includes.actions')->with(['custom_title'=>'User','id'=>$user->custom_id],$user)->render(),
			];
		}
		return $records;
	}
	public function show($user_id){
		$user = User::with('userTransDefault')
		->withCount('likes','likes_done','dislikes','dislikes_done','unmatches','unmatches_done')
		->whereCustomId($user_id)->firstOrFail();
		return view('admin.pages.user-matches.view',compact('user'))->with(['custom_title'=>'User Match Details']);
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