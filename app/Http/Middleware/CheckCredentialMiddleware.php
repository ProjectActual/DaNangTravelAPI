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
        if(Entrust::hasRole(Role::NAME[2]))
        {
            if($user->active == User::ACTIVE[1]) {
                return response()->json([
                    'message'     => 'Tài Khoản của bạn cần được xác thực qua email.',
                    'status'      => 400,
                    'type'        => 'authen_email'
                ], 400);
            } else if($user->active == User::ACTIVE[2]) {
                return response()->json([
                    'message'     => 'Tài khoản của bạn chưa được Quản Trị Viên duyệt.',
                    'status'      => 400,
                    'type'        => 'approve'
                ], 400);
            } else if($user->active == User::ACTIVE[4]) {
                return response()->json([
                    'message'     => 'Tài khoản của bạn đã bị vô hiệu hóa.',
                    'status'      => 400,
                    'type'        => 'locked'
                ], 400);
            }
        }

        return $next($request);
    }
}
