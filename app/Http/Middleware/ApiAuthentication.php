<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthentication
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = \App\User::where('access_token', $token)->first();
        if ($user) {
            auth()->login($user);
            return $next($request);
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }
}