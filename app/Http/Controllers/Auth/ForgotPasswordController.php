<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    use CommonResponse;
    public function forgotPasswordByPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $response = $this->_formatBaseResponse(400, null, 'Số điện thoại không có trong hệ thống', ['errors' => $errors]);
            return response()->json($response, 400);
        }

        $response = $this->broker()->sendResetLink(
            $request->only('phone_number')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Liên kết đặt lại mật khẩu đã được gửi tới số điện thoại của bạn'])
            : response()->json(['error' => trans($response)], 400);
    }

    protected function broker()
    {
        return Password::broker('users');
    }
}
