<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,Response};
use App\Models\DashboardData;

class DashboardController extends Controller
{
    public function edit(){
        return view('admin.pages.dashboard.edit')->with(['custom_title' => 'Dashboard']);
    }

    public function update(Request $request){
        $data = [
            'success' => true
        ];
        $response_code = Response::HTTP_OK;
        $requested_name = $request->name;
        $requested_value = $request->value;
        if(!empty($requested_name) && !empty($requested_value)){
            $year_month_string = strtolower(now()->create($requested_value)->format('Y_M'));
            switch($requested_name){
                case 'app_traction':
                    $downloads_key_name = 'users_downloads_'.$year_month_string;
                    $uninstalls_key_name = 'users_uninstalls_'.$year_month_string;
                    $percentages_key_name = 'users_percentages_'.$year_month_string;
                    $update_data[$downloads_key_name.'_organic'] = $this->isValid($request->users_downloads_organic) ? intval($request->users_downloads_organic) : '';
                    $update_data[$downloads_key_name.'_paid'] = $this->isValid($request->users_downloads_paid) ? intval($request->users_downloads_paid) : '';
                    $update_data[$downloads_key_name.'_referrals'] = $this->isValid($request->users_downloads_referrals) ? intval($request->users_downloads_referrals) : '';
                    $total_downloads = intval($update_data[$downloads_key_name.'_organic']) + intval($update_data[$downloads_key_name.'_paid']) + intval($update_data[$downloads_key_name.'_referrals']);
                    $male_percentage = intval($request->users_percentage ?? 0);
                    $female_percentage = 100 - $male_percentage;
                    $update_data['males_users_downloads_'.$year_month_string] = round(($male_percentage / 100) * $total_downloads);
                    $update_data['females_users_downloads_'.$year_month_string] = $total_downloads - $update_data['males_users_downloads_'.$year_month_string];
                    $update_data[$uninstalls_key_name] = $this->isValid($request->users_uninstalls) ? intval($request->users_uninstalls) : '';
                    $update_data[$percentages_key_name.'_male'] = $male_percentage;
                    $update_data[$percentages_key_name.'_female'] = $female_percentage;
                break;
                case 'app_engagement':
                    $daily_time_key_name = 'users_daily_time_'.$year_month_string;
                    $logins_key_name = 'users_logins_'.$year_month_string;
                    $active_users_key_name = 'users_active_'.$year_month_string;
                    $notifications_sent_key_name = 'notifications_sent_'.$year_month_string;
                    $notifications_clicked_percentages_key_name = 'notifications_clicked_percentages_'.$year_month_string;
                    $update_data[$daily_time_key_name.'_male_subscribers'] = $this->isValid($request->daily_time_male_subscribers) ? $request->daily_time_male_subscribers : '';
                    $update_data[$daily_time_key_name.'_male_non_subscribers'] = $this->isValid($request->daily_time_male_non_subscribers) ? $request->daily_time_male_non_subscribers : '';
                    $update_data[$daily_time_key_name.'_female_users'] = $request->daily_time_female_users ?? '';
                    $update_data[$logins_key_name.'_male_subscribers'] = $request->logins_male_subscribers ?? '';
                    $update_data[$logins_key_name.'_male_non_subscribers'] = $request->logins_male_non_subscribers ?? '';
                    $update_data[$logins_key_name.'_female_users'] = $request->logins_female_users ?? '';
                    $update_data[$active_users_key_name.'_daily'] = $this->isValid($request->active_users_daily) ? intval($request->active_users_daily) : '';
                    $update_data[$active_users_key_name.'_monthly'] = $this->isValid($request->active_users_monthly) ? intval($request->active_users_monthly) : '';
                    $update_data[$notifications_sent_key_name.'_email'] = $this->isValid($request->notifications_sent_email) ? intval($request->notifications_sent_email) : '';
                    $update_data[$notifications_sent_key_name.'_sms'] = $this->isValid($request->notifications_sent_sms) ? intval($request->notifications_sent_sms) : '';
                    $update_data[$notifications_sent_key_name.'_in_app'] = $this->isValid($request->notifications_sent_in_app) ? intval($request->notifications_sent_in_app) : '';
                    $update_data[$notifications_clicked_percentages_key_name.'_email'] = $this->isValid($request->notifications_clicked_email) ? intval($request->notifications_clicked_email) : '';
                    $update_data[$notifications_clicked_percentages_key_name.'_sms'] = $this->isValid($request->notifications_clicked_sms) ? intval($request->notifications_clicked_sms) : '';
                    $update_data[$notifications_clicked_percentages_key_name.'_in_app'] = $this->isValid($request->notifications_clicked_in_app) ? intval($request->notifications_clicked_in_app) : '';
                break;
                case 'app_retention':
                    $users_retention_key_name = 'users_retention_'.$year_month_string;
                    $update_data[$users_retention_key_name.'_d1'] = $this->isValid($request->app_retention_d1) ? intval($request->app_retention_d1) : '';
                    $update_data[$users_retention_key_name.'_d7'] = $this->isValid($request->app_retention_d7) ? intval($request->app_retention_d7) : '';
                    $update_data[$users_retention_key_name.'_d30'] = $this->isValid($request->app_retention_d30) ? intval($request->app_retention_d30) : '';
                break;
                case 'app_paid_users':
                    $paid_users_key_name = 'paid_users_'.$year_month_string;
                    $update_data[$paid_users_key_name.'_weekly'] = $this->isValid($request->paid_users_weekly) ? intval($request->paid_users_weekly) : '';
                    $update_data[$paid_users_key_name.'_monthly'] = $this->isValid($request->paid_users_monthly) ? intval($request->paid_users_monthly) : '';
                    $update_data[$paid_users_key_name.'_half_yearly'] = $this->isValid($request->paid_users_half_yearly) ? intval($request->paid_users_half_yearly) : '';
                    $update_data[$paid_users_key_name.'_yearly'] = $this->isValid($request->paid_users_yearly) ? intval($request->paid_users_yearly) : '';
                break;
                default:
                    $data['success'] = false;
                    $response_code = Response::HTTP_FORBIDDEN;
                break;
            }
            if(!empty($update_data)){
                foreach($update_data as $update_data_key => $update_data_value){
                    DashboardData::updateOrCreate(
                        ['name'=>$update_data_key],
                        ['preview_value'=>$update_data_value]
                    );
                }
            }else{
                $data['success'] = false;
                $response_code = Response::HTTP_FORBIDDEN;
            }
        }else{
            $data['success'] = false;
            $response_code = Response::HTTP_FORBIDDEN;
        }
        return response()->json(compact('data'),$response_code);
    }

    public function getFields(Request $request){
        $data = [
            'success' => true
        ];
        $response_code = Response::HTTP_OK;
        $requested_name = $request->name;
        $requested_value = $request->value;
        if(!empty($requested_name) && !empty($requested_value)){
            $year_month_string = strtolower(now()->create($requested_value)->format('Y_M'));
            switch($requested_name){
                case 'app_traction':
                    $downloads_key_name = 'users_downloads_'.$year_month_string;
                    $uninstalls_key_name = 'users_uninstalls_'.$year_month_string;
                    $percentages_key_name = 'users_percentages_'.$year_month_string.'_male';
                    $data['users_downloads_organic'] = DashboardData::where('name',$downloads_key_name.'_organic')->first()->preview_value ?? '';
                    $data['users_downloads_paid'] = DashboardData::where('name',$downloads_key_name.'_paid')->first()->preview_value ?? '';
                    $data['users_downloads_referrals'] = DashboardData::where('name',$downloads_key_name.'_referrals')->first()->preview_value ?? '';
                    $data['users_uninstalls'] = DashboardData::where('name',$uninstalls_key_name)->first()->preview_value ?? '';
                    $data['users_percentage'] = DashboardData::where('name',$percentages_key_name)->first()->preview_value ?? 50;
                break;
                case 'app_engagement':
                    $daily_time_key_name = 'users_daily_time_'.$year_month_string;
                    $logins_key_name = 'users_logins_'.$year_month_string;
                    $active_users_key_name = 'users_active_'.$year_month_string;
                    $notifications_sent_key_name = 'notifications_sent_'.$year_month_string;
                    $notifications_clicked_percentages_key_name = 'notifications_clicked_percentages_'.$year_month_string;
                    $data['daily_time_male_subscribers'] = DashboardData::where('name',$daily_time_key_name.'_male_subscribers')->first()->preview_value ?? '';
                    $data['daily_time_male_non_subscribers'] = DashboardData::where('name',$daily_time_key_name.'_male_non_subscribers')->first()->preview_value ?? '';
                    $data['daily_time_female_users'] = DashboardData::where('name',$daily_time_key_name.'_female_users')->first()->preview_value ?? '';
                    $data['logins_male_subscribers'] = DashboardData::where('name',$logins_key_name.'_male_subscribers')->first()->preview_value ?? '';
                    $data['logins_male_non_subscribers'] = DashboardData::where('name',$logins_key_name.'_male_non_subscribers')->first()->preview_value ?? '';
                    $data['logins_female_users'] = DashboardData::where('name',$logins_key_name.'_female_users')->first()->preview_value ?? '';
                    $data['active_users_daily'] = DashboardData::where('name',$active_users_key_name.'_daily')->first()->preview_value ?? '';
                    $data['active_users_monthly'] = DashboardData::where('name',$active_users_key_name.'_monthly')->first()->preview_value ?? '';
                    $data['notifications_sent_email'] = DashboardData::where('name',$notifications_sent_key_name.'_email')->first()->preview_value ?? '';
                    $data['notifications_sent_sms'] = DashboardData::where('name',$notifications_sent_key_name.'_sms')->first()->preview_value ?? '';
                    $data['notifications_sent_in_app'] = DashboardData::where('name',$notifications_sent_key_name.'_in_app')->first()->preview_value ?? '';
                    $data['notifications_clicked_email'] = DashboardData::where('name',$notifications_clicked_percentages_key_name.'_email')->first()->preview_value ?? 0;
                    $data['notifications_clicked_sms'] = DashboardData::where('name',$notifications_clicked_percentages_key_name.'_sms')->first()->preview_value ?? 0;
                    $data['notifications_clicked_in_app'] = DashboardData::where('name',$notifications_clicked_percentages_key_name.'_in_app')->first()->preview_value ?? 0;
                break;
                case 'app_retention':
                    $users_retention_key_name = 'users_retention_'.$year_month_string;
                    $data['app_retention_d1'] = DashboardData::where('name',$users_retention_key_name.'_d1')->first()->preview_value ?? 0;
                    $data['app_retention_d7'] = DashboardData::where('name',$users_retention_key_name.'_d7')->first()->preview_value ?? 0;
                    $data['app_retention_d30'] = DashboardData::where('name',$users_retention_key_name.'_d30')->first()->preview_value ?? 0;
                break;
                case 'app_paid_users':
                    $paid_users_key_name = 'paid_users_'.$year_month_string;
                    $data['paid_users_weekly'] = DashboardData::where('name',$paid_users_key_name.'_weekly')->first()->preview_value ?? '';
                    $data['paid_users_monthly'] = DashboardData::where('name',$paid_users_key_name.'_monthly')->first()->preview_value ?? '';
                    $data['paid_users_half_yearly'] = DashboardData::where('name',$paid_users_key_name.'_half_yearly')->first()->preview_value ?? '';
                    $data['paid_users_yearly'] = DashboardData::where('name',$paid_users_key_name.'_yearly')->first()->preview_value ?? '';
                break;
                default:
                    $data['success'] = false;
                    $response_code = Response::HTTP_FORBIDDEN;
                break;
            }
        }else{
            $data['success'] = false;
            $response_code = Response::HTTP_FORBIDDEN;
        }
        return response()->json(compact('data'),$response_code);
    }
    public function preview(){
        $dashboard_data['total_downloads'] = DashboardData::where('name','like','users_downloads_%')->sum('preview_value');
        $dashboard_data['total_uninstalls'] = DashboardData::where('name','like','users_uninstalls_%')->sum('preview_value');
        $dashboard_data['downloads_this_month'] = DashboardData::where('name','like','users_downloads_'.strtolower(date('Y_M')).'%')->sum('preview_value');
        $dashboard_data['uninstalls_this_month'] = DashboardData::where('name','like','users_uninstalls_'.strtolower(date('Y_M')).'%')->sum('preview_value');
        $dashboard_data['total_downloads_males'] = DashboardData::where('name','like','males_users_downloads_%')->sum('preview_value');
        $dashboard_data['total_downloads_females'] = DashboardData::where('name','like','females_users_downloads_%')->sum('preview_value');
        $dashboard_data['last_twelve_months_data'] = [];
        $current_date = now();
        for($i=0;$i<12;$i++){
            $year_month_string = strtolower($current_date->format('Y_M'));
            $dashboard_data['last_twelve_months_data'][$current_date->format('M Y')] = [
                'total_downloads' => DashboardData::where('name','like','users_downloads_'.$year_month_string.'%')->sum('preview_value'),
                'male_downloads' => DashboardData::where('name','like','males_users_downloads_'.$year_month_string.'%')->sum('preview_value'),
                'female_downloads' => DashboardData::where('name','like','females_users_downloads_'.$year_month_string.'%')->sum('preview_value'),
                'organic_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_organic')->first()->preview_value ?? 0,
                'paid_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_paid')->first()->preview_value ?? 0,
                'referral_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_referrals')->first()->preview_value ?? 0,
                'male_percentage' => DashboardData::where('name','users_percentages_'.$year_month_string.'_male')->first()->preview_value ?? 0,
                'female_percentage' => DashboardData::where('name','users_percentages_'.$year_month_string.'_female')->first()->preview_value ?? 0,
                'total_uninstalls' => DashboardData::where('name','users_uninstalls_'.$year_month_string)->first()->preview_value ?? 0,
                'daily_time_male_subscribers' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_male_subscribers')->first()->preview_value ?? 'N/A',
                'daily_time_male_non_subscribers' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_male_non_subscribers')->first()->preview_value ?? 'N/A',
                'daily_time_female_users' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_female_users')->first()->preview_value ?? 'N/A',
                'logins_male_subscribers' => DashboardData::where('name','users_logins_'.$year_month_string.'_male_subscribers')->first()->preview_value ?? 'N/A',
                'logins_male_non_subscribers' => DashboardData::where('name','users_logins_'.$year_month_string.'_male_non_subscribers')->first()->preview_value ?? 'N/A',
                'logins_female_users' => DashboardData::where('name','users_logins_'.$year_month_string.'_female_users')->first()->preview_value ?? 'N/A',
                'active_daily' => DashboardData::where('name','users_active_'.$year_month_string.'_daily')->first()->preview_value ?? 0,
                'active_monthly' => DashboardData::where('name','users_active_'.$year_month_string.'_monthly')->first()->preview_value ?? 0,
                'notifications_sent_email' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_email')->first()->preview_value ?? 0,
                'notifications_sent_sms' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_sms')->first()->preview_value ?? 0,
                'notifications_sent_in_app' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_in_app')->first()->preview_value ?? 0,
                'notifications_clicked_percentages_email' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_email')->first()->preview_value ?? 0,
                'notifications_clicked_percentages_sms' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_sms')->first()->preview_value ?? 0,
                'notifications_clicked_percentages_in_app' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_in_app')->first()->preview_value ?? 0,
                'users_retention_d1' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_email')->first()->preview_value ?? 0,
                'users_retention_d7' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_sms')->first()->preview_value ?? 0,
                'users_retention_d30' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_in_app')->first()->preview_value ?? 0,
                'paid_users_weekly' => DashboardData::where('name','paid_users_'.$year_month_string.'_weekly')->first()->preview_value ?? 0,
                'paid_users_monthly' => DashboardData::where('name','paid_users_'.$year_month_string.'_monthly')->first()->preview_value ?? 0,
                'paid_users_half_yearly' => DashboardData::where('name','paid_users_'.$year_month_string.'_half_yearly')->first()->preview_value ?? 0,
                'paid_users_yearly' => DashboardData::where('name','paid_users_'.$year_month_string.'_yearly')->first()->preview_value ?? 0,
                'revenue_by_weekly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_weekly')->first()->preview_value ?? 0) * 49,
                'revenue_by_monthly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_monthly')->first()->preview_value ?? 0) * 99,
                'revenue_by_half_yearly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_half_yearly')->first()->preview_value ?? 0) * 299,
                'revenue_by_yearly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_yearly')->first()->preview_value ?? 0) * 399,
            ];
            $current_date = $current_date->subMonth();
            if($current_date->format('Ym') == '202209'){
                break;
            }
        }
        $dashboard_data['last_twelve_months_data'] = array_reverse($dashboard_data['last_twelve_months_data']);
        return view('admin.pages.dashboard.view-controlled',$dashboard_data)->with(['custom_title'=>__('Dashboard')]);
    }
    public function publish(){
        $dashboard_datas = DashboardData::get();
        foreach($dashboard_datas as $dashboard_data){
            $dashboard_data->value = $dashboard_data->preview_value;
            $dashboard_data->save();
        }
        flash('Data published successfully!')->success();
        return redirect()->route('admin.dashboard.edit');
    }
    private function isValid($value){
        if(!empty($value) || $value == 0){
            return true;
        }
        if($value == '' || is_null($value)){
            return false;
        }
        return false;
    }
}