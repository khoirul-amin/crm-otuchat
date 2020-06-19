<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class MenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $id_permission)
    {
        $permission = Session::get('permission');
        $data = array_search($id_permission, explode(",",$permission->id_submenu));
        if($data === false){
            return redirect('/home');
        }
        return $next($request);
    }
}
