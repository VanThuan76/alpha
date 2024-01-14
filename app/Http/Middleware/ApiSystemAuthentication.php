<?php

namespace App\Http\Middleware;
use App\Http\Response\CommonResponse;

use App\Models\AdminUser;
use Closure;

class ApiSystemAuthentication
{
    use CommonResponse;
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            $response = $this->_formatBaseResponse(401, null, "Không được xác thực", []);
            return response()->json($response, 401);
        }
    
        $user = AdminUser::where('access_token', $token)->first();
        if ($user) {
            auth()->login($user);
            return $next($request);
        }
    
        $response = $this->_formatBaseResponse(401, null, "Không được xác thực", []);
        return response()->json($response, 401);
    }
    
}