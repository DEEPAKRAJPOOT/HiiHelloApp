<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

class CheckPermit
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
        $routeName = Route::currentRouteName();
        $method = substr($routeName, strrpos($routeName, '.') + 1);
        $routeName = substr($routeName, 0, strrpos($routeName, '.'));
        $method = ($method != '' ? $method : $routeName);
        $permission = '';
        
        try {
            if (config('utility.caching') && Redis::exists('active.routes.' . $routeName)) {
                $role = json_decode((Redis::get('active.routes.' . $routeName)));
            } else {
                $role = Role::where('route', 'like', $routeName . '%')->first();
                if (config('utility.caching')) Redis::set('active.routes.' . $routeName, json_encode($role));
            }
        } catch (\Exception $ex) {
            $role = \Cache::remember('active.routes.' . $routeName, (60 * 60), function () use ($routeName) {
                return $role = Role::where('route', 'like', $routeName . '%')->first();
            });
        }


        // Permissions 
        $access = ['index', 'listing', 'selection-listing', 'showSetting', 'change-setting', 'trashed', 'trasheddata','actor-list','singer-list','unde-review','genderupdate','bulk_gender_update','bulk-approve','dashboardupdate','bulk_photo_verification','bulk_email_verification','deleted','filters','under-review-listing','refund-transaction','merge-location','mergelocations','duplicate-location','duplicate-location-listing'];
        $add = ['store', 'create', 'send-bulk'];
        $update = ['edit', 'update','get-fields','preview','publish'];
        $view = ['show','chart-data','reporting-data','table-data'];
        $delete = ['destroy'];
        $restore = ['restore'];

        if (in_array($method, $access)) $permission = 'access';
        elseif (in_array($method, $add)) $permission = 'add';
        elseif (in_array($method, $update)) $permission = 'edit';
        elseif (in_array($method, $delete)) $permission = 'delete';
        elseif (in_array($method, $restore)) $permission = 'restore';
        elseif (in_array($method, $view)) $permission = 'view';
        
        if (!empty($permission)) {
            $current_permission = unserialize($request->user()->permissions);
            // dd($current_permission);
            if (
                array_key_exists($role->id, $current_permission)
                && !empty($current_permission[$role->id]['permissions'])
                && in_array($permission, explode(',', $current_permission[$role->id]['permissions']))
            )
            return $next($request);
        }
        return redirect('admin/dashboard');
    }
}
