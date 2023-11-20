<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\Controller;
use App\Models\Sales\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    use CommonResponse;
    public function sendOTPByPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255',
        ]);
        $user = User::where("phone_number", $request->input('phone_number'))->first();
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $response = $this->_formatBaseResponse(400, null, 'Số điện thoại không có trong hệ thống', ['errors' => $errors]);
            return response()->json($response, 400);
        } else if(!$user) {
            $response = $this->_formatBaseResponse(400, null, 'Số điện thoại không chính xác', []);
            return response()->json($response, 400);
        }else{
            $response = $this->_formatBaseResponse(200, null, 'OTP đã được gửi tới số điện thoại của bạn', []);
            return response()->json($response, 200);
        }
    }
    public function verifyOTPByPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255',
            'otp' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsMessage = [];
            foreach ($errors as $key => $error) {
                $errorsMessage[$key] = [$error[0]];
            }
            $response = $this->_formatBaseResponse(422, null, "Xác thực không thành công", ['errors' => $errorsMessage]);
            return response()->json($response, 422);
        } else {
            $tokenIdentifier = $request->input('otp') . '_' . $request->input('phone_number');
            $user = User::where("phone_number", $request->input('phone_number'))->first();
            $otpToken = $user->createToken($tokenIdentifier);
            $user->otp_token = $otpToken->accessToken;
            $user->save();
            $result = [
                'data' => [
                    'token' => $otpToken->accessToken,
                ]
            ];
            $response = $this->_formatBaseResponse(200, $result, 'Xác thực thành công', []);
            return response()->json($response, 200);
        }
    }
    public function forgotPasswordByOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255',
            'token' => 'required|string|min:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsMessage = [];
            foreach ($errors as $key => $error) {
                $errorsMessage[$key] = [$error[0]];
            }
            $response = $this->_formatBaseResponse(422, null, "Đổi mật khẩu không thành công", ['errors' => $errorsMessage]);
            return response()->json($response, 422);
        }

        $user = User::where("phone_number", $request->input('phone_number'))
            ->where("otp_token", $request->input('token'))
            ->first();

        if (!$user) {
            $errorMessage = [];
            $existingUser = User::where("phone_number", $request->input('phone_number'))->first();
            if (!$existingUser) {
                $errorMessage['phone_number'] = 'Sai số điện thoại';
            }
            if (!$existingUser || $existingUser->otp_token !== $request->input('token')) {
                $errorMessage['token'] = 'Sai token';
            }

            $response = $this->_formatBaseResponse(422, null, "Đổi mật khẩu không thành công", ['errors' => $errorMessage]);
            return response()->json($response, 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user->password = $input['password'];

        $user->tokens()->where('revoked', false)->update(['revoked' => true]);

        $token = $user->createToken('App');
        $accessToken = $token->accessToken;
        $user->access_token = $accessToken;
        $user->save();

        $response = $this->_formatBaseResponse(200, null, 'Đổi mật khẩu thành công', []);
        return response()->json($response);
    }


    protected function broker()
    {
        return Password::broker('users');
    }
}
