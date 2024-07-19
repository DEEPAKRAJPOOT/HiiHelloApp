<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineUsers;
use App\Models\{User, UsersMongoose};
use Exception;

class ApiUserOnline {
    public function handle(Request $request, Closure $next) {
        $user = Auth::user();
        if(!empty($user)){
            try{
                
                $user->last_online = now();
                $user->save();
                $user_id = $user->id;

                UsersMongoose::updateOne(
                    ['user_id' => $user->id], // Filter by user_id
                    ['$set' => ['last_online' => $user->last_online]] // Update last_online
                );
                /*
                $todayOnline = OnlineUsers::where('user_id',$user_id)->whereDate('last_online',now()->toDateString())->first();
                if($todayOnline){
                    OnlineUsers::where('user_id',$user_id)->whereDate('last_online','=',now()->toDateString())->update(['last_online'=>now()]);
                }else{
                    $onlineData['user_id'] = $user_id;
                    $onlineData['last_online'] = now();
                    OnlineUsers::create($onlineData);
                }
                */
            }catch(Exception $e){}
        }
        return $next($request);
    }
}