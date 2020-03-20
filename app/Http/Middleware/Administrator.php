<?php


namespace App\Http\Middleware;


use Illuminate\Support\Facades\Auth;

class Administrator
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = null)
    {
        if(Auth::user()->admin == 0){
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
