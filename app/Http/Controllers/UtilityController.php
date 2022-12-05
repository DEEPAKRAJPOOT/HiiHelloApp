<?php

namespace App\Http\Controllers;
use App\Admin;
use App\Models\CmsPage;
use App\Models\User;
use App\Models\UserTranslation;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use DB;
class UtilityController extends Controller
{
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile) {
            Storage::delete($user->profile);
        }
        $path = $request->file('profile_pic')->store('profiles');
        $user->profile = $path;

        if ($user->save()) {
            flash('Profile picture updated successfully!')->success();
        } else {
            flash('Unable to update profile picture. Try again later')->error();
        }

        return redirect()->back();
    }

    // Common For All
    public function checkOldPassword(Request $request)
    {
        if (Hash::check($request->old_password, Auth::user()->password)) {
            return 'true';
        } else {
            return 'false';
        }
    }

    // For Unique email check of user & admin
    public function checkEmail(Request $request)
    {
        $id = $request->id ?? 0;
    	if( $request->type == 'user' ) {
    		$user = User::query();
    	} elseif($request->type == 'admin') {
    		$user = Admin::query();
    	}

    	$user =	$user->where([
    			['id', '<>', $id],
    			'email' => $request->email,
    		])->count();

    	if( $user == 0 ){
    	    return "true";
    	}else{
    	    return "false";
    	}
    }
    public function checkContact(Request $request)
    {
        $id = $request->id ?? 0;
    	if( $request->type == 'user' ) {
    		$user = User::query();
    	} elseif($request->type == 'admin') {
    		$user = Admin::query();
    	}

    	$user =	$user->where([
    			['id', '<>', $id],
    			'contact_no' => $request->contact_no,
    		])->count();

    	if( $user == 0 ){
    	    return "true";
    	}else{
    	    return "false";
    	}
    }

    public function noScript()
    {
        return view('errors.no-script');
    }

    public function noCookie()
    {
        return view('errors.no-cookie');
    }

    public function home()
    {
        return view('welcome');
    }

    public function adminHome()
    {
        $users = [];
        $users[] = Auth::user();
        $users[] = Auth::guard()->user();
        $users[] = Auth::guard('admin')->user();

        return redirect(route('admin.dashboard.index'));
    }

        // For Admin Panel Usage
    public function checkTitle(Request $request)
    {
        $id = $request->id ?? 0;
        if( $request->type == 'cms' ) {
            $data = CmsPage::query();
        }

        $data = $data->where([
                ['id', '<>', $id],
                'title' => $request->title,
            ])->count();


        if( $data == 0 ){
            return "true";
        }else{
            return "false";
        }
    }

    public function profileCheckPassword(Request $request) {
        if(Hash::check($request->current_password, Auth::user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function translate()
    {
        $apiKey = 'AIzaSyCnTLblh4He46O3-5NoJ0sXOzyelS76jEY';
        // $text = "Rewari,,state";
        $text = "50 Lnp";
        $source = 'en';
        $target = 'gu';
        // echo $text; die();
        $target_lang = ['en','hi','ta','mr','bn','gu','kn','ml','or','pa','te','as'];
        // foreach($target_lang as $lang)
        // {
        //     if ($lang != $source) {
        //         $url = 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyCnTLblh4He46O3-5NoJ0sXOzyelS76jEY&source=en&target='.$lang.'&q='.rawurlencode($text);
        //         $handle = curl_init($url);
        //         curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        //         $response = curl_exec($handle);
        //         $responseDecoded = json_decode($response, true);
        //         $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        //               //Here we fetch the HTTP response code
        //         // dd($url);
        //         curl_close($handle);
                
        //         if($responseCode != 200) {
        //             dump('Fetching translation failed! Server response code:' . $responseCode);
        //             dd('Error description: ' . $responseDecoded['error']['errors'][0]['message']);
        //         }
        //         else {
        //             echo $lang." ".$responseDecoded['data']['translations'][0]['translatedText'];
        //             echo "<br>";
        //             // dd($responseDecoded);
        //             // dd('Translation: ' . $responseDecoded['data']['translations'][0]['translatedText']);
        //         }
        //     }
        // }

        $url = 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyCnTLblh4He46O3-5NoJ0sXOzyelS76jEY&source='.$source.'&target='.$target.'&q='.rawurlencode($text);
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
              //Here we fetch the HTTP response code
        // dd($url);
        curl_close($handle);
        
        if($responseCode != 200) {
            dump('Fetching translation failed! Server response code:' . $responseCode);
            dd('Error description: ' . $responseDecoded['error']['errors'][0]['message']);
        }
        else {
            // echo $responseDecoded['data']['translations'][0]['translatedText']; echo "<br>"; die();
            // echo "<pre>"; print_r($responseDecoded['data']['translations'][0]['translatedText']); die();
            // echo count(explode(",",$responseDecoded['data']['translations'][0]['translatedText'])); echo "<br>";
            $explode_data = explode(",",$responseDecoded['data']['translations'][0]['translatedText']);
            echo "<pre>"; print_r($explode_data);
            echo $explode_data[0]; echo "<br>";
            echo isset($explode_data[1]) ? $explode_data[1] : NULL; echo "<br>";
            // echo "<br>";
            // dd($responseDecoded);
            // dd('Translation: ' . $responseDecoded['data']['translations'][0]['translatedText']);
        }

    }

    public function locationTranslations(Request $request)
    {   
        $limit = $request->limit ? $request->limit : 1;
        $is_print = $request->is_print ? $request->is_print : 0;
        $id = $request->id ? $request->id : '';

        $default_lang_code  =   config('utility.default_lang_code');
        $apiKey             =   config('utility.google.translate.api_key');
        $message            =   'No details found to translate !!!';
        $message            =   "No location translate records found.";

        $locations  = DB::table("location_translations");
        $locations  = $locations->where("locale","!=","en");
        if (!empty($id)) {
            $locations  = $locations->where("id","=",$id);
        }
        $locations  = $locations->limit($limit);
        $locations  = $locations->orderBy("name","ASC");
        $locations  = $locations->get();
        if ($is_print == 1) {
            echo "<pre>"; print_r($locations->toArray()); die();
        }

        if($locations->isNotEmpty()){
            foreach($locations as $location){
                $name           =   $location->name;
                $locality       =   $location->locality;
                $state          =   $location->state;
                $locationT_id   =   $location->id;
                $detected_lang  =   $location->locale ? $location->locale : 'en';
                $texxt = $name.",".$state;

                if( !empty($name)){
                    $success = $this->translateText($apiKey,$detected_lang,$texxt,$locationT_id);
                }
            }
            $message = 'Location translate successfully.';
        }
        return $message;
    }

    function translateText($apiKey, $detected_lang,$text, $locationT_id)
    {
        $success = false;
        $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target='.$detected_lang;

        $handle = curl_init($traslate_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
        curl_close($handle);

        if($responseCode == 200) {
            $translatedText = explode(",",$responseDecoded['data']['translations'][0]['translatedText']);
            // echo "<pre>"; print_r($translatedText); die();
            $updatename = $translatedText[0];
            $updatestate = isset($translatedText[1]) ? $translatedText[1] : NULL;
           
            DB::table('location_translations')
                ->where('id',$locationT_id)
                ->update([
                    'name' => $updatename,
                    'state' => $updatestate,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            $success = true;
        }
        return $success;
    }

    public function Usertranslate(Request $request)
    {   
        $apiKey             =   config('utility.google.translate.api_key');

        $limit = isset($request->limit) ? $request->limit : 1;
        $print = isset($request->print) ? $request->print : 0;
        $user_list = User::select('users.id as user_id','user_translations.full_name as full_name','user_translations.about_me as about_me','user_translations.fav_movie as fav_movie','user_translations.locale as locale','user_translations.id as user_translations_id')
                    ->join("user_translations","user_translations.user_id","=","users.id")
                    ->where('user_translations.locale','en')
                    ->where('users.is_trans_as','n')
                    // ->orWhere('user_translations.locale','as')
                    // ->where("user_translations.full_name","!=","")
                    ->orderBy("users.id","ASC")
                    ->limit($limit)
                    ->get();
        if ($print == 1) {
            echo "<pre>"; print_r($user_list->toArray()); die();
        }
        if (count($user_list) > 0) {
            foreach ($user_list as $key => $val) {
                $user = DB::table('users')->where('id',$val->user_id)->first();
                if (!empty($user)) {
                    $UserTranslation = new UserTranslation();
                    $UserTranslation->locale = 'as';
                    $UserTranslation->user_id = $val->user_id;
                    if(!empty($val->full_name)){
                        $message = $this->translateUserText($apiKey,$val->full_name);
                        $UserTranslation->full_name = $message;
                    }
                    if(!empty($val->about_me)){
                        $message = $this->translateUserText($apiKey,$val->about_me);
                        $UserTranslation->about_me = $message;
                    }
                    if(!empty($val->fav_movie)){
                        $message = $this->translateUserText($apiKey,$val->fav_movie);
                        $UserTranslation->fav_movie = $message;
                    }
                    $UserTranslation->save();

                    DB::table('users')->where('id',$val->user_id)->update(array('is_trans_as' => 'y'));
                }

            }
        }
    }

    function translateUserText($apiKey, $text)
    {

        $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=as';
        
        $handle = curl_init($traslate_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
        curl_close($handle);

        if($responseCode == 200) {
            $translatedText = $responseDecoded['data']['translations'][0]['translatedText'];
            return $translatedText;
        }else
        {
            return false;
        }

    }

    function chk_female_subscriptions(Request $request)
    {
         $user_list = User::select('users.id as id','users.gender as gender','users.subscription_end_date as subscription_end_date')
                    ->where('users.gender','Female')
                    ->get();
        // echo "<pre>"; print_r($user_list->toArray()); die();
        $subscription_total = 0;
        $un_subscription_total = 0;
        if (count($user_list) > 0) {
            foreach ($user_list as $key => $val) {
                $subscription_data = Subscription::where("user_id",$val->id)->where("status","active")->where("plan_id","3")->whereNull("deleted_at")->first();
                if (empty($subscription_data)) {
                    echo "User Id :- ".$val->id. "<br>";

                    $plan = SubscriptionPlan::where('is_default_for_girl','y')->first();
                    if($plan){

                        $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                        if ($val->subscription_end_date >= $new_subscription_start_date) {
                            $new_subscription_start_date = $val->subscription_end_date;
                        }

                        // add free subscription
                        Subscription::firstOrCreate([
                            'user_id'       =>  $val->id ?? NULL,
                            'plan_id'       =>  $plan->id ?? NULL,
                            'months'        =>  $plan->months,
                            'amount'        =>  $plan->amount,
                            'start_date'    =>  $new_subscription_start_date,
                            'end_date'      =>  NULL,
                            'payment_date'  =>  now(),
                            'payment_type'  =>  '',
                            'status'        =>  'active',
                        ], [
                            'custom_id'     =>  getUniqueString('subscriptions'),
                        ]);

                        // update user table subscription details
                        User::where('id',$val->id)->update([ 
                            'is_subscribed' =>  'y',
                        ]);
                    }

                    $subscription_total ++;
                }
                else
                {
                    echo "Subscription User Id :- ".$val->id. "<br>";
                    $un_subscription_total ++;
                }
            }
        }
        echo "subscription total :- ".$subscription_total. "<br>";
        echo "uN subscription total :- ".$un_subscription_total. "<br>";
    }
}
