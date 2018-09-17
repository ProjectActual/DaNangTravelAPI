<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\Role;
use Auth;

class AdminMiddleware
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
        $user = Auth::guard('api')->user();

        if(!empty($user) && $user->roles->contains('name', ROLE::NAME['ADMINISTRATOR'])) {
            return $next($request);
        }

        return responses('You do not have access to the router', 401);

    }
}
