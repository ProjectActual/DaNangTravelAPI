<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Response;
use Entrust;

use App\Entities\Role;

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
        if(Entrust::hasRole(Role::NAME[1])) {
            return $next($request);
        }

        return response()->json([
            'message'     => 'You do not have access to the router',
            'status'      => Response::HTTP_UNAUTHORIZED
        ], Response::HTTP_UNAUTHORIZED);
    }
}
