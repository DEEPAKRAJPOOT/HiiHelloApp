<?php

namespace App\Http\Controllers;
use App\Admin;
use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $text = 'saltlake';
        $source = 'en';
        $target = 'as';
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
            echo $responseDecoded['data']['translations'][0]['translatedText'];
            echo "<br>";
            // dd($responseDecoded);
            // dd('Translation: ' . $responseDecoded['data']['translations'][0]['translatedText']);
        }

    }
}
