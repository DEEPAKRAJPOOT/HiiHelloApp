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

    protected $description = 'This Command used to analytic dashboard store data in table';

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
        $past_30_day_date   =    Carbon::today()->subDays(30);

        $total_users = User::whereNull('deleted_at')->count();
        $male_users = User::where('gender', '=', 'Male')->whereNull('deleted_at')->count();
        $female_users = User::where('gender', '=', 'Female')->whereNull('deleted_at')->count();
        $na_users = User::whereNull('gender')->whereNull('deleted_at')->count();
        $total_subscribed = User::where('gender', '=', 'Male')->where('is_subscribed', '=', 'y')->whereNull('deleted_at')->count();
        $total_unsubscribed = User::where('gender', '=', 'Male')->where('is_subscribed', '!=', 'y')->whereNull('deleted_at')->count();
        $per_day_users = User::whereNull('deleted_at')->whereDate("created_at", Carbon::today())->count();
        $per_week_users = User::whereNull('deleted_at')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $per_30_day_users = User::whereNull('deleted_at')->where("created_at", ">=", $past_30_day_date)->count();

        $all_users = User::select('birth_date', 'gender', 'facebook_id', 'google_id', 'apple_id', 'is_social_user')->whereNull('deleted_at')->whereNotNull('birth_date')->whereNotNull('gender')->get();
        // echo "<pre>"; print_r($all_users->toArray()); die();
        $male_age_18_25   = 0;
        $male_age_26_35   = 0;
        $male_age_36_45   = 0;
        $male_age_45      = 0;
        $female_age_18_25 = 0;
        $female_age_26_35 = 0;
        $female_age_36_45 = 0;
        $female_age_45    = 0;

        if (count($all_users) > 0) {
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

        $all_user_list = User::select(
            'facebook_id',
            'google_id',
            'apple_id',
            'is_social_user',
            'gender',
            'contact_verified_at',
            'email_verified_at',
            'verify_photo_status',
            'verify_status',
            'otp_less_id'
        )
            ->whereNull('deleted_at')
            ->get();
        $is_phone_user = 0;
        $is_google_user = 0;
        $is_facebook_user = 0;
        $is_apple_user = 0;
        $is_otp_less_user = 0;

        $male_phone_verified = 0;
        $male_phone_unverified = 0;
        $male_email_verified = 0;
        $male_email_unverified = 0;
        $male_photo_verified = 0;
        $male_photo_unverified = 0;
        $male_account_verified = 0;
        $male_account_unverified = 0;

        $female_phone_verified = 0;
        $female_phone_unverified = 0;
        $female_email_verified = 0;
        $female_email_unverified = 0;
        $female_photo_verified = 0;
        $female_photo_unverified = 0;
        $female_account_verified = 0;
        $female_account_unverified = 0;
        if (count($all_user_list) > 0) {
            foreach ($all_user_list as $key => $val) {

                // mode of reg.
                if (!empty($val->facebook_id) && $val->is_social_user == 'y') {
                    $is_facebook_user += 1;
                }
                if (!empty($val->google_id) && $val->is_social_user == 'y') {
                    $is_google_user += 1;
                }
                if (!empty($val->apple_id) && $val->is_social_user == 'y') {
                    $is_apple_user += 1;
                }
                if (empty($val->facebook_id) && empty($val->google_id) && empty($val->apple_id) && $val->is_social_user == 'n') {
                    $is_phone_user += 1;
                }

                if (!empty($val->otp_less_id)) {
                    $is_otp_less_user += 1;
                }
                // verified-unverified 
                // phone
                if (!empty($val->gender) && !empty($val->contact_verified_at) && $val->gender == 'Male') {
                    $male_phone_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->contact_verified_at) && $val->gender == 'Female') {
                    $female_phone_verified += 1;
                }
                if (!empty($val->gender) && empty($val->contact_verified_at) && $val->gender == 'Male') {
                    $male_phone_unverified += 1;
                }
                if (!empty($val->gender) && empty($val->contact_verified_at) && $val->gender == 'Female') {
                    $female_phone_unverified += 1;
                }

                // email
                if (!empty($val->gender) && !empty($val->email_verified_at) && $val->gender == 'Male') {
                    $male_email_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->email_verified_at) && $val->gender == 'Female') {
                    $female_email_verified += 1;
                }
                if (!empty($val->gender) && empty($val->email_verified_at) && $val->gender == 'Male') {
                    $male_email_unverified += 1;
                }
                if (!empty($val->gender) && empty($val->email_verified_at) && $val->gender == 'Female') {
                    $female_email_unverified += 1;
                }

                // photo
                if (!empty($val->gender) && !empty($val->verify_photo_status) && $val->gender == 'Male' && $val->verify_photo_status == 'verified') {
                    $male_photo_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_photo_status) && $val->gender == 'Female' && $val->verify_photo_status == 'verified') {
                    $female_photo_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_photo_status) && $val->gender == 'Male' && $val->verify_photo_status == 'unverified') {
                    $male_photo_unverified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_photo_status) && $val->gender == 'Female' && $val->verify_photo_status == 'unverified') {
                    $female_photo_unverified += 1;
                }

                // profile
                if (!empty($val->gender) && !empty($val->verify_status) && $val->gender == 'Male' && $val->verify_status == 'verified') {
                    $male_account_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_status) && $val->gender == 'Female' && $val->verify_status == 'verified') {
                    $female_account_verified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_status) && $val->gender == 'Male' && $val->verify_status == 'unverified') {
                    $male_account_unverified += 1;
                }
                if (!empty($val->gender) && !empty($val->verify_status) && $val->gender == 'Female' && $val->verify_status == 'unverified') {
                    $female_account_unverified += 1;
                }
            }
        }
        $created_at         = date("Y-m-d H:i:s");
        cache()->forget('oldest-record'); //forget cache recorde change on development
        $old_date = cache()->rememberForever('oldest-record', function () {
            return User::selectRaw('created_at')->orderBy('created_at', 'asc')->first();
        });

        $insert_data = array(
            'total_users' => $total_users,
            "per_day_users" => $per_day_users,
            "per_week_users" => $per_week_users,
            "per_30_day_users" => $per_30_day_users,
            "male_users" => $male_users,
            "female_users" => $female_users,
            "na_users" => $na_users,
            "male_18_25" => $male_age_18_25,
            "male_26_35" => $male_age_26_35,
            "male_36_45" => $male_age_36_45,
            "male_45" => $male_age_45,
            "female_18_25" => $female_age_18_25,
            "female_26_35" => $female_age_26_35,
            "female_36_45" => $female_age_36_45,
            "female_45" => $female_age_45,
            "paid_users" => $total_subscribed,
            "non_paid_users" => $total_unsubscribed,
            "total_phone_users" => $is_phone_user,
            "total_google_users" => $is_google_user,
            "total_facebook_users" => $is_facebook_user,
            "total_apple_users" => $is_apple_user,
            "total_otp_less_users" => $is_otp_less_user,
            "male_phone_verified" => $male_phone_verified,
            "male_phone_unverified" => $male_phone_unverified,
            "male_email_verified" => $male_email_verified,
            "male_email_unverified" => $male_email_unverified,
            "male_photo_verified" => $male_photo_verified,
            "male_photo_unverified" => $male_photo_unverified,
            "male_account_verified" => $male_account_verified,
            "male_account_unverified" => $male_account_unverified,
            "female_phone_verified" => $female_phone_verified,
            "female_phone_unverified" => $female_phone_unverified,
            "female_email_verified" => $female_email_verified,
            "female_email_unverified" => $female_email_unverified,
            "female_photo_verified" => $female_photo_verified,
            "female_photo_unverified" => $female_photo_unverified,
            "female_account_verified" => $female_account_verified,
            "female_account_unverified" => $female_account_unverified,
            "created_at" => $created_at
        );
        // echo "<pre>"; print_r($insert_data); die();
        DB::table('analytic_dashboard')->insert($insert_data);
        $message            = "Analytic dashboard data update successfully.";
        return $message;
    }

    public function age_check($dateOfBirth)
    {
        // $dateOfBirth = "30-06-1996";
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        // echo 'Your age is '.$diff->format('%y');
        return $diff->format('%y');
    }
}
