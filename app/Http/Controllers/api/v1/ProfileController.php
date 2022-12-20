<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use App\Http\Requests\Api\User\{FullProfileRequest, SetInterestRequest, SetMediaRequest};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Storage, Auth};
use App\Http\Resources\v1\{UserFullProfile, UserInterestResource, MediaResource};
use App\Models\{User, UserDetail, Interest, UserInterest, ProfileDetail, Personality, Language, UserPersonality,ImageModerationLog};

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
    public function setFullProfile(Request $request)
    {
        $fullProfileRequest = new FullProfileRequest();
        if ($this->apiValidator($request->all(), $fullProfileRequest->rules())) {
            try {
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $traslate_data = [];

                if (!empty($request->email)) {
                    $user->email = $request->email;
                }

                if (!empty($request->about_me) && !empty($user->language)) {
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['about_me' =>  $request->about_me];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_about_me = 'n';
                }

                if (!empty($request->fav_movie) && !empty($user->language)) {
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['fav_movie' =>  $request->fav_movie];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_fav_movie = 'n';
                }

                if (!empty($request->university_college)) {
                    $university_college = ProfileDetail::select('id')->whereSlug($request->university_college)->whereIsActive('y')->firstOrFail();
                    $user->university_id = $university_college->id;
                }
                if (!empty($request->profession)) {
                    $profession = ProfileDetail::select('id')->whereSlug($request->profession)->whereIsActive('y')->firstOrFail();
                    $user->profession_id = $profession->id;
                }
                if (!empty($request->relationship_status)) {
                    $relationship_status = ProfileDetail::select('id')->whereSlug($request->relationship_status)->whereIsActive('y')->firstOrFail();
                    $user->relationship_status_id = $relationship_status->id;
                }
                if (!empty($request->i_am_here)) {
                    $i_am_here = ProfileDetail::select('id')->whereSlug($request->i_am_here)->whereIsActive('y')->firstOrFail();
                    $user->you_are_here_id = $i_am_here->id;
                }
                if (!empty($request->food_preference)) {
                    $food_preference = ProfileDetail::select('id')->whereSlug($request->food_preference)->whereIsActive('y')->firstOrFail();
                    $user->food_preference_id = $food_preference->id;
                }
                if (!empty($request->drinking)) {
                    $drinking = ProfileDetail::select('id')->whereSlug($request->drinking)->whereIsActive('y')->firstOrFail();
                    $user->drinking_id = $drinking->id;
                }
                if (!empty($request->smoking)) {
                    $smoking = ProfileDetail::select('id')->whereSlug($request->smoking)->whereIsActive('y')->firstOrFail();
                    $user->smoking_id = $smoking->id;
                }
                if (!empty($request->pet)) {
                    $pet = ProfileDetail::select('id')->whereSlug($request->pet)->whereIsActive('y')->firstOrFail();
                    $user->pet_id = $pet->id;
                }
                if (!empty($request->star_sign)) {
                    $star_sign = ProfileDetail::select('id')->whereSlug($request->star_sign)->whereIsActive('y')->firstOrFail();
                    $user->star_sign_id = $star_sign->id;
                }
                if (!empty($request->religion)) {
                    $religion = ProfileDetail::select('id')->whereSlug($request->religion)->whereIsActive('y')->firstOrFail();
                    $user->religion_id = $religion->id;
                }
                if (!empty($request->community)) {
                    $community = ProfileDetail::select('id')->whereSlug($request->community)->whereIsActive('y')->firstOrFail();
                    $user->community_id = $community->id;
                }
                if (!empty($request->education)) {
                    $education = ProfileDetail::select('id')->whereSlug($request->education)->whereIsActive('y')->firstOrFail();
                    $user->education_id = $education->id;
                }

                // Remove Personality
                if (!empty($request->remove_personalities)) {
                    $remove_personalities = $request->remove_personalities;

                    UserPersonality::whereUserId($user->id)
                        ->whereHas('personality', function ($query) use ($remove_personalities) {
                            $query->whereIn('custom_id', $remove_personalities);
                        })->delete();
                }

                // Store Personality
                if (!empty($request->personalities)) {
                    $personality_ids = Personality::whereIn('custom_id', $request->personalities)->whereIsActive('y')->pluck('id')->toArray();
                    foreach ($personality_ids as $personality_id) {
                        UserPersonality::updateOrCreate([
                            'user_id'           =>  $user->id,
                            'personality_id'    =>  $personality_id,
                        ], [
                            'custom_id'         =>  getUniqueString('user_personalities'),
                        ]);
                    }
                }
                $user->save();

                $user = User::with([
                    'userTranslation', 'location.locationTranslation', 'language',
                    'education.profileDetailTranslation',
                    'university.profileDetailTranslation', 'profession.profileDetailTranslation',
                    'religion.profileDetailTranslation',
                    'relationshipStatus.profileDetailTranslation', 'youAreHere.profileDetailTranslation',
                    'foodPreference.profileDetailTranslation', 'drinking.profileDetailTranslation',
                    'smoking.profileDetailTranslation', 'pet.profileDetailTranslation',
                    'starSign.profileDetailTranslation', 'community.profileDetailTranslation',
                    'personalities.personality.personalityTranslation'
                ])
                    ->withCount(['blockedTos' => function ($query) use ($auth_id) {
                        $query->whereBlockBy($auth_id);
                    }])
                    ->whereId($user->id)->firstOrFail();

                return (new UserFullProfile($user))
                    ->additional(['meta'  => [
                        'message'       =>  trans('api.profile_setuped'),
                        'is_ban'        =>  false,
                    ]]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Personality':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Personality details")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\ProfileDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Profile details")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_full_profile');
            }
        }

        return $this->returnResponse();
    }

    /**
     * Set user interests
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setInterest(Request $request)
    {
        $setInterestRequest = new SetInterestRequest();
        if ($this->apiValidator($request->all(), $setInterestRequest->rules())) {
            try {
                $user = $request->user();

                // Remove Interests
                if (!empty($request->remove_interests)) {
                    $rmv_inerests = [];
                    foreach ($request->remove_interests as $key => $remove_interest) {
                        $rmv_inerests[] = $remove_interest;
                    }

                    UserInterest::whereUserId($user->id)
                        ->whereHas('interest', function ($query) use ($rmv_inerests) {
                            $query->whereIn('custom_id', $rmv_inerests);
                        })->delete();
                }

                // Store Interest
                if (!empty($request->interests)) {
                    $selected_inerests = [];
                    foreach ($request->interests as $key => $interest) {
                        $selected_inerests[] = $interest;
                    }

                    if (count($selected_inerests) > 0) {
                        $interest_ids = Interest::whereIn('custom_id', $selected_inerests)->whereIsActive('y')->pluck('id')->toArray();
                        foreach ($interest_ids as $interest_id) {
                            UserInterest::updateOrCreate([
                                'user_id'       =>  $user->id,
                                'interest_id'   =>  $interest_id,
                            ], [
                                'custom_id'     =>  getUniqueString('user_interests'),
                            ]);
                        }
                    }
                }

                $user = User::with([
                    'userDetails', 'interests.interest.parentInterest', 'interests.interest.masterInterest',
                    'interests.interest.interestTranslation'
                ])
                    ->whereId($user->id)->firstOrFail();

                return ([
                    'data'  =>  [
                        'interests' => UserInterestResource::collection($user->interests),
                        'flags'     =>  [
                            'profile_percentage'    =>  $user->calculateProfilePercent(),
                        ]
                    ],
                    'meta'  => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.profile_setuped'),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Interest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Interest details")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\UserInterest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User interest")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_interest');
            }
        }

        return $this->returnResponse();
    }

    /**
     * Set user images, video & audio after uploding s3 directly
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setMedia(Request $request)
    {
        $safe_image = "true";
        $setMediaRequest = new SetMediaRequest();
        if ($this->apiValidator($request->all(), $setMediaRequest->rules())) {
            try {
                $user = $request->user();



                // Delete Voice
                if (!empty($request->remove_voice) && $request->remove_voice == 'y') {
                    if (!empty($user->voice)) {
                        if (Storage::exists($user->voice)) {
                            Storage::delete($user->voice);
                        }
                    }
                    $user->voice = NULL;
                    $user->voice_answer = NULL;
                    $user->save();
                }

                // Store Audio
                if (!empty($request->voice) && !empty($request->voice_answer)) {
                    if (!empty($user->voice)) {
                        if (Storage::exists($user->voice)) {
                            Storage::delete($user->voice);
                        }
                    }
                    $user->voice = $request->voice;
                    $user->voice_answer = $request->voice_answer;
                    $user->save();
                }

                // Store Video
                if (!empty($request->video)) {
                    $user_video =  UserDetail::updateOrCreate([
                        'user_id'       =>  $user->id,
                        'video'         =>  $request->video,
                    ], [
                        'custom_id'     =>  getUniqueString('user_details'),
                    ]);
                }

                // Delete Video
                if (!empty($request->remove_video)) {
                    $rmv_video = UserDetail::select('id', 'video')->whereUserId($user->id)
                        ->whereCustomId($request->remove_video)->first();
                    if ($rmv_video) {
                        if (Storage::exists($rmv_video->video)) {
                            Storage::delete($rmv_video->video);
                        }
                        $rmv_video->delete();
                    }
                }

                // Store New Images
                if (!empty($request->image_path)) {
                   

                    $s3_file_url = generateURL($request->image_path); 


                    $awsImgResultArr = array();

                    if($s3_file_url!="")                   
                    {
                        ///CHECK FOR AWS REKOGNIZTION START
                        $awsImgResultArr = checkAwsImageModeration($request,$s3_file_url,"url");                   
                    }    
                    else
                    {
                        $safe_image = "false";
                    }

                    if(count($awsImgResultArr) > 0)
                    {
                        if($awsImgResultArr["is_safe_image"]==true) 
                        {
                           
                           if (empty($user->profile_photo)) {
                                $user->profile_photo = $request->image_path;
                                $user->is_media_checked = 'n';
                                $user->save();
                            } else {
                                $count_images = $user->userDetails->whereNotNull('image')->count();
                                $new_sequence = $count_images + 1;

                                $new_image = UserDetail::updateOrCreate([
                                    'user_id'   =>  $user->id,
                                    'image'     =>  $request->image_path,
                                ], [
                                    'custom_id' =>  getUniqueString('user_details'),
                                ]);

                                if ($new_image->wasRecentlyCreated) {
                                    $new_image->sequence = $new_sequence;
                                    $new_image->is_verified = 'n';
                                    $new_image->save();
                                }
                            }

                        }  
                        else
                        {
                           if (Storage::exists($request->image_path)) 
                           {                             
                             Storage::delete($request->image_path);
                           } 

                           $safe_image = "false";  
                        } 


                        //INSERT IN TO IMAGE MODERATIO LOG START
                        if($awsImgResultArr["is_safe_image"]==true) 
                            $is_approved = 1;
                        else
                            $is_approved = 0;

                        $image_type = "other_photo";  
                        $message = $awsImgResultArr["log_message"];                        
                        $total_face_detected = $awsImgResultArr["total_face_detected"];                        
                        
                        $response_data = $awsImgResultArr["image_moderation_response"];
                        $request_data = $awsImgResultArr["image_moderation_request"];

                        ImageModerationLog::Create([
                            'user_id'             => $user->id,
                            'is_approved'         => $is_approved,
                            'request'             => $request_data,
                            'response'            => $response_data,
                            'total_face_detected' => $total_face_detected,
                            'message'             => $message,
                            'image_type'          => $image_type,
                        ]);    

                        //INSERT IN TO IMAGE MODERATIO LOG END     


                    }                    
                    //CHECK FOR AWS REKOGNIZTION END
                }

                // Delete Image
                if (!empty($request->remove_image)) {
                    if ($user->profile_photo == $request->remove_image) {
                        if (Storage::exists($user->profile_photo)) {
                            Storage::delete($user->profile_photo);
                        }
                        $user->profile_photo = NULL;
                        $user->save();
                    } else {
                        $rmv_image = UserDetail::select('id', 'image')->whereUserId($user->id)
                            ->whereImage($request->remove_image)->first();
                        if ($rmv_image) {
                            if (Storage::exists($rmv_image->image)) {
                                Storage::delete($rmv_image->image);
                            }
                            $rmv_image->delete();
                        }
                    }
                }

                // Change Sequence
                if (!empty($request->image_sequence)) {
                    $original_photo = $user->profile_photo;

                    for ($i = 0; $i < count($request->image_sequence); $i++) {
                        $custom_id = $request->image_sequence[$i];
                        $old_sequence = $i + 1;

                        if ($custom_id == 'profile_photo' && !empty($original_photo)) {
                            if ($old_sequence > 1) {
                                $old_sequence = $old_sequence - 1;
                            }

                            UserDetail::updateOrCreate([
                                'user_id'   =>  $user->id,
                                'image'     =>  $original_photo,
                            ], [
                                'custom_id' =>  getUniqueString('user_details'),
                                'sequence'  =>  $old_sequence,
                            ]);
                        } else {
                            $image_data = UserDetail::whereUserId($user->id)->whereCustomId($custom_id)->first();
                            if ($image_data) {
                                $image_data->update(['sequence' => $old_sequence]);
                                $image_data->save();
                            } else {
                                $user->profile_photo = $custom_id;
                                $user->save();

                                // Delete From Other
                                UserDetail::whereUserId($user->id)->whereImage($custom_id)->delete();
                            }
                        }
                    }
                }

                $user = User::with(['userTranslation', 'personalities', 'userDetails', 'interests'])->whereId($user->id)->firstOrFail();
                return (new MediaResource($user))
                    ->additional(['meta'  => [
                        'message'       =>  trans('api.profile_setuped'),
                        'safe_image'    =>  $safe_image,       
                        'is_ban'        =>  false,
                    ]]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\UserDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User details")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_users_media');
            }
        }

        return $this->returnResponse();
    }
}
