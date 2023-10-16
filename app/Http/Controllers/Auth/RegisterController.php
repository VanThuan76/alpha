<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Models\Sales\User;
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
            'phone_number' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $response = $this->_formatBaseResponse(400, null, 'Tạo tài khoản không thành công', ['errors' => $errors]);
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create([
            'name' => $input['name'],
            'phone_number' => $input['phone_number'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
        $accessToken = $user->createToken('MyApp')->accessToken;
        $user->update(['access_token' => $accessToken]);
        $response = $this->_formatBaseResponse(200, $user, 'Tạo tài khoản thành công', ['accessToken' => $accessToken]);
        return response()->json($response);
    }
}
