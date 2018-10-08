<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\User;
use App\Entities\Role;

class CredentialMiddleware
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
        return $next($request);
    }
}
