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

                if(!empty($request->voice) && !empty($request->voice_answer)){
                    if(!empty($user->voice)){ if( Storage::exists($user->voice) ) { Storage::delete($user->voice); } }

                    $voice_path = $request->voice->store('users/voice');
                    $user->voice = $voice_path;
                    $user->voice_answer = $request->voice_answer;
                }

                if( !empty($request->profile_photo) ) {
                    if(!empty($user->profile_photo)){
                        if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                    }
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                    $user->profile_photo = $path;
                }
                    
                if($user->save()){
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

                    // Update Sequence Of Images
                    $del_imgs = UserDetail::whereUserId($user->id);
                    if(!empty($request->old_images) && !empty($request->old_images['sequence']) && !empty($request->old_images['file']) ){
                        $not_delete_images = $request->old_images['file'];

                        $total_old_images = count($request->old_images['file']);
                        if( $total_old_images == count($request->old_images['sequence']) ){
                            for ($i=0; $i < $total_old_images; $i++) { 
                                $custom_id = $request->old_images['file'][$i];
                                $sequence = $request->old_images['sequence'][$i];

                                if($custom_id == 'profile_photo'){
                                    $main_img = UserDetail::updateOrCreate([
                                        'user_id'   =>  $user->id,
                                        'image'     =>  $user->profile_photo,
                                        'sequence'  =>  $sequence,
                                    ],[
                                        'custom_id' =>  getUniqueString('user_details'),
                                    ]);
                                    $not_delete_images[] = $main_img->custom_id;
                                }else{
                                    $image_data = UserDetail::whereUserId($user->id)->whereCustomId($custom_id)->first();
                                    if($image_data){
                                        $image_data->update(['sequence' => $sequence]);
                                        $image_data->save();
                                    }
                                }
                            }
                        }
                        $del_imgs = $del_imgs->whereNotIn('custom_id',$not_delete_images);
                    }

                    // Update Image As Main Image
                    if(!empty($request->old_profile_photo)){
                        $main_image = UserDetail::select('image')->whereUserId($user->id)->whereCustomId($request->old_profile_photo)->first();
                        if($main_image){
                            // if(!empty($user->profile_photo)){
                            //     if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                            // }
                            $user->profile_photo = $main_image->image; 
                            $user->save();
                        }
                    }

                    // Delete Extra Images
                    $del_imgs = $del_imgs->whereNotNull('image')->whereNull('video')->get();
                    if($del_imgs->isNotEmpty()){
                        foreach ($del_imgs as $key => $del_img) {
                            if($del_img && $del_img->image != $user->profile_photo){
                                if( Storage::exists($del_img->image) ) { Storage::delete($del_img->image); } 
                            }
                        }
                        $del_imgs->each->delete();
                    }
                    
                    // Store New Images
                    if(!empty($request->images) && !empty($request->images['sequence']) && !empty($request->images['file']) ){
                        $total_images = count($request->images['file']);
                        if( $total_images == count($request->images['sequence']) ){
                            $new_images = [];
                            for ($i=0; $i < $total_images; $i++) { 
                                $image      =   $request->images['file'][$i];
                                $sequence   =   $request->images['sequence'][$i];
                                $path       =   $image->store('users/images');

                                $new_images[] = [
                                    'custom_id'                 =>  getUniqueString('user_details'),
                                    'user_id'                   =>  $user->id,
                                    'image'                     =>  $path,   
                                    'sequence'                  =>  $sequence,
                                    'created_at'                =>  \Carbon\Carbon::now(),
                                    'updated_at'                =>  \Carbon\Carbon::now(),
                                ];
                            }
                            UserDetail::insert($new_images);
                        }
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

                $user = User::with(['userDetails','interests.interest.parentInterest',
                                    'interests.interest.masterInterest','interests.interest.interestTranslation',
                                    'personality.personalityTranslation'])
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
