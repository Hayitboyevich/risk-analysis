<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\JsonResponse;

class Authenticate
{

    public function handle($request, Closure $next)
    {
        try {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        } catch (TokenExpiredException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token invalid'
            ], 401);
        } catch (JWTException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Token not provided'
            ], 401);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
