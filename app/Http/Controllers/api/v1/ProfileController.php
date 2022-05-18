<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\User\ { FullProfileRequest };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage, Auth };
use App\Http\Resources\v1\ { UserFullProfile };
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
                $user = $request->user();
                if(!empty($request->email)){ $user->email = $request->email; }
                if(!empty($request->about_me)){ $user->about_me = $request->about_me; }
                if(!empty($request->fav_movie)){ $user->fav_movie = $request->fav_movie; }

                if(!empty($request->personality)){
                    $personality = Personality::select('id')->whereCustomId($request->personality)->whereIsActive('y')->firstOrFail();
                    $user->personality_id = $personality->id;
                }
                if(!empty($request->university_college)){
                    $university_college = ProfileDetail::select('id')->whereSlug($request->university_college)->whereIsActive('y')->firstOrFail();
                    $user->university_id = $university_college->id;
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
                if(!empty($request->sun_sign)){
                    $sun_sign = ProfileDetail::select('id')->whereSlug($request->sun_sign)->whereIsActive('y')->firstOrFail();
                    $user->star_sign_id = $sun_sign->id;
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

                if(!empty($request->voice) && !empty($request->voice_answer)){
                    if(!empty($user->voice)){ if( Storage::exists($user->voice) ) { Storage::delete($user->voice); } }

                    $voice_path = $request->voice->store('users/voice');
                    $user->voice = $voice_path;
                    $user->voice_answer = $request->voice_answer;
                }
                    
                if($user->save()){
                    if(!empty($request->interests)){
                        $selected_inerests = []; $not_delete_interests = [];

                        foreach($request->interests as $key => $interest){
                            $selected_inerests[] = $interest;
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
                }

                $user = User::with(['userDetails','interests','personality.personalityTranslation'])
                                ->whereId($user->id)->firstOrFail();

                return (new UserFullProfile($user))
                        ->additional(['meta'  => [
                            'message'       =>  trans('api.profile_setuped'), 
                        ]]);
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
