<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CollegeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class UserMatchesController extends Controller {
	public function index() {
		return view('admin.pages.user-matches.index')->with(['custom_title'=>'User Matches']);
	}
	public function listing(Request $request) {
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$columns = ['user_translations.full_name', 'system_matches', 'likes_done', 'dislikes_done', 'likes_received', 'dislikes_received'];
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
				$query->where('user_translations.full_name','like',"%{$search}%");
			});
		}
		$records['recordsFiltered'] = $users->count();
		$users = $users
		->offset($offset)->limit($limit)
		->get();
		foreach($users as $user){
			$records['data'][] = [
				'name'               => $user->userTransDefault ? $user->userTransDefault->full_name : 'N/A',
				'unmatches_done'     => $user->unmatches_done_count,
				'unmatches_received' => $user->unmatches_count,
				'likes_done'         => $user->likes_done_count,
				'dislikes_done'      => $user->dislikes_done_count,
				'likes_received'     => $user->likes_count,
				'dislikes_received'  => $user->dislikes_count,
				'action'             => view('admin.layouts.includes.actions')->with(['custom_title'=>'User','id'=>$user->custom_id],$user)->render(),
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