<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserMatchesController extends Controller {
	public function index() {
		return redirect()->route('admin.system-matches.index');
	}
	public function show($user_id){
		$user = User::with('userTransDefault')
		->withCount('likes','likes_done','dislikes','dislikes_done','unmatches','unmatches_done','chat_initiations')
		->whereCustomId($user_id)->firstOrFail();
		return view('admin.pages.user-matches.view',compact('user'))->with(['custom_title'=>'User Match Details']);
	}
}