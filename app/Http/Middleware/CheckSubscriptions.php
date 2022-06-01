<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class CheckSubscriptions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( Auth::user()->subscription_end_date <= \Carbon\Carbon::now() ) {
            if ($request->expectsJson()) {

                $controller = new Controller();
                $controller->response['meta']['message'] = trans('api.subscription_required');
                $controller->response['meta']['url'] = url()->current();
                $controller->response['meta']['api'] = request()->route()->controller->getVersion();
           	 	$controller->response['meta']['language'] = app()->getLocale();
                $controller->status = Response::HTTP_FORBIDDEN;     

                return $controller->returnResponse();
            } else {
                flash('Your account has been deactivated by admin!')->warning()->important();
                Auth::logout();
                return redirect(route('login'));
            }
        }
        return $next($request);
    }
}
