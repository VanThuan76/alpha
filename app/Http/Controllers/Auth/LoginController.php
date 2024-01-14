<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUser;

class LoginController extends Controller
{
    use CommonResponse;
    public function login(Request $request)
    {
        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->is_deleted == 1) {
                $response = $this->_formatBaseResponse(401, null, 'Tài khoản đã đóng. Vui lòng tới Chi nhánh gần nhất hoặc liên hệ Tổng đài để được hỗ trợ.', []);
                return response()->json($response, 401);
            } else if ($user->status == 0) {
                $response = $this->_formatBaseResponse(401, null, 'Tài khoản chưa xác thực', []);
                return response()->json($response, 401);
            } else {
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
    public function loginUserSystem(Request $request)
    {
        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();
            if ($user->status == 0) {
                $response = $this->_formatBaseResponse(401, null, 'Tài khoản đã bị đóng vui lòng liên hệ admin', []);
                return response()->json($response, 401);
            } else {
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