<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\User\ { FullProfileRequest };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage, Auth };
use App\Models\ { User, UserDetail, Interest, UserInterest, ProfileDetail, Personality };

class ProfileController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }
    public function getAuthUser(){ return auth('sanctum')->user(); }

    /**
     * Setup full profile of the user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function setFullProfile(Request $request){
        $rules = FullProfileRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = User::with(['userDetails','interests'])->whereId(Auth::id())->firstOrFail();

                if(!empty($request->email)){ $user->email = $request->email; }

                if(!empty($request->about_me)){ $user->about_me = $request->about_me; }

                if(!empty($request->personality)){
                    $personality = Personality::select('id')->whereCustomId($request->personality)->whereIsActive('y')->firstOrFail();
                    $user->personality_id = $personality->id;
                }

                if(!empty($request->university)){
                    $university = ProfileDetail::select('id')->whereSlug($request->university)->whereIsActive('y')->firstOrFail();
                    $user->university_id = $university->id;
                }

                if(!empty($request->profession)){
                    $profession = ProfileDetail::select('id')->whereSlug($request->profession)->whereIsActive('y')->firstOrFail();
                    $user->profession_id = $profession->id;
                }

                if(!empty($request->relationship_status)){
                    $relationship_status = ProfileDetail::select('id')->whereSlug($request->relationship_status)->whereIsActive('y')->firstOrFail();
                    $user->relationship_status_id = $relationship_status->id;
                }

                if(!empty($request->you_are_here)){
                    $you_are_here = ProfileDetail::select('id')->whereSlug($request->you_are_here)->whereIsActive('y')->firstOrFail();
                    $user->you_are_here_id = $you_are_here->id;
                }

                if(!empty($request->food_preference)){
                    $food_preference = ProfileDetail::select('id')->whereSlug($request->food_preference)->whereIsActive('y')->firstOrFail();
                    $user->food_preference_id = $food_preference->id;
                }

                if(!empty($request->drinking)){
                    $drinking = ProfileDetail::select('id')->whereSlug($request->drinking)->whereIsActive('y')->firstOrFail();
                    $user->drinking_id = $drinking->id;
                }

                if(!empty($request->smoking)){
                    $smoking = ProfileDetail::select('id')->whereSlug($request->smoking)->whereIsActive('y')->firstOrFail();
                    $user->smoking_id = $smoking->id;
                }

                if(!empty($request->pet)){
                    $pet = ProfileDetail::select('id')->whereSlug($request->pet)->whereIsActive('y')->firstOrFail();
                    $user->pet_id = $pet->id;
                }

                if(!empty($request->star_sign)){
                    $star_sign = ProfileDetail::select('id')->whereSlug($request->star_sign)->whereIsActive('y')->firstOrFail();
                    $user->star_sign_id = $star_sign->id;
                }

                if(!empty($request->religion)){
                    $religion = ProfileDetail::select('id')->whereSlug($request->religion)->whereIsActive('y')->firstOrFail();
                    $user->religion_id = $religion->id;
                }

                if(!empty($request->community)){
                    $community = ProfileDetail::select('id')->whereSlug($request->community)->whereIsActive('y')->firstOrFail();
                    $user->community_id = $community->id;
                }
                
                if(!empty($request->education)){
                    $education = ProfileDetail::select('id')->whereSlug($request->education)->whereIsActive('y')->firstOrFail();
                    $user->education_id = $education->id;
                }

                if(!empty($request->date_idea)){
                    $date_idea = ProfileDetail::select('id')->whereSlug($request->date_idea)->whereIsActive('y')->firstOrFail();
                    $user->date_idea_id = $date_idea->id;
                }

                if(!empty($request->social_cause)){
                    $social_cause = ProfileDetail::select('id')->whereSlug($request->social_cause)->whereIsActive('y')->firstOrFail();
                    $user->social_cause_id = $social_cause->id;
                }

                if(!empty($request->risk_taken)){
                    $risk_taken = ProfileDetail::select('id')->whereSlug($request->risk_taken)->whereIsActive('y')->firstOrFail();
                    $user->risk_taken_id = $risk_taken->id;
                }

                if(!empty($request->perfect_relation_things)){
                    $perfect_relation_things = ProfileDetail::select('id')->whereSlug($request->perfect_relation_things)->whereIsActive('y')->firstOrFail();
                    $user->perfect_relation_id = $perfect_relation_things->id;
                }

                if(!empty($request->my_mantra)){
                    $my_mantra = ProfileDetail::select('id')->whereSlug($request->my_mantra)->whereIsActive('y')->firstOrFail();
                    $user->my_mantra_id = $my_mantra->id;
                }

                if(!empty($request->one_thing_know)){
                    $one_thing_know = ProfileDetail::select('id')->whereSlug($request->one_thing_know)->whereIsActive('y')->firstOrFail();
                    $user->one_thing_know_id = $one_thing_know->id;
                }

                if(!empty($request->worst_date)){
                    $worst_date = ProfileDetail::select('id')->whereSlug($request->worst_date)->whereIsActive('y')->firstOrFail();
                    $user->worst_date_id = $worst_date->id;
                }

                if(!empty($request->introduce_to_family)){
                    $introduce_to_family = ProfileDetail::select('id')->whereSlug($request->introduce_to_family)->whereIsActive('y')->firstOrFail();
                    $user->intro_family_id = $introduce_to_family->id;
                }

                if(!empty($request->found_the_one)){
                    $found_the_one = ProfileDetail::select('id')->whereSlug($request->found_the_one)->whereIsActive('y')->firstOrFail();
                    $user->found_one_id = $found_the_one->id;
                }

                if(!empty($request->about_me_surprises)){
                    $about_me_surprises = ProfileDetail::select('id')->whereSlug($request->about_me_surprises)->whereIsActive('y')->firstOrFail();
                    $user->about_surprising_id = $about_me_surprises->id;
                }

                if(!empty($request->political_views)){
                    $political_views = ProfileDetail::select('id')->whereSlug($request->political_views)->whereIsActive('y')->firstOrFail();
                    $user->political_view_id = $political_views->id;
                }

                if($user->save()){
                    if(!empty($request->interests)){
                        $selected_inerests = []; $not_delete_interests = [];
                        foreach($request->interests as $key => $interest_levels){
                            foreach($interest_levels as $key => $req_interest){
                                $selected_inerests[] = $req_interest;
                            }
                        }

                        if(count($selected_inerests) > 0){
                            $interest_ids = Interest::whereIn('custom_id',$selected_inerests)->whereIsActive('y')->pluck('id')->toArray();
                            foreach($interest_ids as $interest_id){
                                $custom_id = getUniqueString('user_interests');

                                UserInterest::updateOrCreate([
                                    'user_id'       =>  $user->id,
                                    'interest_id'   =>  $interest_id,
                                ],[
                                    'custom_id'     =>  $custom_id,
                                ]);
                                $not_delete_interests[] = $custom_id;
                            }
                        }

                        // Delete Interests
                        UserInterest::whereUserId($user->id)->whereNotIn('custom_id',$not_delete_interests)->delete();
                    }

                    // Store Images
                    if($request->has('images')){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->image)){
                                    if( Storage::exists($userDetail->image) ) { Storage::delete($userDetail->image); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $image_data = [];
                        foreach ($request->images as $key => $image) {
                            if($image){
                                $image_path = $image->store('users/images');
                                $image_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'image'             =>  $image_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($image_data);
                    }
                    
                    // Store Videos
                    if($request->hasFile('videos')){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->video)){
                                    if( Storage::exists($userDetail->video) ) { Storage::delete($userDetail->video); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $video_data = [];
                        foreach ($request->videos as $key => $video) {
                            if($video){
                                $video_path = $video->store('users/videos');
                                $video_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'video'             =>  $video_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($video_data);
                    }   

                    // Store Audios
                    if(!empty($request->voices)){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->voice)){
                                    if( Storage::exists($userDetail->voice) ) { Storage::delete($userDetail->voice); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $video_data = [];
                        foreach ($request->voices as $key => $voice) {
                            if($voice){
                                $voice_path = $voice->store('users/voice');
                                $video_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'voice'             =>  $voice_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($video_data);
                    }
                }

                return [ 'data' => [ 'flags' =>  [ 'profile_percentage'    =>  $user->calculateProfilePercent(),],],
                        'meta'  => [
                            'message'       =>  trans('api.profile_setuped'), 
                        ]];
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Personality':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Personality details")]);
                        break;
                    case 'App\Models\Interest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Interest details")]);
                        break;
                    case 'App\Models\ProfileDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Profile details")]);
                        break;
                    case 'App\Models\UserInterest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User interest")]);
                        break;
                    case 'App\Models\UserDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User details")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'set_full_profile');
            }
        }

        return $this->returnResponse();  
    }
}
