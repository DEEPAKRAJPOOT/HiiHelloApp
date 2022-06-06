<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\Personality;
use App\Models\ProfileDetail;
use App\Models\Interest;
use App\Models\UserInterest;
use App\Models\Language;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.users.index')->with(['custom_title' => 'Users']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //personality
        $personalities = Personality::where('is_active','y')->get();
        $university = ProfileDetail::where(['attribute'=>'university_college','is_active'=>'y'])->get();
        $educations = ProfileDetail::with(['profileDetailTransDefault'])->where(['attribute'=>'education','is_active'=>'y'])->get(); 
        $professions = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'profession','is_active'=>'y'])->get();
        $religions = ProfileDetail::with('profileDetailTransDefault')->where(['attribute' => 'religion','is_active'=>'y'])->get();
        $relationship_status = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'relationship_status','is_active'=>'y'])->get();
        $you_are_here = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'i_am_here','is_active'=>'y'])->get();
        $food_preferences = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'food_preference','is_active'=>'y'])->get();
        $drinking = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'drinking','is_active'=>'y'])->get();
        $smoking = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'smoking','is_active'=>'y'])->get();
        $pets = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'pet','is_active'=>'y'])->get();
        $star_signs = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'star_sign','is_active'=>'y'])->get();
        $community = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'community','is_active'=>'y'])->get();
        $travelling = Interest::with(['subInterests.interestTransDefault'])->where(['slug'=>'traveling','is_active'=>'y'])->get();
        $musics = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'music','is_active' => 'y'])->get();
        $books = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'books', 'is_active' => 'y'])->get();
        $films = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'film', 'is_active' => 'y'])->get();
        $hobbies = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'hobbies','is_active' => 'y'])->get();
        $childhood = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'childhood-game','is_active' => 'y'])->get();
        $sports = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'sports','is_active' => 'y'])->get();
        $actors = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'actors', 'is_active' => 'y'])->get();
        $singers = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'singers', 'is_active' => 'y'])->get();
        $foods = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'food', 'is_active' => 'y'])->get();
        return view('admin.pages.users.create',compact('personalities','university','educations','professions','religions','relationship_status','you_are_here','food_preferences','drinking','smoking','pets','star_signs','community','travelling','musics','books','films','hobbies','childhood','sports','actors','singers','foods'))->with(['custom_title' => 'User']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = NULL;
        if( $request->has('profile_photo') ) {
            $path = $request->file('profile_photo')->store('users/profile_photo');
        }

        $user = User::create($request->all());
        $user['custom_id']   =   getUniqueString('users');
        $user['password']    =   Hash::make(config('utility.default_password'));
        $user->profile_photo = $path;

        // user full name
        $traslate_data = [];
        $language_codes = Language::pluck('lang_code')->toArray();
        if(!empty($request->full_name))
        {
            foreach($language_codes as $language_code){
                $traslate_data[$language_code] =  [ 'full_name' =>  $request->full_name ];
            }
            $user->update($traslate_data);
            $user->is_translated = 'n';
        }

        if(!empty($request->fav_movie))
        {
            foreach($language_codes as $language_code){
                $traslate_data[$language_code] =  [ 'fav_movie' =>  $request->fav_movie ];
            }
            $user->update($traslate_data);
            $user->is_translated = 'n';
        }

        /* Verification Details */
        $verify_photo  =   $request->verify_photo;
        $verify_video  =   $request->verify_video;

        if($verify_photo == 'y' && empty($user->photo_verified_at)){
            $user->photo_verified_at = \Carbon\Carbon::now(); 
        }elseif($verify_photo == NULL){
            $user->photo_verified_at = NULL;
        }

        if($verify_video == 'y' && empty($user->video_verified_at)){ 
            $user->video_verified_at = \Carbon\Carbon::now(); 
        }elseif($verify_video == NULL){
            $user->video_verified_at = NULL;
        }

        /* User Interest */
        if(!empty($request->traveling_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->traveling_id,  
                ]);
        }

        if(!empty($request->music_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->music_id,  
                ]);
        }

        if(!empty($request->hobbie_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->hobbie_id,  
                ]);
        }

        if(!empty($request->game_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->game_id,  
                ]);
        }

        if(!empty($request->sport_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->sport_id,  
                ]);
        }

        if(!empty($request->film_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->film_id,  
                ]);
        }

        if(!empty($request->depend_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->depend_id,  
                ]);
        }

        if(!empty($request->singer_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->singer_id,  
                ]);
        }

        if(!empty($request->food_id)){
            UserInterest::create(
                ['custom_id' => getUniqueString('user_interests'),
                  'user_id'  => $user->id,
                  'interest_id' => $request->food_id,  
                ]);
        }

        if( $user->save() ) {
            flash('User account created successfully!')->success();
        } else {
            flash('Unable to save avatar. Please try again later.')->error();
        }
        return redirect(route('admin.users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::with([
            'userTransDefault',
            'country.countryTransDefault','location.locationTransDefault',
            'language','userDetails',
            'interests.interest.interestTransDefault',
            'relationshipStatus.profileDetailTransDefault','youAreHere.profileDetailTransDefault',
            'foodPreference.profileDetailTransDefault','drinking.profileDetailTransDefault',
            'smoking.profileDetailTransDefault','starSign.profileDetailTransDefault',
            'religion.profileDetailTransDefault','community.profileDetailTransDefault',
            'education.profileDetailTransDefault'])->firstOrFail();
        return view('admin.pages.users.view',compact('user'))->with(['custom_title' => 'User']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $personalities = Personality::where('is_active','y')->get();
        $university = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'university_college','is_active'=>'y'])->get();
        $educations = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'education','is_active'=>'y'])->get(); 
        $professions = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'profession','is_active'=>'y'])->get();
        $religions = ProfileDetail::with('profileDetailTransDefault')->where(['attribute' => 'religion','is_active'=>'y'])->get();
        $relationship_status = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'relationship_status','is_active'=>'y'])->get();
        $you_are_here = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'i_am_here','is_active'=>'y'])->get();
        $food_preferences = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'food_preference','is_active'=>'y'])->get();
        $drinking = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'drinking','is_active'=>'y'])->get();
        $smoking = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'smoking','is_active'=>'y'])->get();
        $pets = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'pet','is_active'=>'y'])->get();
        $star_signs = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'star_sign','is_active'=>'y'])->get();
        $community = ProfileDetail::with('profileDetailTransDefault')->where(['attribute'=>'community','is_active'=>'y'])->get();
        //user interest
        $user_interest = UserInterest::with('interest')->where('user_id',$user->id)->pluck('interest_id')->toArray();
        $travelling = Interest::with(['subInterests.interestTransDefault'])->where(['slug'=>'traveling','is_active'=>'y'])->get();
        $musics = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'music','is_active' => 'y'])->get();
        $books = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'books', 'is_active' => 'y'])->get();
        $films = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'film', 'is_active' => 'y'])->get();
        $hobbies = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'hobbies','is_active' => 'y'])->get();
        $childhood = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'childhood-game','is_active' => 'y'])->get();
        $sports = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'sports','is_active' => 'y'])->get();
        $actors = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'actors', 'is_active' => 'y'])->get();
        $singers = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'singers', 'is_active' => 'y'])->get();
        $foods = Interest::with(['subInterests.interestTransDefault'])->where(['slug' => 'food', 'is_active' => 'y'])->get();
        return view('admin.pages.users.edit', compact('user','personalities','university','educations','professions','religions','relationship_status','you_are_here','food_preferences','drinking','smoking','pets','star_signs','community','travelling','musics','books','films','hobbies','childhood','sports','actors','singers','foods','user_interest'))->with(['custom_title' => 'Users']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        try{
            DB::beginTransaction();
            if(!empty($request->action) && $request->action == 'change_status') {
                $content = ['status'=>204, 'message'=>"something went wrong"];
                if($user) {
                    $user->is_active = $request->value;
                    if($user->save()) {
                        DB::commit();
                        $content['status']=200;
                        $content['message'] = "Status updated successfully.";
                    }
                }
                return response()->json($content);
            } else {
                $path = $user->profile_photo;
                //request has remove_profie_photo then delete user image
                if( $request->has('remove_profie_photo') ){
                    if( $user->profile_photo){
                        Storage::delete($user->profile_photo);
                    }
                    $path = null;
                }

                if( $request->hasFile('profile_photo') ) {
                    if( $user->profile_photo){
                        Storage::delete($user->profile_photo);
                    }
                    $path = $request->profile_photo->store('users/profile_photo');
                }
                $user->fill($request->validated());
                $user->profile_photo = $path;

                /* Verification Details */
                $photo_verified_at  =   $request->photo_verified_at;
                $video_verified_at  =   $request->video_verified_at;

                if($photo_verified_at == 'y' && empty($user->photo_verified_at)){
                    $user->photo_verified_at = \Carbon\Carbon::now(); 
                }elseif($photo_verified_at == NULL){
                    $user->photo_verified_at = NULL;
                }

                if($video_verified_at == 'y' && empty($user->video_verified_at)){ 
                    $user->video_verified_at = \Carbon\Carbon::now(); 
                }elseif($video_verified_at == NULL){
                    $user->video_verified_at = NULL;
                }

                if(!empty($request->traveling_id)){
                    UserInterest::updateOrCreate(
                        [
                          'user_id'  => $user->id,
                          'interest_id' => $request->traveling_id,  
                        ],
                        [
                            'custom_id' => getUniqueString('user_interests'),
                        ]);
                }

                if(!empty($request->music_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->music_id,  
                        ]);
                }

                if(!empty($request->hobbie_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->hobbie_id,  
                        ]);
                }

                if(!empty($request->game_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->game_id,  
                        ]);
                }

                if(!empty($request->sport_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->sport_id,  
                        ]);
                }

                if(!empty($request->film_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->film_id,  
                        ]);
                }

                if(!empty($request->depend_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->depend_id,  
                        ]);
                }

                if(!empty($request->singer_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->singer_id,  
                        ]);
                }

                if(!empty($request->food_id)){
                    UserInterest::create(
                        ['custom_id' => getUniqueString('user_interests'),
                          'user_id'  => $user->id,
                          'interest_id' => $request->food_id,  
                        ]);
                }

                // dd($request->all());

                if( $user->save() ) {
                    DB::commit();
                    flash('User details updated successfully!')->success();
                } else {

                    flash('Unable to update user. Try again later')->error();
                }
                return redirect(route('admin.users.index'));
            }
        }catch(QueryException $e){
            DB::rollback();
            return redirect()->back()->flash('error',$e->getMessage());
        }catch(Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];

            $users_profile_photos = User::whereIn('custom_id', explode(',', $request->ids))->pluck('profile_photo')->toArray();
            foreach ($users_profile_photos as $image) {
                if(!empty($image)){
                  Storage::delete($image);
                }
            }
            User::whereIn('custom_id',explode(',',$request->ids))->forceDelete();
            $content['status']=200;
            $content['message'] = "User deleted successfully.";
            $content['count'] = User::all()->count();
            return response()->json($content);
        }else{
            $user = User::where('custom_id', $id)->firstOrFail();
            if( $user->profile_photo ){
                Storage::delete($user->profile_photo);
            }
            $user->forceDelete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"User deleted successfully.", 'count' => User::all()->count());
                return response()->json($content);
            }else{
                flash('User deleted successfully.')->success();
                return redirect()->route('admin.users.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $users = User::with('userTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('country_code', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('interest', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();
        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->custom_id,
            ];

            $records['data'][] = [
                'id' => $user->id,
                'full_name' =>  $user->userTransDefault ? $user->userTransDefault->full_name : "",
                'country_code' => $user->country_code,
                'contact_no' => $user->contact_no ? '<a href="tel:' . $user->contact_no . '" >' . $user->contact_no . '</a>' : 'N/A',
                'gender' => $user->gender,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->custom_id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $user->custom_id)->render(),
            ];
        }
        // dd($records);
        return $records;
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.pages.users.trashed', compact('users'))->with(['custom_title' => 'TRASHED']);
    }

    public function trashedData(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $users = User::with('userTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('country_code', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('interest', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->onlyTrashed()->get();
        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'display' => ($user->is_display == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->id,
            ];

            $records['data'][] = [
                'full_name' =>  $user->userTransDefault ? $city->userTransDefault->full_name : "",
                'country_code' => $user->country_code,
                'contact_no' => $user->contact_no ? '<a href="tel:' . $user->contact_no . '" >' . $user->contact_no . '</a>' : 'N/A',
                'gender' => $user->gender,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'display' => view('admin.layouts.includes.switchDisplay', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $user->id)->render(),
            ];
        }
        // dd($records);

        return $records;
    }

    public function getActorList(Request $request)
    {
        $data['interest_id'] = Interest::where('parent_id',$request->actor_id)->get();
        return response()->json($data);
    }

    public function getSingerList(Request $request)
    {
        $data['interest_id'] = Interest::where('parent_id',$request->singer_id)->get();
        return response()->json($data);
    }
}
