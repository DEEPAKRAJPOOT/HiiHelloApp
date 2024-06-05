<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineUsers;
use App\Models\User;
use Exception;

class ApiUserOnline {
    public function handle(Request $request, Closure $next) {
        $user = Auth::user();
        if(!empty($user)){
            try{
                
                $user->last_online = now();
                $user->save();
                $user_id = $user->id;
                
                $todayOnline = OnlineUsers::where('user_id',$user_id)->whereDate('last_online',now()->toDateString())->first();
                if($todayOnline){
                    OnlineUsers::where('user_id',$user_id)->whereDate('last_online','=',now()->toDateString())->update(['last_online'=>now()]);
                }else{
                    $onlineData['user_id'] = $user_id;
                    $onlineData['last_online'] = now();
                    OnlineUsers::create($onlineData);
                }
            }catch(Exception $e){}
        }
        return $next($request);
    }
}