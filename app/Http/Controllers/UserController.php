<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use CommonResponse;

    public function update(Request $request)
    {
        $accessToken = $request->header('Authorization');
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:22'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $verify = 0;
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->avatar = $request->avatar;
            $user->birthdate = date("Y-m-d", strtotime($request->birthdate));

            if ($user->name && $user->phone && $user->birthdate) {
                if ($user->verify == 0) {
                    if ($user->package_type == 1) {
                        $user->expire_time = Carbon::parse($user->expire_time)->addMonths(3);
                    } else {
                        $user->package_type = 1;
                        $user->expire_time = Carbon::now()->addMonths(3);
                    }
                    $verify = 1;
                    $user->verify = 1;
                }
            }

            if ($request->password != '******') {
                $user->password = Hash::make($request->password);
            }

            return response()->json([
                'user' => $user,
                'verify' => $verify
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
