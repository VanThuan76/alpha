<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\Models\Sales\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends BaseController
{
    use CommonResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => [
                'required',
                'string',
                'max:255',
                'size:10',
                Rule::unique('users')->where(function ($query) {
                    $query->where('is_deleted', 0);
                }),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) {
                    $query->where('is_deleted', 0);
                }),
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsMessage = [];
            foreach ($errors as $key => $error) {
                $errorsMessage[$key] = [$error[0]];
            }

            $response = $this->_formatBaseResponse(422, null, "Tạo tài khoản không thành công", ['errors' => $errorsMessage]);
            return response()->json($response, 422);

        } else {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create([
                'name' => $input['name'],
                'phone_number' => $input['phone_number'],
                'email' => $input['email'],
                'password' => $input['password'],
                'status' => 0,
            ]);

            if (!$token = $user->tokens->where('revoked', false)->where('expires_at', '>', Carbon::now())->first()) {
                $token = $user->createToken('App');
            }

            $accessToken = $token->accessToken;
            $user->update(['access_token' => $accessToken]);
            $response = $this->_formatBaseResponse(200, null, 'Tạo tài khoản thành công', []);
            return response()->json($response);
        }
    }
}
