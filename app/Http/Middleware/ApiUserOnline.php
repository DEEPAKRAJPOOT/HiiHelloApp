<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ApiUserOnline {
    public function handle(Request $request, Closure $next) {
        $user = Auth::user();
        if(!empty($user)){
            try{
                $user->last_online = now();
                $user->save();
            }catch(Exception $e){}
        }
        return $next($request);
    }
}