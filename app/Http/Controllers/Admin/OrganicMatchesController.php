<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AdminData;
use App\Models\ChatRoom;
use App\Models\Like;
use App\Models\DisLike;
use App\Models\SystemMatch;
use App\Models\BlockUser;
use App\Models\UnMatch;
use App\Http\Requests\Admin\OrganicMatchRequest;
use Illuminate\Database\QueryException;
use App\Jobs\NotificationJob;
use Exception;

class OrganicMatchesController extends Controller {
	public function index() {
		return view('admin.pages.organic-matches.index')->with(['custom_title'=>'Organic Matches']);
	}
	public function listing(Request $request) {
		session_write_close();
		extract($this->DTFilters($request->all()));
		DB::enableQueryLog();
		$records = [
			'data' => []
		];
		$users = User::withAggregate('userTransDefault','full_name')
		->withCount('likes','likes_done','dislikes','dislikes_done','unmatches','unmatches_done','chat_initiations');
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
		if($sort_column == 'organic_matches'){
			$sort_column = 'match_count';
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
				'organic_matches'          => $user->match_count,
				'created_at'               => now()->create($user->created_at)->format('Y-m-d').'<br><small>'.now()->create($user->created_at)->diffForHumans().'</small>',
				'action'                   => view('admin.layouts.includes.actions')->with(['custom_title'=>'User','id'=>$user->custom_id],$user)->render(),
			];
		}
		return $records;
	}
	public function show($user_id){
		return redirect()->route('admin.user-matches.show',$user_id);
	}
	public function create(){
		return view('admin.pages.organic-matches.create')->with(['custom_title' => 'Like']);
	}
	public function update(Request $request){
		$status = 500;
		$message = 'Unable to update organic matches. Please try again';
		ini_set('max_execution_time',3600);
		set_time_limit(3600);
		try{
			(new \App\Console\Commands\AdminData)->handle(true);
			$status = 200;
			$message = 'Organic matches updated successfully!';
			flash($message)->success();
		}catch(Exception $e){
			$status = 500;
			$message = 'Unable to update organic matches. Error: '.$e->getMessage();
			flash($message)->error();
		}
		if($request->expectsJson()){
			return response()->json(['message'=>$message],$status);
		}
		return redirect()->route('admin.organic-matches.index');
	}
	public function store(OrganicMatchRequest $request){
		try{
			if($request->liked_by_user == $request->target_user){
				return redirect()->back()->with('error','Sender and Receiver cannot be same.');
			}
			$liked_by_user = User::whereCustomId($request->liked_by_user)->first();
			$target_user = User::whereCustomId($request->target_user)->first();
			if(empty($liked_by_user) || empty($target_user)){
				return redirect()->back()->with('error','Unable to add like. Please Try again');
			}
			DB::beginTransaction();
			$block = BlockUser::select('id')->whereBlockBy($target_user->id)->whereBlockedTo($liked_by_user->id)->exists();
			if(!empty($block)){
				return redirect()->back()->with('error','Receiver has blocked the Sender');
			}
			$like = Like::firstOrCreate([
				'user_id'   =>  $target_user->id,
				'liker_id'  =>  $liked_by_user->id,
			], [
				'custom_id' =>  getUniqueString('likes'),
			]);
			DisLike::whereUserId($target_user->id)->whereDisLikerId($liked_by_user->id)->delete();
			if(!empty($request->create_match)){
				$block_back = BlockUser::select('id')->whereBlockBy($liked_by_user->id)->whereBlockedTo($target_user->id)->exists();
				if(!empty($block_back)){
					return redirect()->back()->with('error','Sender has blocked the Receiver');
				}
				$like_back = Like::firstOrCreate([
					'user_id'  =>  $liked_by_user->id,
					'liker_id' =>  $target_user->id,
				], [
					'custom_id' =>  getUniqueString('likes'),
				]);
				DisLike::whereUserId($liked_by_user->id)->whereDisLikerId($target_user->id)->delete();
				if($like_back->save()){
					if($like_back->wasRecentlyCreated){
						$liked_by_user->increment('like_count');
						$liker_notification_title = trans('api.notify_message.add_like.title');
						$liker_notification_message = trans('api.notify_message.add_like.message');
						$liker_notification_type = config('utility.notification.type.add_like');
					}
				}else{
					return redirect()->back()->with('error','Some Unknown error occured');
				}
			}
			if($like->save()){
				if($like->wasRecentlyCreated){
					$target_user->increment('like_count');
					$target_notification_title = trans('api.notify_message.add_like.title');
					$target_notification_message = trans('api.notify_message.add_like.message');
					$target_notification_type = config('utility.notification.type.add_like');
				}
			}else{
				return redirect()->back()->with('error','Some Unknown error occured');
			}
			$matched = !empty($request->create_match) || Like::whereUserId($liked_by_user->id)->whereLikerId($target_user->id)->exists();
			if($matched && ($like->wasRecentlyCreated || (!empty($like_back) && $like_back->wasRecentlyCreated))){
				$liked_by_user->increment('match_count');
				$target_user->increment('match_count');
				UnMatch::whereUnmatchBy($liked_by_user->id)->whereUnmatchTo($target_user->id)->delete();
				UnMatch::whereUnmatchBy($target_user->id)->whereUnmatchTo($liked_by_user->id)->delete();
				$target_notification_title = $liker_notification_title = trans('api.notify_message.new_match.title');
				$target_notification_message = $liker_notification_message = trans('api.notify_message.new_match.message');
				$target_notification_type = $liker_notification_type = config('utility.notification.type.new_match');
			}
			if(!empty($target_notification_title)){
				$target_notification = [
					'custom_id'     =>  getUniqueString('notifications'),
					'key'           =>  'user_id',
					'value'         =>  $liked_by_user->id,
					'user_id'       =>  $target_user->id,
					'title'         =>  $target_notification_title,
					'message'       =>  $target_notification_message,
					'image'         =>  '',
					'type'          =>  $target_notification_type,
				];
				$target_notificationJob = new NotificationJob($target_notification,$target_user);
				dispatch($target_notificationJob);
			}
			if(!empty($liker_notification_title)){
				$liker_notification = [
					'custom_id'     =>  getUniqueString('notifications'),
					'key'           =>  'user_id',
					'value'         =>  $target_user->id,
					'user_id'       =>  $liked_by_user->id,
					'title'         =>  $liker_notification_title,
					'message'       =>  $liker_notification_message,
					'image'         =>  '',
					'type'          =>  $liker_notification_type,
				];
				$liker_notificationJob = new NotificationJob($liker_notification,$liked_by_user);
				dispatch($liker_notificationJob);
			}			
			DB::commit();
			flash('Like created successfully!')->success();
			return redirect()->route('admin.organic-matches.index');
		}catch (QueryException $e) {
			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage());
		}catch(Exception $e){
			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage());
		}
	}
	public function reportingData(){
		$current_month_date = strtolower(now()->format('Y_M'));
		$total_organic_matches = AdminData::where('name','matches_organic')->first();
		if(!empty($total_organic_matches)){
			$total_organic_matches = $total_organic_matches->value;
		}else{
			$total_organic_matches = 0;
		}

		$organic_matches_this_month = AdminData::where('name','matches_organic_'.$current_month_date)->first();
		if(!empty($organic_matches_this_month)){
			$organic_matches_this_month = $organic_matches_this_month->value;
		}else{
			$organic_matches_this_month = 0;
		}

		$organic_matches_this_week = AdminData::where('name','matches_organic_this_week')->first();
		if(!empty($organic_matches_this_week)){
			$organic_matches_this_week = $organic_matches_this_week->value;
		}else{
			$organic_matches_this_week = 0;
		}

		$organic_matches_today = AdminData::where('name','matches_organic_today')->first();
		if(!empty($organic_matches_today)){
			$organic_matches_today = $organic_matches_today->value;
		}else{
			$organic_matches_today = 0;
		}
		return response()->json(['success'=>true,'data'=>compact('total_organic_matches','organic_matches_this_month','organic_matches_this_week','organic_matches_today')]);
	}
	public function tableData(Request $request){
		$target_month_date = strtolower(now()->create($request->target_month_date)->format('Y_M'));

		$monthly_likes_done_data = AdminData::where('name','likes_'.$target_month_date)->first();
		if(empty($monthly_likes_done_data)){
			$monthly_likes_done_data = $this->getMonthlyLikes($request->target_month_date);
		}
		$monthly_likes_done = $monthly_likes_done_data->value;

		$monthly_dislikes_done_data = AdminData::where('name','dislikes_'.$target_month_date)->first();
		if(empty($monthly_dislikes_done_data)){
			$monthly_dislikes_done_data = $this->getMonthlyDisLikes($request->target_month_date);
		}
		$monthly_dislikes_done = $monthly_dislikes_done_data->value;

		$monthly_organic_matches_data = AdminData::where('name','matches_organic_'.$target_month_date)->first();
		if(empty($monthly_organic_matches_data)){
			$monthly_organic_matches_data = $this->getMonthlyMatches($request->target_month_date);
		}
		$monthly_organic_matches = $monthly_organic_matches_data->value;

		return response()->json(['success'=>true,'data'=>compact('monthly_likes_done','monthly_dislikes_done','monthly_organic_matches')]);
	}
	private function getMonthlyLikes($target_month_date){
		$target_date_string = strtolower(now()->create($target_month_date)->format('Y_M'));
		$target_next_month_date = now()->create($target_month_date)->addMonth()->format('Y-m-01');
		$likes_done_target_month_record_name = 'likes_'.$target_date_string;
		$likes_done_target_month = Like::where('created_at','>=',$target_month_date)->where('created_at','<',$target_next_month_date)->count();
        $likes_done_target_month_record = AdminData::where('name',$likes_done_target_month_record_name)->first();
        if(empty($likes_done_target_month_record)){
            $likes_done_target_month_record = new AdminData;
            $likes_done_target_month_record->name = $likes_done_target_month_record_name;
        }
        $likes_done_target_month_record->value = $likes_done_target_month;
        $likes_done_target_month_record->save();
        return $likes_done_target_month_record;
	}
	private function getMonthlyDisLikes($target_month_date){
		$target_date_string = strtolower(now()->create($target_month_date)->format('Y_M'));
		$target_next_month_date = now()->create($target_month_date)->addMonth()->format('Y-m-01');
		$dislikes_done_target_month_record_name = 'dislikes_'.$target_date_string;
		$dislikes_done_target_month = DisLike::where('created_at','>=',$target_month_date)->where('created_at','<',$target_next_month_date)->count();
        $dislikes_done_target_month_record = AdminData::where('name',$dislikes_done_target_month_record_name)->first();
        if(empty($dislikes_done_target_month_record)){
            $dislikes_done_target_month_record = new AdminData;
            $dislikes_done_target_month_record->name = $dislikes_done_target_month_record_name;
        }
        $dislikes_done_target_month_record->value = $dislikes_done_target_month;
        $dislikes_done_target_month_record->save();
        return $dislikes_done_target_month_record;
	}
	private function getMonthlyMatches($target_month_date){
		$target_date_string = strtolower(now()->create($target_month_date)->format('Y_M'));
		$target_next_month_date = now()->create($target_month_date)->addMonth()->format('Y-m-01');
		$organic_matches_target_month_record_name = 'matches_organic_'.$target_date_string;
		$organic_matches_target_month = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query)use($target_month_date,$target_next_month_date){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
            $query->where(function($sub_query)use($target_month_date,$target_next_month_date){
                $sub_query->where(function($sub_sub_query)use($target_month_date,$target_next_month_date){
                    $sub_sub_query->where('likes.created_at','>=',$target_month_date);
                    $sub_sub_query->where('likes.created_at','<',$target_next_month_date);
                    $sub_sub_query->where('like.created_at','<',$target_next_month_date);
                });
                $sub_query->orWhere(function($sub_sub_query)use($target_month_date,$target_next_month_date){
                    $sub_sub_query->where('like.created_at','>=',$target_month_date);
                    $sub_sub_query->where('like.created_at','<',$target_next_month_date);
                    $sub_sub_query->where('likes.created_at','<',$target_next_month_date);
                });
            });
        })->first()->matches;
        $organic_matches_target_month_record = AdminData::where('name',$organic_matches_target_month_record_name)->first();
        if(empty($organic_matches_target_month_record)){
            $organic_matches_target_month_record = new AdminData;
            $organic_matches_target_month_record->name = $organic_matches_target_month_record_name;
        }
        $organic_matches_target_month_record->value = floor(intval($organic_matches_target_month) / 2);
        $organic_matches_target_month_record->save();
        return $organic_matches_target_month_record;
	}
}