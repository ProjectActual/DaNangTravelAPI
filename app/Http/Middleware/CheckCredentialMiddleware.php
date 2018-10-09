<?php

namespace App\Http\Middleware;

use Closure;
use Entrust;

use App\Entities\Role;
use App\Entities\User;

class CheckCredentialMiddleware
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
        $user = $request->user();

        if(Entrust::hasRole(Role::NAME[2]) && $user->active == User::ACTIVE[2])
        {
            return response()->json([
                'message'     => 'Tài Khoản của bạn cần được xác thực qua email.',
                'status'      => 400
            ], 400);
        }

        if(Entrust::hasRole(Role::NAME[2]) && $user->admin_active == User::ADMIN_ACTIVE[2])
        {
            return response()->json([
                'message'     => 'Tài khoản của bạn chưa được Quản Trị Viên duyệt.',
                'status'      => 400
            ], 400);
        }

        return $next($request);
    }
}
