<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\Role;
use Auth;
use App;

class AuthenticationMiddleware
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

        if($user->hasRole(Role::NAME[1]) || $user->hasRole(Role::NAME[2])) {
            return $next($request);
        }

        return response()->json([
            'message'     => 'You do not have access to the router',
            'status'      => 401
        ], 401);

    }
}
