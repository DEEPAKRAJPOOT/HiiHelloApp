<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\Like;
use App\Models\DisLike;
use App\Models\SystemMatch;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;

class UserTreeController extends Controller
{
    public function index()
    {
        // echo Carbon::today(); echo "<br>";
        // echo Carbon::now()->month; echo "<br>";
        // echo Carbon::now()->year; echo "<br>";
        // echo Carbon::now()->endOfWeek(); die();
        return view('admin.pages.tree.index')->with(['custom_title' => 'Users Tree']);
    }

    public function listing(Request $request)
    {

        extract($this->DTFilters($request->all()));

        DB::enableQueryLog();

        $flgPendingProfile = $request->flgPendingProfile;

        $records = [];
        $users = User::with('userTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('account_id', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%")
                    ->orWhereHas('userTransDefault', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            });
        }


        $count = $users->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

       
        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();

        // echo "<pre>"; print_r($users->toArray()); die();
        // dd(DB::getQueryLog());
        // exit();

        foreach ($users as $user) {
            if (!empty($request->filter_types)) {
                if ($request->filter_types == 1) {
                    // $users = $users->where("created_at",Carbon::today());

                    $total_like_send = Like::where("liker_id",$user->id)->where("created_at",Carbon::today())->count();
                    $total_like_received = Like::where("user_id",$user->id)->where("created_at",Carbon::today())->count();
                    $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->where("created_at",Carbon::today())->count();
                    $total_dislike_received = DisLike::where("user_id",$user->id)->where("created_at",Carbon::today())->count();

                    $auth_id = $user->id;
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
                    ->where("likes.created_at",Carbon::today())
                    ->count();
                }
                if ($request->filter_types == 2) {
                    // $users = $users->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                    $total_like_send = Like::where("liker_id",$user->id)->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $total_like_received = Like::where("user_id",$user->id)->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
                    $total_dislike_received = DisLike::where("user_id",$user->id)->whereBetween('created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

                    $auth_id = $user->id;
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
                    ->whereBetween('likes.created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->whereIsActive('y')
                    ->count();

                }
                if ($request->filter_types == 3) {
                    // $users = $users->whereMonth("created_at",Carbon::now()->month);

                    $total_like_send = Like::where("liker_id",$user->id)->whereMonth("created_at",Carbon::now()->month)->count();
                    $total_like_received = Like::where("user_id",$user->id)->whereMonth("created_at",Carbon::now()->month)->count();
                    $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->whereMonth("created_at",Carbon::now()->month)->count();
                    $total_dislike_received = DisLike::where("user_id",$user->id)->whereMonth("created_at",Carbon::now()->month)->count();

                    $auth_id = $user->id;
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
                    ->whereMonth("likes.created_at",Carbon::now()->month)
                    ->whereIsActive('y')  
                    ->count();

                }
                if ($request->filter_types == 4) {
                    // $users = $users->whereYear("created_at",Carbon::now()->year);

                    $total_like_send = Like::where("liker_id",$user->id)->whereYear("created_at",Carbon::now()->year)->count();
                    $total_like_received = Like::where("user_id",$user->id)->whereYear("created_at",Carbon::now()->year)->count();
                    $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->whereYear("created_at",Carbon::now()->year)->count();
                    $total_dislike_received = DisLike::where("user_id",$user->id)->whereYear("created_at",Carbon::now()->year)->count();

                    $auth_id = $user->id;
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
                    ->whereYear("likes.created_at",Carbon::now()->year)
                    ->whereIsActive('y')  
                    ->count();
                }
                if ($request->filter_types == 5 && $request->from_date != '' && $request->to_date != '') {
                    $total_like_send = Like::where("liker_id",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();
                    $total_like_received = Like::where("user_id",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();
                    $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();
                    $total_dislike_received = DisLike::where("user_id",$user->id)->where("created_at",">=",$request->from_date)->where("created_at","<=",$request->to_date)->count();

                    $auth_id = $user->id;
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
                    ->where("likes.created_at",">=",$request->from_date)->where("likes.created_at","<=",$request->to_date)
                    ->whereIsActive('y')  
                    ->count();

                }
            }
            else
            {
                $total_like_send = Like::where("liker_id",$user->id)->count();
                $total_like_received = Like::where("user_id",$user->id)->count();
                $total_dislike_send = DisLike::where("dis_liker_id",$user->id)->count();
                $total_dislike_received = DisLike::where("user_id",$user->id)->count();

                $auth_id = $user->id;
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
                    ->count();

            }
            $is_signup_mode = "";
            if (!empty($user->facebook_id) && $user->is_social_user == 'y') {
                $is_signup_mode = "Facebook";
            }else if (!empty($user->google_id) && $user->is_social_user == 'y') {
                $is_signup_mode = "Google";
            }else if (!empty($user->apple_id) && $user->is_social_user == 'y') {
                $is_signup_mode = "Apple";
            }else {
                $is_signup_mode = "Phone";
            }

            $records['data'][] = [
                'profile_photo' => view('admin.layouts.includes.photos_verify')->with(['user_id' => $user->id,'profile_photo' => $user->profile_photo  ?? 'N/A', 'is_profile_photo' => 1, 'is_verify_photo' => 0])->render(),
                'verify_photo' => view('admin.layouts.includes.photos_verify')->with(['user_id' => $user->id,'verify_photo' => $user->verify_photo  ?? 'N/A', 'is_profile_photo' => 0, 'is_verify_photo' => 1])->render(),
                'account_id' => $user->account_id ?? "N/A",
                'full_name' =>  $user->userTransDefault ? $user->userTransDefault->full_name : "N/A",
                'mode_of_registration' =>  $is_signup_mode,
                'created_at' => date('Y-m-d h:i A', strtotime($user->created_at)) ?? 'N/A',
                'send_total_like' => $total_like_send,
                'received_total_like' => $total_like_received,
                'send_total_dislikes' => $total_dislike_send,
                'received_total_dislikes' => $total_dislike_received,
                'total_matches' => $likes,
                'action' => view('admin.layouts.includes.user_match')->with(['custom_title' => 'User Match Data', 'id' => $user->id], $user)->render(),
            ];
        }
        // dd($records);
        return $records;
    }

    public function usermatchlisting(Request $request)
    {
        $auth_id = $request->user_id;
        // $auth_id = 1493;
        extract($this->DTFilters($request->all()));

        DB::enableQueryLog();

        $flgPendingProfile = $request->flgPendingProfile;

        $records = [];

        $likes = DB::table('likes')
            ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
            // ->with('userTranslation:id,locale,user_id,full_name')
            ->join("likes as like", function ($q) {
                $q->on("likes.liker_id", "=", "like.user_id");
                $q->on("like.liker_id", "=", "likes.user_id");
            })
            ->join('users', function ($q) {
                $q->on('users.id', "=", "likes.user_id");
            })
            ->join('user_translations', function ($q) {
                $q->on('user_translations.user_id', "=", "users.id");
            })
            ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
            ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
            ->where("likes.user_id", '!=', $auth_id)
            // ->where('users.id', '!=', $auth_id)   
            ->whereIsActive('y'); 

        if ($search != '') {
            $likes->where(function ($query) use ($search) {
                $query->Where('likes.created_at', 'like', "%{$search}%")
                    ->orWhere('user.gender', 'like', "%{$search}%")
                    ->orWhere('user_translations.full_name', 'like', "%{$search}%");
                    // ->orWhereHas('userTransDefault', function ($q) use ($search) {
                    //     $q->where('full_name', 'like', "%{$search}%");
                    // });
            });
        }

        $count = $likes->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

       
        $likes = $likes->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $likes = $likes->get();    
        echo "<pre>"; print_r($likes); die();
        $records['data'] = $likes;
        return $records;
    }

    public function get_user_match_data(Request $request)
    {
        $auth_id = $request->user_id;
        if (!empty($request->filter_types)) {
            if ($request->filter_types == 1) {
                $likes = DB::table('likes')
                ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                // ->with('userTranslation:id,locale,user_id,full_name')
                ->join("likes as like", function ($q) {
                    $q->on("likes.liker_id", "=", "like.user_id");
                    $q->on("like.liker_id", "=", "likes.user_id");
                })
                ->join('users', function ($q) {
                    $q->on('users.id', "=", "likes.user_id");
                })
                ->join('user_translations', function ($q) {
                    $q->on('user_translations.user_id', "=", "users.id");
                })
                ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
                ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                ->where("likes.user_id", '!=', $auth_id)
                // ->where('users.id', '!=', $auth_id)   
                ->where("likes.created_at",Carbon::today())
                ->whereIsActive('y')  
                ->get();
            }

            if ($request->filter_types == 2) {
                $likes = DB::table('likes')
                ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                // ->with('userTranslation:id,locale,user_id,full_name')
                ->join("likes as like", function ($q) {
                    $q->on("likes.liker_id", "=", "like.user_id");
                    $q->on("like.liker_id", "=", "likes.user_id");
                })
                ->join('users', function ($q) {
                    $q->on('users.id', "=", "likes.user_id");
                })
                ->join('user_translations', function ($q) {
                    $q->on('user_translations.user_id', "=", "users.id");
                })
                ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
                ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                ->where("likes.user_id", '!=', $auth_id)
                // ->where('users.id', '!=', $auth_id)   
                ->whereBetween('likes.created_at',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereIsActive('y')  
                ->get();
            }

            if ($request->filter_types == 3) {
                $likes = DB::table('likes')
                ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                // ->with('userTranslation:id,locale,user_id,full_name')
                ->join("likes as like", function ($q) {
                    $q->on("likes.liker_id", "=", "like.user_id");
                    $q->on("like.liker_id", "=", "likes.user_id");
                })
                ->join('users', function ($q) {
                    $q->on('users.id', "=", "likes.user_id");
                })
                ->join('user_translations', function ($q) {
                    $q->on('user_translations.user_id', "=", "users.id");
                })
                ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
                ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                ->where("likes.user_id", '!=', $auth_id)
                // ->where('users.id', '!=', $auth_id)
                ->whereMonth("likes.created_at",Carbon::now()->month)  
                ->whereIsActive('y')  
                ->get();
            }

            if ($request->filter_types == 4) {
                $likes = DB::table('likes')
                ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                // ->with('userTranslation:id,locale,user_id,full_name')
                ->join("likes as like", function ($q) {
                    $q->on("likes.liker_id", "=", "like.user_id");
                    $q->on("like.liker_id", "=", "likes.user_id");
                })
                ->join('users', function ($q) {
                    $q->on('users.id', "=", "likes.user_id");
                })
                ->join('user_translations', function ($q) {
                    $q->on('user_translations.user_id', "=", "users.id");
                })
                ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
                ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                ->where("likes.user_id", '!=', $auth_id)
                // ->where('users.id', '!=', $auth_id)   
                ->whereYear("likes.created_at",Carbon::now()->year)
                ->whereIsActive('y')  
                ->get();
            }

            if ($request->filter_types == 5 && $request->from_date != '' && $request->to_date != '') {
                $likes = DB::table('likes')
                ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
                // ->with('userTranslation:id,locale,user_id,full_name')
                ->join("likes as like", function ($q) {
                    $q->on("likes.liker_id", "=", "like.user_id");
                    $q->on("like.liker_id", "=", "likes.user_id");
                })
                ->join('users', function ($q) {
                    $q->on('users.id', "=", "likes.user_id");
                })
                ->join('user_translations', function ($q) {
                    $q->on('user_translations.user_id', "=", "users.id");
                })
                ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
                ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
                ->where("likes.user_id", '!=', $auth_id)
                // ->where('users.id', '!=', $auth_id)  
                ->where("likes.created_at",">=",$request->from_date)->where("likes.created_at","<=",$request->to_date) 
                ->whereIsActive('y')  
                ->get();
            }
        }
        else
        {
            $likes = DB::table('likes')
            ->select("likes.created_at","user_translations.full_name","users.id","users.gender",DB::raw("DATE_FORMAT(likes.created_at, '%d-%m-%Y %h:%i:%s') as created_at"))
            // ->with('userTranslation:id,locale,user_id,full_name')
            ->join("likes as like", function ($q) {
                $q->on("likes.liker_id", "=", "like.user_id");
                $q->on("like.liker_id", "=", "likes.user_id");
            })
            ->join('users', function ($q) {
                $q->on('users.id', "=", "likes.user_id");
            })
            ->join('user_translations', function ($q) {
                $q->on('user_translations.user_id', "=", "users.id");
            })
            ->where("user_translations.locale", '=', 'en')                //  To only get users details who likes current user
            ->where("likes.liker_id", '=', $auth_id)                //  To only get users details who likes current user
            ->where("likes.user_id", '!=', $auth_id)
            // ->where('users.id', '!=', $auth_id)   
            ->whereIsActive('y')  
            ->get();    
        }
        
        return $likes;
    }

    public function filters(Request $request)
    {
        $records     = [];
        $total_like_male = array();
        $total_female_like = array();
        $total_male_dislikes = array();
        $total_female_dislikes = array();
        $total_male_system_match = array();
        $total_female_system_match = array();
        $total_male_org_match = array();
        $total_female_org_match = array();

        if ($request->filter_type == 1) {

            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("likes.user_id")
                    ->get();

            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                    ->groupBy("system_match.user_id")
                    ->get();

            // orgmatch
            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                            ->get();
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y-m-d').'%')
                            ->get();
        }
        else if ($request->filter_type == 2) {

            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->whereBetween("likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->whereBetween("likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("likes.user_id")
                    ->get();

            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->whereBetween("dis_likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->whereBetween("dis_likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->whereBetween("system_match.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->whereBetween("system_match.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                    ->groupBy("system_match.user_id")
                    ->get();

            // orgmatch
            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->whereBetween("likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                            ->get();
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->whereBetween("likes.created_at",[Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
                            ->get();

        }
        else if ($request->filter_type == 3) {

            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("likes.user_id")
                    ->get();

            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                    ->groupBy("system_match.user_id")
                    ->get();

            // orgmatch
            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                            ->get();
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('m').'%')
                            ->get();
        }
        else if ($request->filter_type == 4) {
            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("likes.user_id")
                    ->get();

            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->where("dis_likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->where("system_match.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                    ->groupBy("system_match.user_id")
                    ->get();

            // orgmatch
            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                            ->get();
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->where("likes.created_at","LIKE",'%'.Carbon::now()->format('Y').'%')
                            ->get();
        }
        else if ($request->filter_type == 5) {

            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->where("likes.created_at",">=",$request->fromdate_search)->where("likes.created_at","<=",$request->todate_search)
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->where("likes.created_at",">=",$request->fromdate_search)->where("likes.created_at","<=",$request->todate_search)
                    ->groupBy("likes.user_id")
                    ->get();

            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->where("dis_likes.created_at",">=",$request->fromdate_search)->where("dis_likes.created_at","<=",$request->todate_search)
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->where("dis_likes.created_at",">=",$request->fromdate_search)->where("dis_likes.created_at","<=",$request->todate_search)
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->where("system_match.created_at",">=",$request->fromdate_search)->where("system_match.created_at","<=",$request->todate_search)
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->where("system_match.created_at",">=",$request->fromdate_search)->where("system_match.created_at","<=",$request->todate_search)
                    ->groupBy("system_match.user_id")
                    ->get();

            // orgmatch
            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->where("likes.created_at",">=",$request->fromdate_search)->where("likes.created_at","<=",$request->todate_search)
                            ->get();
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->where("likes.created_at",">=",$request->fromdate_search)->where("likes.created_at","<=",$request->todate_search)
                            ->get();
        }
        else
        {   
            // like
            $total_like_male = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Male")
                    ->groupBy("likes.user_id")
                    ->get();
            $total_female_like = Like::select('likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","likes.user_id")
                    ->where("users.gender","Female")
                    ->groupBy("likes.user_id")
                    ->get();


            //dislike
            $total_male_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Male")
                    ->groupBy("dis_likes.user_id")
                    ->get();
            $total_female_dislikes = DisLike::select('dis_likes.id as id','users.gender as gender')
                    ->join("users","users.id","=","dis_likes.user_id")
                    ->where("users.gender","Female")
                    ->groupBy("dis_likes.user_id")
                    ->get();

            // system match
            $total_male_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Male")
                    ->groupBy("system_match.user_id")
                    ->get();
            $total_female_system_match = SystemMatch::select('system_match.id as id','users.gender as gender')
                    ->join("users","users.id","=","system_match.user_id")
                    ->where("users.gender","Female")
                    ->groupBy("system_match.user_id")
                    ->get();

            $total_male_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Male")
                            ->get();
            
            $total_female_org_match = DB::table('likes')
                            ->select('likes.liker_id as liker_id','likes.user_id as user_id','users.id as id','users.gender as gender')
                            ->join("likes as like", function ($q) {
                                $q->on("likes.liker_id", "=", "like.user_id");
                                $q->on("like.liker_id", "=", "likes.user_id");
                            })
                            ->join('users', function ($q) {
                                $q->on('users.id', "=", "likes.user_id");
                            })
                            ->where("users.gender","Female")
                            ->get();

            //old data
            // $total_likes = Like::count();
            // $total_dislikes = DisLike::count();
            // $total_system_match = SystemMatch::count();
            // $total_org_match = DB::table('likes')
            //                 ->join("likes as like", function ($q) {
            //                     $q->on("likes.liker_id", "=", "like.user_id");
            //                     $q->on("like.liker_id", "=", "likes.user_id");
            //                 })
            //                 ->join('users', function ($q) {
            //                     $q->on('users.id', "=", "likes.user_id");
            //                 })
            //                 ->count();
        }

        $records['total_like_male'] = number_format(count($total_like_male));
        $records['total_female_like'] = number_format(count($total_female_like));
        $records['total_male_dislikes'] = number_format(count($total_male_dislikes));
        $records['total_female_dislikes'] = number_format(count($total_female_dislikes));
        $records['total_male_system_match'] = number_format(count($total_male_system_match));
        $records['total_female_system_match'] = number_format(count($total_female_system_match));
        $records['total_male_org_match'] = number_format(count($total_male_org_match));
        $records['total_female_org_match'] = number_format(count($total_female_org_match));

        return $records;
    }

    public function top_usertree()
    {

        $male_users_records     = [];
        $female_users_records     = [];
        $male_users = User::select('users.id as id','users.account_id as account_id','users.contact_no as contact_no','users.gender as gender',DB::raw("count(likes.user_id) as total_likes"));
        $male_users = $male_users->join("likes","likes.user_id","=","users.id");
        $male_users = $male_users->where("users.gender","Male");
        $male_users = $male_users->with('userTransDefault');
        $male_users = $male_users->groupBy('users.id');
        $male_users = $male_users->orderby('total_likes','DESC');
        $male_users = $male_users->limit(5);
        $male_users = $male_users->get();
        // echo "<pre>"; print_r($male_users->toArray()); die();
        if (count($male_users) > 0) {
            foreach ($male_users as $key => $user) {
                $chk = Like::where("liker_id",$user->id)->count();
                $male_users_records[] = [
                    'account_id' => $user->account_id ?? "N/A",
                    'contact_no' => $user->contact_no ?? "N/A",
                    'full_name'  => $user->full_name ?? "N/A",
                    'total_likes'=> $user->total_likes ?? "N/A",
                    'total_match'=> $chk ?? 0,
                ];
            }
        }

        $female_users = User::select('users.id as id','users.account_id as account_id','users.contact_no as contact_no','users.gender as gender',DB::raw("count(likes.user_id) as total_likes"));
        $female_users = $female_users->join("likes","likes.user_id","=","users.id");
        $female_users = $female_users->where("users.gender","Female");
        $female_users = $female_users->with('userTransDefault');
        $female_users = $female_users->groupBy('users.id');
        $female_users = $female_users->orderby('total_likes','DESC');
        $female_users = $female_users->limit(5);
        $female_users = $female_users->get();
        // echo "<pre>"; print_r($female_users->toArray()); die();
        if (count($female_users) > 0) {
            foreach ($female_users as $key => $user) {
                $chk = Like::where("liker_id",$user->id)->count();
                $female_users_records[] = [
                    'account_id' => $user->account_id ?? "N/A",
                    'contact_no' => $user->contact_no ?? "N/A",
                    'full_name'  => $user->full_name ?? "N/A",
                    'total_likes'=> $user->total_likes ?? "N/A",
                    'total_match'=> $chk ?? 0,
                ];
            }
        }

        // echo "<pre>"; print_r($male_users_records); die();
        return view('admin.pages.tree.toplist', compact('male_users_records','female_users_records'))->with(['custom_title' => __('Top 5 User Tree')]);

    }
}
