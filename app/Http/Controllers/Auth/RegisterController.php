<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Models\Sales\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    use CommonResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $message = 'Tạo tài khoản không thành công';

            if (isset($errors['email'][0]) && $errors['email'][0] == 'The email has already been taken.') {
                $message = 'Email đã tồn tại trong hệ thống';
            } elseif (isset($errors['phone_number'][0]) && $errors['phone_number'][0] == 'The phone number has already been taken.') {
                $message = 'Số điện thoại đã tồn tại trong hệ thống';
            }

            $response = $this->_formatBaseResponse(400, null, $message, ['errors' => $errors]);
            return response()->json($response, 400);
        } else {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create([
                'name' => $input['name'],
                'phone_number' => $input['phone_number'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            if (!$token = $user->tokens->where('revoked', false)->where('expires_at', '>', Carbon::now())->first()) {
                $token = $user->createToken('App');
            }

            $accessToken = $token->accessToken;
            $user->update(['access_token' => $accessToken]);
            $response = $this->_formatBaseResponse(200, $user, 'Tạo tài khoản thành công', ['accessToken' => $accessToken]);
            return response()->json($response);
        }
    }
}