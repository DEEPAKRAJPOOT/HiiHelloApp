<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CheckApiUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if(!empty($user)){
            if($user->is_active == 'y'){
                return $next($request);
            }
            return response()->json(['msg'=>'You are blocked. please contact administrative', 'status' =>'0']);
        }
        return response()->json(['msg'=>'Login data not found.', 'status' =>'0']);
    }
}
