<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApiUser
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }

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

        if (!empty($user)) {
            if ($user->is_active == 'y' && $user->user_status == 'active') {
                return $next($request);
            }

            return response()->json([
                // 'data'  =>  [
                //     'max_date'  =>  array(),
                //     'min_date'  =>  $common_age->min_date ?? NULL,
                // ],
                'meta' => [
                    'api'       =>  $this->getVersion(),
                    'url'       =>  url()->current(),
                    'language'  =>  app()->getLocale(),
                    'message'   =>  $user->is_active === 'n' ? trans('api.in_active') : trans('api.self_inactive'),
                    'is_ban'    => $user->is_active === 'n',
                    'user_status'    => $user->user_status,
                ]
            ]);
        }
        return response()->json(['msg' => 'Login data not found.', 'status' => '0']);
    }
}
