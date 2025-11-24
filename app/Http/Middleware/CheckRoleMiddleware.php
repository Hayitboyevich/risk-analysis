<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthenticatedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->user()) {
            $this->user = Auth::guard('api')->user();
            if ($this->user)  $this->roleId = $this->user->getRoleFromToken();
            if (in_array($this->roleId, $this->user->roles->pluck('id')->toArray())) {
                return $next($request);
            }else{
                throw new UnauthenticatedException('Unauthenticated', 401);
            }
        }else{
            throw new UnauthenticatedException('Unauthenticated.', 401);
        }
    }
}
