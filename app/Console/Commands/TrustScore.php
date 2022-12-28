<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Facades\DB;


class TrustScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trust_score:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $message = 'Trusted Score Updated Successfully !!!';


        User::select('id','custom_id','email','birth_date','verify_photo_status','verify_email_send','email_verified_at','contact_verified_at')
                ->with(['userTransEn:id,user_id,full_name'])
                ->where('id',14591)
                ->chunk(100, function($users) {
                        if($users->isNotEmpty())
                        {

                            $pre_month_start_date = date('Y-m-d', strtotime('first day of last month'));
                            $pre_month_end_date = date('Y-m-d', strtotime('last day of last month'));

                            foreach($users as $user)
                            {
                                $Trusted_Score_Total = 0;

                                // Verification Status Point Calculation Start
                                $Total_Verification_Point = $this->total_verification_point($user);
                                $Trusted_Score_Total = $Trusted_Score_Total + $Total_Verification_Point;
                                // Verification Status Point Calculation End

                                // Profile %  Point Calculation Start
                                $profile_percent = $user->calculateProfilePercent();

                                $Total_Profile_Point = 0;


                                if($profile_percent < 20)
                                {
                                    $Total_Profile_Point = 1;
                                }
                                else if($profile_percent >= 20 && $profile_percent <= 50)
                                {
                                    $Total_Profile_Point = 2;
                                }
                                else if($profile_percent > 50)
                                {
                                    $Total_Profile_Point = 3;
                                }

                                $Trusted_Score_Total = $Trusted_Score_Total + $Total_Profile_Point;
                                // Profile %  Point Calculation End

                                // Total Like Recieved Calculation Start
                                $Total_Like_Point = $this->total_like_received_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Like_Point > 10)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total + 1;
                                }
                                // Total Like Recieved Calculation End

                                // Total Organic Match Calculation Start    
                                $Total_Organic_Match_Count = $this->total_organic_match_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Organic_Match_Count > 5)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total + 2;
                                }    
                                // Total Organic Match Calculation End  

                                // Total Chat Initiate Calculation Start    
                                $Total_Chat_Initiate_Count = $this->total_chat_initiate_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Chat_Initiate_Count > 10)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total + 1;
                                }
                                // Total Chat Initiate Calculation End    

                                //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++//
                                //============= POINT DEDUCTION CALCULATION START================// 
                                //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++//

                                // Total Profile Reported Calculation Start    
                                $Total_Profile_Reported_Count = $this->total_profile_reported_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Profile_Reported_Count > 0)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total - $Total_Profile_Reported_Count;
                                }
                                
                                // Total Block User Calculation Start  
                                $Total_Block_Count = $this->total_block_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Block_Count > 5)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total - 3;
                                }
                                // Total Block User Calculation End  


                                 // Total Block User Calculation Start  
                                $Total_Photo_Rejection_Count = $this->photo_rejection_point($user->id,$pre_month_start_date,$pre_month_end_date);
                                if($Total_Photo_Rejection_Count > 0)
                                {
                                    $Trusted_Score_Total = $Trusted_Score_Total - ($Total_Photo_Rejection_Count * 2);
                                }
                                // Total Block User Calculation End  

                                //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++//
                                //============= UPDATE USER SCROTE TO USER TABLE=================// 
                                //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++//                                

                                 //Update User Score Start
                                $user->trusted_score = $Trusted_Score_Total;
                                $user->trusted_score_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $user->save();

                                echo "\n\rUser Id ".$user->id;
                                echo "\n\rVerification Point ".$Total_Verification_Point;
                                echo "\n\rProfile Complete Point ".$Total_Profile_Point;
                                echo "\n\rLike Point Count (10 > Then 1) ".$Total_Like_Point;
                                echo "\n\rOrganic Match Point Count (5 > Then 2) ".$Total_Organic_Match_Count;
                                echo "\n\rChat Initiat Point Count (10 > Then 1) ".$Total_Chat_Initiate_Count;
                                echo "\n\r-----------------------------";
                                echo "\n\rProfile Reported Point ".$Total_Profile_Reported_Count;
                                echo "\n\rBlock Report Point  (5 > Then 3) ".$Total_Block_Count;
                                echo "\n\rPhoto Rejection Point ".($Total_Photo_Rejection_Count * 2);
                                echo "\n\r-----------------------------";
                                echo "\n\rTrusted Score Point ".$Trusted_Score_Total;

                                // Email                                
                                exit;
                            }
                        }
                });
        
        return $message;
    }

    public function photo_rejection_point($userid,$pre_month_start_date,$pre_month_end_date)
    {        
        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $photo_reject_count = DB::table('image_moderation_log')                    
                    ->where("user_id", '=', $userid)                //  To only get users details who likes current user
                    ->where("is_approved", '=', 0)
                    ->whereBetween('created_at', [$from_date, $to_date])->count();
                    

        return $photo_reject_count;            
    }


    public function total_block_point($userid,$pre_month_start_date,$pre_month_end_date)
    {        
        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $block_count = DB::table('block_users')                    
                    ->where("blocked_to", '=', $userid)                //  To only get users details who likes current user
                    ->whereBetween('created_at', [$from_date, $to_date])->count();
                    

        return $block_count;            
    }


    public function total_profile_reported_point($userid,$pre_month_start_date,$pre_month_end_date)
    {        
        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $profile_reported_count = DB::table('profile_reports')                    
                    ->where("reported_user_id", '=', $userid)                //  To only get users details who likes current user
                    ->whereBetween('created_at', [$from_date, $to_date])->count();
                    

        return $profile_reported_count;            
    }

    public function total_chat_initiate_point($userid,$pre_month_start_date,$pre_month_end_date)
    {
        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $chat_initi_by_user = DB::table('chat_rooms')                    
                    ->where("creator_id", '=', $userid)                //  To only get users details who likes current user
                    ->whereBetween('created_at', [$from_date, $to_date])->count();
                    

        return $chat_initi_by_user;            
    }


    public function total_organic_match_point($userid,$pre_month_start_date,$pre_month_end_date)
    {
        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $organic_match = DB::table('likes')
                    ->join("likes as like", function ($q) {
                        $q->on("likes.liker_id", "=", "like.user_id");
                        $q->on("like.liker_id", "=", "likes.user_id");
                    })
                    ->join('users', function ($q) {
                        $q->on('users.id', "=", "likes.user_id");
                    })
                    ->where("likes.liker_id", '=', $userid)                //  To only get users details who likes current user
                    ->where("likes.user_id", '!=', $userid)
                    ->whereBetween('like.created_at', [$from_date, $to_date])->count();

        return $organic_match;            
    }

    public function total_like_received_point($userid,$pre_month_start_date,$pre_month_end_date)
    {

        $from_date         = $pre_month_start_date." 00:00:00";
        $to_date           = $pre_month_end_date." 23:59:59";

        $total_like_received = Like::where("user_id",$userid)->whereBetween('created_at', [$from_date, $to_date])->count();
        return $total_like_received;
    }

    public function total_verification_point($user)
    {  
        $tmp_verification_point = 0;

        //echo $user->verify_photo_status;

        if($user->emailVerifyStatus()=='verified')
        {
            $tmp_verification_point++;
        }

        if($user->contactVerifyStatus()=='verified')
        {
            $tmp_verification_point++;
        }

        if($user->verify_photo_status=='verified')
        {
            $tmp_verification_point++;
        }

        return $tmp_verification_point;
    }
}

