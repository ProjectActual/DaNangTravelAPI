<?php

namespace App\Http\Middleware;

use Closure;

use App\Entities\User;

class CheckBlockMiddware
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
        if($user->is_block == User::IS_BLOCK[1]) {
            return response()->json([
                'message'     => 'Tài khoản của bạn đã bị vô hiệu hóa',
                'status'      => 404
            ], 404);
        }

        return $next($request);
    }
}
