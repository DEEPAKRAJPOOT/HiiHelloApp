<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Carbon;
use DB;

class AdminDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command used to analytic dashboard store data in table ';

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
        $message            =   "No Analytic dashboard records found.";
        $total_users = User::whereNull('deleted_at')->count();
        $male_users = User::where('gender','=','Male')->whereNull('deleted_at')->count();
        $female_users = User::where('gender','=','Female')->whereNull('deleted_at')->count();
        $na_users = User::whereNull('gender')->whereNull('deleted_at')->count();
        $total_subscribed = User::where('gender','=','Male')->where('is_subscribed','=','y')->whereNull('deleted_at')->count();
        $total_unsubscribed = User::where('gender','=','Male')->where('is_subscribed','!=','y')->whereNull('deleted_at')->count();

        $all_users = User::select('birth_date','gender')->whereNull('deleted_at')->whereNotNull('birth_date')->whereNotNull('gender')->get();
        // echo "<pre>"; print_r($all_users->toArray()); die();
        $male_age_18_25   = 0;
        $male_age_26_35   = 0;
        $male_age_36_45   = 0;
        $male_age_45      = 0;
        $female_age_18_25 = 0;
        $female_age_26_35 = 0;
        $female_age_36_45 = 0;
        $female_age_45    = 0;
        if(count($all_users) > 0){
            foreach ($all_users as $key => $val) {
                $age_check = $this->age_check($val->birth_date);
                // echo $age_check; echo "<br>";
                if ($val->gender == "Male") {
                    if ($age_check >= 18 && $age_check <= 25) {
                        $male_age_18_25 += 1;
                    }
                    if ($age_check >= 26 && $age_check <= 35) {
                        $male_age_26_35 += 1;
                    }
                    if ($age_check >= 36 && $age_check <= 45) {
                        $male_age_36_45 += 1;
                    }
                    if ($age_check >= 46) {
                        $male_age_45 += 1;
                    }
                }

                if ($val->gender == "Female") {
                    if ($age_check >= 18 && $age_check <= 25) {
                        $female_age_18_25 += 1;
                    }
                    if ($age_check >= 26 && $age_check <= 35) {
                        $female_age_26_35 += 1;
                    }
                    if ($age_check >= 36 && $age_check <= 45) {
                        $female_age_36_45 += 1;
                    }
                    if ($age_check >= 46) {
                        $female_age_45 += 1;
                    }
                }
            }
            
        }

        $created_at         = date("Y-m-d H:i:s");
        cache()->forget('oldest-record'); //forget cache recorde change on development
        $old_date = cache()->rememberForever('oldest-record', function () {
            return User::selectRaw('created_at')->orderBy('created_at', 'asc')->first();
        });

        if (isset($old_date->created_at)) {
            $startDate = Carbon::parse($old_date->created_at)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $diffInDays = $startDate->diffInDays($endDate);

            //diffInDays same date return 0 day and if date 4 and 5 diffInDays return 1 day
            $diffInDays = $diffInDays + 1;
            $per_day_users = $diffInDays >= 1 ? floor(($total_users / $diffInDays)) : 0; //Per Day Register User
            $per_week_users = $diffInDays >= 7 ? floor(($total_users / ($diffInDays / 7))) : 0; //Per Week Register User
            $per_30_day_users = $diffInDays >= 30 ? floor(($total_users / ($diffInDays / 30))) : 0; //Per 30 Day Register User
        }

        $insert_data = array(
            'total_users'=>$total_users,
            "per_day_users"=>$per_day_users,
            "per_week_users"=>$per_week_users,
            "per_30_day_users"=>$per_30_day_users,
            "male_users"=>$male_users,
            "female_users"=>$female_users,
            "na_users"=>$na_users,
            "male_18_25"=>$male_age_18_25,
            "male_26_35"=>$male_age_26_35,
            "male_36_45"=>$male_age_36_45,
            "male_45"=>$male_age_45,
            "female_18_25"=>$female_age_18_25,
            "female_26_35"=>$female_age_26_35,
            "female_36_45"=>$female_age_36_45,
            "female_45"=>$female_age_45,
            "paid_users"=>$total_subscribed,
            "non_paid_users"=>$total_unsubscribed,
            "created_at"=>$created_at
        );
         // echo "<pre>"; print_r($insert_data); die();
        DB::table('analytic_dashboard')->insert($insert_data);
        $message            = "Analytic dashboard data update successfully.";
        return $message;
    }

    public function age_check($dateOfBirth){
        // $dateOfBirth = "30-06-1996";
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        // echo 'Your age is '.$diff->format('%y');
        return $diff->format('%y');
    }
}
