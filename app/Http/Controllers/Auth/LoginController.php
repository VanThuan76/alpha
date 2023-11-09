<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use CommonResponse;
    public function login(Request $request)
    {
        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            $user = Auth::user();
            if($user->status == 0){
                $response = $this->_formatBaseResponse(401, null, 'Tài khoản chưa xác thực', []);
                return response()->json($response, 401);
            }else{
                $accessToken = [
                    'access_token' => $user->access_token
                ];
                $response = $this->_formatBaseResponse(200, $accessToken, 'Đăng nhập thành công', []);
                return response()->json($response);
            }
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Sai số điện thoại hoặc mật khẩu', []);
            return response()->json($response, 401);
        }
    }
}
