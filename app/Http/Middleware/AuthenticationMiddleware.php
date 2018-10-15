<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Response;
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
            'message'     => 'Bạn không có quyền truy cập vào đường dẫn',
            'status'      => Response::HTTP_UNAUTHORIZED
        ], Response::HTTP_UNAUTHORIZED);
    }
}
