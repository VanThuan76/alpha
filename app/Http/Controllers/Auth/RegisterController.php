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
            $errorsFormat = [
                'errors' => [
                    isset($errors['email'][0]) && $errors['email'][0] ? 'Trường email đã có trong cơ sở dữ liệu' : "",
                    isset($errors['phone_number'][0]) && $errors['phone_number'][0] ? 'Trường phone number đã có trong cơ sở dữ liệu' : "",
                ]
            ];
            $response = $this->_formatBaseResponse(422, null, $errorsFormat, []);
            return response()->json($response, 422);
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
            $result = [
                'access_token' => $accessToken
            ];
            $response = $this->_formatBaseResponse(200, $result, 'Tạo tài khoản thành công', []);
            return response()->json($response);
        }
    }
}
