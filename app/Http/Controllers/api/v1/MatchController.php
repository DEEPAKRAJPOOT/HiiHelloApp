<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Http\Requests\Api\Match\{DeleteMatchRequest, GetMatchRequest,SystemMatchRequest};
use App\Http\Resources\v1\{MatchResource};
use App\Models\{User, Like, ChatRoom, UserInterest, BlockUser, UnMatch, UserPersonality,SystemMatch};
use Carbon\Carbon;

class MatchController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * Get new matched profile details.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNewMatches(Request $request)
    {
        $getMatchRequest = new GetMatchRequest();
        if ($this->apiValidator($request->all(), $getMatchRequest->rules())) {
            try {
                $user = $request->user();

                // Match Not Allowed
                if(!$user->isNewMatchAllow()){
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('New Matches')]);
                    $this->status = Response::HTTP_OK;
                    return $this->returnResponse();
                }

                $auth_id = $user ? $user->id : NULL;
                $search = $request->search;
                $user->match_count = 0; // Reset Match Count
                $user->save();

                // Config Details
                $backup_logic       =   config('utility.profile.match.backup_logic') ?? true;
                $match_percentage   =   config('utility.profile.match.match_percentage') ?? 20;
                $age_min_diff       =   config('utility.profile.match.age_min_diff') ?? 1;
                $age_max_diff       =   config('utility.profile.match.age_max_diff') ?? 1;
                $max_limit          =   config('utility.profile.match.max_limit') ?? 1;
                $max_limit_apply    =   config('utility.profile.match.max_limit_apply') ?? true;

                $auth_interest  =   $user->interest ? $user->interest : 'Both';
              
                $auth_age   =   $user->getAge();    // Blocked & Interest Details
                $age_from   =   $auth_age - $age_min_diff;
                $age_to     =   $auth_age + $age_max_diff;

                $unmatched      =   UnMatch::whereUnmatchBy($auth_id)->whereNotNull('unmatch_to')->distinct()->pluck('unmatch_to')->toArray();
                $blocked        =   BlockUser::whereBlockBy($auth_id)->whereNotNull('blocked_to')->distinct()->pluck('blocked_to')->toArray();
                $interests      =   UserInterest::whereUserId($auth_id)->whereNotNull('interest_id')->distinct()->pluck('interest_id')->toArray();
                $personalities  =   UserPersonality::whereUserId($auth_id)->whereNotNull('personality_id')->distinct()->pluck('personality_id')->toArray();

                // if chat is open then restrict in match profiles
                $rooms = ChatRoom::whereHas('chatMessages')
                    ->where(function ($query) use ($auth_id) {
                        $query->whereCreatorId($auth_id)->orWhere('participate_id', $auth_id);
                    });
                $creators = $rooms->whereNotNull('creator_id')->pluck('creator_id')->toArray();
                $participants = $rooms->whereNotNull('participate_id')->pluck('participate_id')->toArray();

                $restricted_ids = array_unique(array_merge($unmatched, $blocked, $creators, $participants));
                if (($key = array_search($auth_id, $restricted_ids)) !== false) {
                    unset($restricted_ids[$key]);
                }

                // Get users details who likes each others
                $likes = DB::table('likes')
                    ->join("likes as like", function ($q) {
                        $q->on("likes.liker_id", "=", "like.user_id");
                        $q->on("like.liker_id", "=", "likes.user_id");
                    })
                    ->join('users', function ($q) {
                        $q->on('users.id', "=", "likes.user_id");
                    })
                    ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                    ->where("likes.user_id", '!=', $auth_id)
                    ->pluck('users.custom_id')->toArray();

                $likes_count = count($likes);
                
                $matches = User::with('userTranslation:id,locale,user_id,full_name')
                    ->where('id', '!=', $auth_id)                           // Not Own Profile
                    ->whereNotNull('profile_photo')                         // Must Have Main Photo
                    ->whereIsActive('y');

                if ($auth_interest != 'Both') {
                    $matches = $matches->where('gender', $auth_interest);   // Interested in Gender
                }                                           
                if (count($restricted_ids) > 0) {
                    $matches = $matches->whereNotIn('id', $restricted_ids);
                }
                if($likes_count > 0){
                   $matches = $matches->whereIn('custom_id', $likes);       // Someone likes me and I like him/her 
                }

                $matches =  $matches->where(function ($query)
                    use ($user, $age_from, $age_to, $match_percentage, $interests, $personalities) {
                        $query->orWhere('language_id', $user->language_id)                          // Language
                            ->orWhere('location_id', $user->location_id)                            // Location
                            ->orWhereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'),array($age_from,$age_to))  // Age / Birth Date
                            ->orWhere('profile_percentage', '>=', $match_percentage)                // Profile completion
                            ->orWhere('verify_status', 'verified')                                  // Verified/Unverified  

                            ->orWhereHas('personalities', function ($q) use ($personalities) {      // Personality Type 
                                $q->whereIn('personality_id', $personalities);
                            })

                            // Basic Details 
                            ->orWhere('relationship_status_id', $user->relationship_status_id)      // Relationship status
                            ->orWhere('you_are_here_id', $user->you_are_here_id)                    // I am here for
                            ->orWhere('food_preference_id', $user->food_preference_id)              // Food Preference
                            ->orWhere('drinking_id', $user->drinking_id)                            // Drinking
                            ->orWhere('smoking_id', $user->smoking_id)                              // Smoking
                            ->orWhere('pet_id', $user->pet_id)                                      // Pet
                            ->orWhere('education_id', $user->education_id)                          // Education
                            ->orWhere('university_id', $user->university_id)                        // University/College
                            ->orWhere('profession_id', $user->profession_id)                        // Profession
                            ->orWhere('star_sign_id', $user->star_sign_id)                          // Star Sign

                            ->orWhereHas('interests', function ($q) use ($interests) {              // My Interests
                                $q->whereIn('interest_id', $interests);
                            });
                    });

                if (!empty($search)) {
                    $matches = $matches->whereHas('userTranslations', function ($query_search) use ($search) {
                        $query_search->where('full_name', 'like', "%{$search}%");
                    });
                }

                $count = $matches->count();


                //NEED TO REMOVE ITS FOR CHECKING PURPOSE ONLY
                //$count = 0;
                //NEED TO REMOVE ITS FOR CHECKING PURPOSE ONLY


                $is_system_generated = false;
                $array_system_match_user_custome_id = array();

                // BackUp Plan If No Profile Match
                if ($count < 1 && $backup_logic == true) {
                    
                    //echo "Auth User :".$auth_id;
                    $is_system_generated = true;
                    
                    $today_date = \Carbon\Carbon::today()->format('Y-m-d');
                    $system_data = SystemMatch::select('custom_id','match_id','is_connected','match_date')->whereDate('match_date', '=', $today_date)->where('user_id', $auth_id)->first();
                    if($system_data)
                    {
                            $system_match_id = $system_data->match_id;   //GET FROM SYSTEM MATCH TABLE 
                            $array_system_match_user_custome_id[$system_match_id] = $system_data->custom_id;

                            if($system_data->is_connected==0)
                            {
                                
                                //RETURN THAT MATCH USER ID FROM SYSTEM_MATCH Table (match_id);
                                $matches = User::with('userTranslation:id,locale,user_id,full_name')
                                    ->where('id', '!=', $auth_id)->whereNotNull('profile_photo')->whereIsActive('y')->where('id',$system_match_id);
                                if ($auth_interest != 'Both') {
                                    $matches = $matches->where('gender', $auth_interest);
                                }
                                if (count($restricted_ids) > 0) {
                                    $matches = $matches->whereNotIn('id', $restricted_ids);
                                }

                                if ($max_limit_apply) { $count = $max_limit; } 
                                else{ $count = $matches->count(); }
                            
                            }
                            else  //connected user for that same date
                            {       
                                
                                //FOR GET NULL QUERY OBJECT
                                $system_match_id = -1;    

                                //RETURN THAT MATCH USER ID FROM SYSTEM_MATCH Table (match_id);
                                $matches = User::with('userTranslation:id,locale,user_id,full_name')
                                    ->where('id', '!=', $auth_id)->whereNotNull('profile_photo')->whereIsActive('y')->where('id',$system_match_id);
                                if ($auth_interest != 'Both') {
                                    $matches = $matches->where('gender', $auth_interest);
                                }
                                if (count($restricted_ids) > 0) {
                                    $matches = $matches->whereNotIn('id', $restricted_ids);
                                }

                                if ($max_limit_apply) { $count = $max_limit; } 
                                else{ $count = $matches->count(); }

                            }

                    }
                    else
                    {                                                
                        $system_data_connected = SystemMatch::select('custom_id','match_id','is_connected','match_date')->where('user_id', $auth_id)->where('is_connected', 0)->first();

                      

                        if($system_data_connected)
                        {                                  

                                $system_match_id = $system_data_connected->match_id;   //GET FROM SYSTEM MATCH TABLE 
                                $array_system_match_user_custome_id[$system_match_id] = $system_data_connected->custom_id;

                                 //RETURN THAT MATCH USER ID FROM SYSTEM_MATCH Table (match_id);
                                $matches = User::with('userTranslation:id,locale,user_id,full_name')
                                    ->where('id', '!=', $auth_id)->whereNotNull('profile_photo')->whereIsActive('y')->where('id',$system_match_id);
                                if ($auth_interest != 'Both') {
                                    $matches = $matches->where('gender', $auth_interest);
                                }
                                if (count($restricted_ids) > 0) {
                                    $matches = $matches->whereNotIn('id', $restricted_ids);
                                }

                                if ($max_limit_apply) { $count = $max_limit; } 
                                else{ $count = $matches->count(); }

                        }
                        else
                        {
                                //FIND USER AS PER IT WORKS AS IT IS NO CHANGE       

                                $matches = User::with('userTranslation:id,locale,user_id,full_name')
                                    ->where('id', '!=', $auth_id)->whereNotNull('profile_photo')->whereIsActive('y');
                                if ($auth_interest != 'Both') {
                                    $matches = $matches->where('gender', $auth_interest);
                                }
                                if (count($restricted_ids) > 0) {
                                    $matches = $matches->whereNotIn('id', $restricted_ids);
                                }

                                if ($max_limit_apply) { $count = $max_limit; } 
                                else{ $count = $matches->count(); }



                                // FOR INSERT DATA IN SYSTEM MATCH TABLE START FROM SEARCHED USER ABOVE
                                $match_id = 0;                                

                                $sql_match = $matches->latest();

                                if ($max_limit_apply) {
                                    if($likes_count != 0 && $count > $max_limit){
                                        $max_limit = $count;
                                    }
                                    $sql_match    =   $sql_match->limit($max_limit)->get();
                                } else {
                                    $sql_match    =   $sql_match->limit($request->limit ?? config('utility.pagination.limit'))
                                        ->offset($request->offset ?? config('utility.pagination.offset'))
                                        ->get();
                                }

                                if($sql_match)
                                {
                                    foreach ($sql_match as $sql_data) {                                        
                                        $match_id = $sql_data->id;   

                                            if($match_id > 0)
                                            {
                                                $custome_id = getUniqueString('system_match');

                                                $subscription =  SystemMatch::create([
                                                    'custom_id'                 =>  $custome_id,
                                                    'user_id'                   =>  $auth_id,
                                                    'match_id'                  =>  $match_id,
                                                    'is_connected'              =>  0,
                                                    'match_date'                =>  $today_date,
                                                ]);

                                                $array_system_match_user_custome_id[$match_id] = $custome_id;
                                            }                                        
                                    }                                    
                                }     

                                // FOR INSERT DATA IN SYSTEM MATCH TABLE END
                        }

                    }

                }

                $matches = $matches->latest();

                if ($max_limit_apply) {
                    if($likes_count != 0 && $count > $max_limit){
                        $max_limit = $count;
                    }
                    $matches    =   $matches->limit($max_limit)->get();
                } else {
                    $matches    =   $matches->limit($request->limit ?? config('utility.pagination.limit'))
                        ->offset($request->offset ?? config('utility.pagination.offset'))
                        ->get();
                }


                if ($matches->isNotEmpty()) {
                    $this->status = Response::HTTP_OK;

                    //dd($array_system_match_user_custome_id);
                    foreach($matches as $item){                               
                         $item->is_system_generated = $is_system_generated;
                         if($is_system_generated)
                         {
                            $item->system_match_user_key  = $array_system_match_user_custome_id[$item->id];
                         }
                         else
                         {
                            $item->system_match_user_key = null;
                         }   
                    }


                    return (MatchResource::Collection($matches))->additional([
                        'meta'  =>  [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $max_limit_apply ? $max_limit : $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' =>  __('New Matches')]),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('New Matches')]);
                    $this->status = Response::HTTP_OK;
                }
            } catch (ModelNotFoundException $exception) {
                $this->response['meta']['message'] = trans('api.went_wrong');
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'new_matches');
            }
        }
        return $this->returnResponse();
    }

    /**
     * Remove match/Unmatch profile detail.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeMatch(Request $request)
    {
        $deleteMatchRequest = new DeleteMatchRequest();
        if ($this->apiValidator($request->all(), $deleteMatchRequest->rules())) {
            DB::beginTransaction();
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $match_user = User::select('id')->whereCustomId($request->user_id)->firstOrFail();

                // Delete Like Details
                Like::whereUserId($match_user->id)->whereLikerId($auth_id)->delete();

                // Like::where(function($query) use ($auth_id, $match_user){
                //     $query->whereUserId($auth_id)->whereLikerId($match_user->id);
                // })->orWhere(function($query_or) use ($auth_id, $match_user){
                //     $query_or->whereUserId($match_user->id)->whereLikerId($auth_id);
                // })->delete();

                // Save Unmatch Details
                UnMatch::firstOrCreate([
                    'unmatch_by'    =>  $auth_id,
                    'unmatch_to'    =>  $match_user->id ?? NULL,
                ], [
                    'custom_id'     =>  getUniqueString('un_matches'),
                ]);

                $room = ChatRoom::with('chatMessages')
                    ->where(function ($query) use ($auth_id, $match_user) {
                        $query->where('creator_id', $auth_id)->where('participate_id', $match_user->id);
                    })->orWhere(function ($query_or) use ($auth_id, $match_user) {
                        $query_or->where('creator_id', $match_user->id)->where('participate_id', $auth_id);
                    })->first();

                // Delete Chat Room & Chat Messages
                if ($room) {
                    if ($room->chatMessages) {
                        $room->chatMessages->each->delete();
                    }
                    $room->delete();
                }

                DB::commit();
                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.delete', ['entity' =>  __('Unmatch')]),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                DB::rollback();
                $this->status = Response::HTTP_OK;
                switch ($exception->getModel()) {
                    case 'App\Models\ChatRoom':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Chat room")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();
                $this->status = Response::HTTP_OK;
                $this->storeErrorLog($e, 'delete_match');
            }
        }
        return $this->returnResponse();
    }


    public function setIsConnectedForSystemMatch(Request $request)
    {
        $systemMatchRequest = new SystemMatchRequest();
        if ($this->apiValidator($request->all(), $systemMatchRequest->rules())) {
            DB::beginTransaction();
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;

                
                $updated = SystemMatch::where('custom_id',$request->system_match_user_key)->update(['is_connected'=>'1']);

                if($updated)
                {
                   DB::commit();
                    $this->status = Response::HTTP_OK;
                    return ([
                        'data'  =>  NULL,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.update', ['entity' =>  __('Is Connected')]),
                        ]
                    ]);
                }
                else
                {
                     DB::commit();
                        $this->status = Response::HTTP_OK;
                        return ([
                            'data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.not_found', ['entity' =>  __('System Match User Key')]),
                            ]
                        ]);
                }
            } catch (ModelNotFoundException $exception) {
                DB::rollback();
                $this->status = Response::HTTP_OK;
                switch ($exception->getModel()) {
                    case 'App\Models\SystemMatch':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("System Match")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();
                $this->status = Response::HTTP_OK;
                $this->storeErrorLog($e, 'delete_match');
            }
        }
        return $this->returnResponse();
    }
}
