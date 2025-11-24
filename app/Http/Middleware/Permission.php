<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Permission
{

    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        $active_role = (int)$user->getRoleFromToken();

        if (!$active_role) {
            abort(400, 'Foydalanuvchi role ko\'rsatilmagan!');
        }

        if (!$user->hasPermissionForRole($permission, $active_role)) {
            return response()->json([
                'success' => false,
                'message' => 'Amalni bajarish huquqi mavjud emas.'
            ],403);
        }

        return $next($request);
    }
}
