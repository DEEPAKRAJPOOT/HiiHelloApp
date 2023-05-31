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
		$data['active_system_matches'] = SystemMatch::select(DB::raw('COUNT(`id`) AS active_system_matches'))->where('is_connected',0)->first()->active_system_matches;
		$data['connected_system_matches'] = SystemMatch::select(DB::raw('COUNT(`id`) AS connected_system_matches'))->where('is_connected',1)->first()->connected_system_matches;
		$data['expired_system_matches'] = SystemMatch::select(DB::raw('COUNT(`id`) AS expired_system_matches'))->where('is_connected',2)->first()->expired_system_matches;
		return view('admin.pages.user-matches.index',$data)->with(['custom_title'=>'User Matches']);
	}
	public function listing(Request $request) {
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$columns = ['userTransDefault.full_name'];
		$records = [
			'data' => []
		];
		if(isset($request->order[0]['column'])){
			$sort_column = $columns[$request->order[0]['column']];
		}
		$users = User::with('userTransDefault')->withCount('likes','likes_done','dislikes','dislikes_done','unmatches','unmatches_done');
		$records['recordsTotal'] = $users->count();
		if($search != ''){
			$users->where(function($query)use($search){
				$query->whereHas('userTransDefault',function($query_translation)use($search){
					$query_translation->where('full_name','like','%'.$search.'%')->whereIsActive('y');
				});
			});
		}
		$records['recordsFiltered'] = $users->count();
		$users = $users
		->orderBy('updated_at','desc')
		->offset($offset)->limit($limit)
		->get();
		foreach($users as $user){
			$total_system_matches = $user->system_matches_for()->whereHas('to_user')->count() + $user->system_matches_to()->whereHas('for_user')->count();
			$connected_system_matches = $user->system_matches_for()->whereHas('to_user')->where('is_connected',1)->count() + $user->system_matches_to()->whereHas('for_user')->where('is_connected',1)->count();
			$records['data'][] = [
				'name'                     => $user->userTransDefault ? $user->userTransDefault->full_name : 'N/A',
				'unmatches_done'           => $user->unmatches_done_count,
				'unmatches_received'       => $user->unmatches_count,
				'likes_done'               => $user->likes_done_count,
				'dislikes_done'            => $user->dislikes_done_count,
				'likes_received'           => $user->likes_count,
				'dislikes_received'        => $user->dislikes_count,
				'system_matches'           => $total_system_matches,
				'system_matches_connected' => (!empty($total_system_matches) && !empty($connected_system_matches)) ? $connected_system_matches.' (~'.round($connected_system_matches / $total_system_matches * 100,2).'%)' : '0',
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
}