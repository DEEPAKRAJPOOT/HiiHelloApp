<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminData as AdminDataModel;
use App\Models\Like;
use App\Models\DisLike;
use App\Models\User;
use Illuminate\Support\Carbon;
use DB;
use Exception;

class AdminData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:data';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'This Command is used to store admin data in table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle($override=false){
        $message = 'Admin Data updated successfully';
        $this->updateTodayRecords();
        if((date('G') % 6 == 0) || $override){
            $this->updateThisWeekRecords();
        }
        if((date('G') == '1') || $override){
            $this->updateThisMonthRecords();
        }
        if((date('G') == '2') || $override){
            $this->updateTotalRecords();
        }
        if((date('G') == '3' && date('j') == 1) || $override){
            $this->updateLastMonthRecords();
        }
        return $message;
    }

    private function updateTodayRecords(){
        $today = now()->format('Y-m-d');
        $organic_matches_today = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query)use($today){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
            $query->where(function($sub_query)use($today){
                $sub_query->where('likes.created_at','>=',$today);
                $sub_query->orWhere('like.created_at','>=',$today);
            });
        })->first()->matches;
        $organic_matches_today_record = AdminDataModel::where('name','matches_organic_today')->first();
        if(empty($organic_matches_today_record)){
            $organic_matches_today_record = new AdminDataModel;
            $organic_matches_today_record->name = 'matches_organic_today';
        }
        $organic_matches_today_record->value = floor(intval($organic_matches_today) / 2);
        $organic_matches_today_record->save();
    }

    private function updateThisWeekRecords(){
        $this_week = now()->parse('This week')->format('Y-m-d');
        $organic_matches_this_week = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query)use($this_week){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
            $query->where(function($sub_query)use($this_week){
                $sub_query->where('likes.created_at','>=',$this_week);
                $sub_query->orWhere('like.created_at','>=',$this_week);
            });
        })->first()->matches;
        $organic_matches_this_week_record = AdminDataModel::where('name','matches_organic_this_week')->first();
        if(empty($organic_matches_this_week_record)){
            $organic_matches_this_week_record = new AdminDataModel;
            $organic_matches_this_week_record->name = 'matches_organic_this_week';
        }
        $organic_matches_this_week_record->value = floor(intval($organic_matches_this_week) / 2);
        $organic_matches_this_week_record->save();
    }

    private function updateThisMonthRecords(){
        $this_month_date = now()->format('Y-m-01');
        $this_month_organic_matches_record_name = 'matches_organic_'.strtolower(now()->format('Y_M'));
        $this_month_likes_done_record_name = 'likes_'.strtolower(now()->format('Y_M'));
        $this_month_dislikes_done_record_name = 'dislikes_'.strtolower(now()->format('Y_M'));

        $organic_matches_this_month = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query)use($this_month_date){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
            $query->where(function($sub_query)use($this_month_date){
                $sub_query->where('likes.created_at','>=',$this_month_date);
                $sub_query->orWhere('like.created_at','>=',$this_month_date);
            });
        })->first()->matches;
        $organic_matches_this_month_record = AdminDataModel::where('name',$this_month_organic_matches_record_name)->first();
        if(empty($organic_matches_this_month_record)){
            $organic_matches_this_month_record = new AdminDataModel;
            $organic_matches_this_month_record->name = $this_month_organic_matches_record_name;
        }
        $organic_matches_this_month_record->value = floor(intval($organic_matches_this_month) / 2);
        $organic_matches_this_month_record->save();

        $likes_done_this_month = Like::where('created_at','>',$this_month_date)->count();
        $likes_done_this_month_record = AdminDataModel::where('name',$this_month_likes_done_record_name)->first();
        if(empty($likes_done_this_month_record)){
            $likes_done_this_month_record = new AdminDataModel;
            $likes_done_this_month_record->name = $this_month_likes_done_record_name;
        }
        $likes_done_this_month_record->value = $likes_done_this_month;
        $likes_done_this_month_record->save();

        $dislikes_done_this_month = DisLike::where('created_at','>',$this_month_date)->count();
        $dislikes_done_this_month_record = AdminDataModel::where('name',$this_month_dislikes_done_record_name)->first();
        if(empty($dislikes_done_this_month_record)){
            $dislikes_done_this_month_record = new AdminDataModel;
            $dislikes_done_this_month_record->name = $this_month_dislikes_done_record_name;
        }
        $dislikes_done_this_month_record->value = $dislikes_done_this_month;
        $dislikes_done_this_month_record->save();
    }

    private function updateLastMonthRecords(){
        $this_month_date = now()->format('Y-m-01');
        $last_month_date = now()->subMonth()->format('Y-m-01');
        $last_month_organic_matches_record_name = 'matches_organic_'.strtolower(now()->subMonth()->format('Y_M'));
        $last_month_likes_done_record_name = 'likes_'.strtolower(now()->subMonth()->format('Y_M'));
        $last_month_dislikes_done_record_name = 'dislikes_'.strtolower(now()->subMonth()->format('Y_M'));

        $organic_matches_last_month = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query)use($last_month_date,$this_month_date){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
            $query->where(function($sub_query)use($last_month_date,$this_month_date){
                $sub_query->where(function($sub_sub_query)use($last_month_date,$this_month_date){
                    $sub_sub_query->where('likes.created_at','>=',$last_month_date);
                    $sub_sub_query->where('likes.created_at','<',$this_month_date);
                    $sub_sub_query->where('like.created_at','<',$this_month_date);
                });
                $sub_query->orWhere(function($sub_sub_query)use($last_month_date,$this_month_date){
                    $sub_sub_query->where('like.created_at','>=',$last_month_date);
                    $sub_sub_query->where('like.created_at','<',$this_month_date);
                    $sub_sub_query->where('likes.created_at','<',$this_month_date);
                });
            });
        })->first()->matches;
        $organic_matches_last_month_record = AdminDataModel::where('name',$last_month_organic_matches_record_name)->first();
        if(empty($organic_matches_last_month_record)){
            $organic_matches_last_month_record = new AdminDataModel;
            $organic_matches_last_month_record->name = $last_month_organic_matches_record_name;
        }
        $organic_matches_last_month_record->value = floor(intval($organic_matches_last_month) / 2);
        $organic_matches_last_month_record->save();

        $likes_done_last_month = Like::where('created_at','>=',$last_month_date)->where('created_at','<',$this_month_date)->count();
        $likes_done_last_month_record = AdminDataModel::where('name',$last_month_likes_done_record_name)->first();
        if(empty($likes_done_last_month_record)){
            $likes_done_last_month_record = new AdminDataModel;
            $likes_done_last_month_record->name = $last_month_likes_done_record_name;
        }
        $likes_done_last_month_record->value = $likes_done_last_month;
        $likes_done_last_month_record->save();

        $dislikes_done_last_month = DisLike::where('created_at','>=',$last_month_date)->where('created_at','<',$this_month_date)->count();
        $dislikes_done_last_month_record = AdminDataModel::where('name',$last_month_dislikes_done_record_name)->first();
        if(empty($dislikes_done_last_month_record)){
            $dislikes_done_last_month_record = new AdminDataModel;
            $dislikes_done_last_month_record->name = $last_month_dislikes_done_record_name;
        }
        $dislikes_done_last_month_record->value = $dislikes_done_last_month;
        $dislikes_done_last_month_record->save();

    }

    private function updateTotalRecords(){
        $total_organic_matches = Like::select(DB::raw('COUNT(DISTINCT(`likes`.`id`)) AS matches'))->join('likes as like',function($query){
            $query->on('likes.liker_id','like.user_id');
            $query->on('like.liker_id','likes.user_id');
        })->first()->matches;
        $total_organic_matches_record = AdminDataModel::where('name','matches_organic')->first();
        if(empty($total_organic_matches_record)){
            $total_organic_matches_record = new AdminDataModel;
            $total_organic_matches_record->name = 'matches_organic';
        }
        $total_organic_matches_record->value = floor(intval($total_organic_matches) / 2);
        $total_organic_matches_record->save();
    }
}
