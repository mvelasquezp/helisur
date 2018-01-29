<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class Superadmin
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
        if(Auth::user()->esAdmin()) {
            return $next($request);
        }
        else {
            return "No tiene permisos para acceder aquí";
        }
    }
}
