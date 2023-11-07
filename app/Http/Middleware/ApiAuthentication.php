<?php

namespace App\Http\Middleware;
use App\Http\Response\CommonResponse;

use Closure;

class ApiAuthentication
{
    use CommonResponse;
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = \App\User::where('access_token', $token)->first();
        if ($user) {
            auth()->login($user);
            return $next($request);
        }
        $response = $this->_formatBaseResponse(403, null, "Không được xác thực", []);
        return response()->json($response, 422);
    }
}