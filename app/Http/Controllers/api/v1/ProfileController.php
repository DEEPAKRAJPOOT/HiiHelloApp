<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\User\ { FullProfileRequest, SetInterestRequest, SetMediaRequest };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage, Auth };
use App\Http\Resources\v1\ { UserFullProfile, UserInterestResource, MediaResource };
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
                $auth_id = $user ? $user->id : NULL;

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
                if(!empty($request->i_am_here)){
                    $i_am_here = ProfileDetail::select('id')->whereSlug($request->i_am_here)->whereIsActive('y')->firstOrFail();
                    $user->you_are_here_id = $i_am_here->id;
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
                $user->save();

                $user = User::with(['location.locationTranslation','language',
                                'personality.personalityTranslation','education.profileDetailTranslation',
                                'university.profileDetailTranslation','profession.profileDetailTranslation',
                                'religion.profileDetailTranslation',
                                'relationshipStatus.profileDetailTranslation','youAreHere.profileDetailTranslation',
                                'foodPreference.profileDetailTranslation','drinking.profileDetailTranslation',
                                'smoking.profileDetailTranslation','pet.profileDetailTranslation',
                                'starSign.profileDetailTranslation','community.profileDetailTranslation',
                            ])
                            ->withCount(['blockedTos' => function ($query) use ($auth_id) {
                                $query->whereBlockBy($auth_id);
                            }])
                            ->withCount('likes')->whereId($user->id)->firstOrFail();

                return (new UserFullProfile($user))
                        ->additional(['meta'  => [
                            'message'       =>  trans('api.profile_setuped'), 
                        ]]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Personality':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Personality details")]);
                        break;
                    case 'App\Models\ProfileDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Profile details")]);
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

    /**
     * Set user interests
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function setInterest(Request $request){
        $rules = SetInterestRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                
                // Remove Interests
                if(!empty($request->remove_interests)){
                    $rmv_inerests = [];
                    foreach($request->remove_interests as $key => $remove_interest){ $rmv_inerests[] = $remove_interest; }
                    
                    UserInterest::whereUserId($user->id)
                        ->whereHas('interest',function($query) use ($rmv_inerests){
                            $query->whereIn('custom_id',$rmv_inerests);
                        })->delete();
                }

                // Store Interest
                if(!empty($request->interests)){
                    $selected_inerests = [];
                    foreach($request->interests as $key => $interest){ $selected_inerests[] = $interest; }

                    if(count($selected_inerests) > 0){
                        $interest_ids = Interest::whereIn('custom_id',$selected_inerests)->whereIsActive('y')->pluck('id')->toArray();
                        foreach($interest_ids as $interest_id){
                            UserInterest::updateOrCreate([
                                'user_id'       =>  $user->id,
                                'interest_id'   =>  $interest_id,
                            ],[
                                'custom_id'     =>  getUniqueString('user_interests'),
                            ]);
                        }
                    }
                }   

                $user = User::select('id','custom_id')
                            ->with(['userDetails','interests.interest.parentInterest','interests.interest.masterInterest',
                            'interests.interest.interestTranslation'])
                            ->whereId($user->id)->firstOrFail();

                return (['data'  =>  [
                            'interests' => UserInterestResource::collection($user->interests),
                            'flags'     =>  [
                                'profile_percentage'    =>  $user->calculateProfilePercent(),
                            ]],
                        'meta'  => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.profile_setuped'), 
                        ]]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Interest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Interest details")]);
                        break;
                    case 'App\Models\UserInterest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User interest")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'set_interest');
            }
        }

        return $this->returnResponse();  
    }

    /**
     * Set user images, video & audio after uploding s3 directly
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function setMedia(Request $request){
        $rules = SetMediaRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();

                // Store Audio
                if(!empty($request->voice) && !empty($request->voice_answer)){
                    if(!empty($user->voice)){ if( Storage::exists($user->voice) ) { Storage::delete($user->voice); } }
                    $user->voice = $request->voice;
                    $user->voice_answer = $request->voice_answer;
                    $user->save();
                }

                // Store Video
                if(!empty($request->video)){
                    $user_video =  UserDetail::updateOrCreate([
                        'user_id'       =>  $user->id,
                        'video'         =>  $request->video,
                    ],[
                        'custom_id'     =>  getUniqueString('user_details'),
                    ]);
                }

                // Delete Video
                if(!empty($request->remove_video)){
                    $rmv_video = UserDetail::select('id','video')->whereUserId($user->id)
                                        ->whereCustomId($request->remove_video)->first();
                    if($rmv_video){
                        if( Storage::exists($rmv_video->video) ) { Storage::delete($rmv_video->video); }
                        $rmv_video->delete();
                    }
                } 

                // Store New Images
                if(!empty($request->image_path)){
                    if(empty($user->profile_photo)){
                        $user->profile_photo = $request->image_path;
                        $user->save();
                    }else{
                        $count_images = $user->userDetails->whereNotNull('image')->count();
                        $new_sequence = $count_images + 1;

                        $new_image = UserDetail::updateOrCreate([
                            'user_id'   =>  $user->id,
                            'image'     =>  $request->image_path,
                        ],[
                            'custom_id' =>  getUniqueString('user_details'),
                        ]);

                        if($new_image->wasRecentlyCreated){
                            $new_image->sequence = $new_sequence;
                            $new_image->save();
                        }
                    }
                }

                // Delete Image
                if(!empty($request->remove_image)){
                    if($user->profile_photo == $request->remove_image){
                        if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                        $user->profile_photo = NULL;
                        $user->save();
                    }else{
                        $rmv_image = UserDetail::select('id','image')->whereUserId($user->id)
                                        ->whereImage($request->remove_image)->first();
                        if($rmv_image){
                            if( Storage::exists($rmv_image->image) ) { Storage::delete($rmv_image->image); }
                            $rmv_image->delete();
                        }
                    }
                } 

                // Change Sequence
                if(!empty($request->image_sequence)){
                    $original_photo = $user->profile_photo;

                    for ($i=0; $i < count($request->image_sequence); $i++) { 
                        $custom_id = $request->image_sequence[$i];
                        $old_sequence = $i + 1;

                        if($custom_id == 'profile_photo' && !empty($original_photo)){
                            if($old_sequence > 1){ $old_sequence = $old_sequence - 1; }

                            UserDetail::updateOrCreate([
                                'user_id'   =>  $user->id,
                                'image'     =>  $original_photo,
                            ],[
                                'custom_id' =>  getUniqueString('user_details'),
                                'sequence'  =>  $old_sequence,
                            ]);
                        }else{
                            $image_data = UserDetail::whereUserId($user->id)->whereCustomId($custom_id)->first();
                            if($image_data){
                                $image_data->update(['sequence' => $old_sequence]);
                                $image_data->save();
                            }else{
                                $user->profile_photo = $custom_id;
                                $user->save();

                                // Delete From Other
                                UserDetail::whereUserId($user->id)->whereImage($custom_id)->delete();
                            }
                        }
                    }
                }

                $user = User::with(['userDetails','interests'])->whereId($user->id)->firstOrFail();
                return (new MediaResource($user))
                        ->additional(['meta'  => [
                            'message'       =>  trans('api.profile_setuped'), 
                        ]]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
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
                $this->storeErrorLog($e,'set_users_media');
            }
        }

        return $this->returnResponse();  
    }
}
