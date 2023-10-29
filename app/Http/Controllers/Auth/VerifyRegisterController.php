<?php

namespace App\Http\Controllers\Auth;

use App\Http\Response\CommonResponse;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

class VerifyRegisterController extends BaseController
{
    use CommonResponse;

    public function verifyRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'string', 'max:255'],
            'otp' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsMessage = [];
            foreach ($errors as $key => $error) {
                $errorsMessage[$key] = $error[0];
            }
            $response = $this->_formatBaseResponse(422, null, ['errors' => $errorsMessage], []);
            return response()->json($response, 422);
            
        } else {
            $user = User::where('phone_number', $request['phone_number'])->first();
            $accessToken = [
                'access_token' => $user->access_token
            ];
            $response = $this->_formatBaseResponse(200, $accessToken, 'Xác thực thành công', []);
            return response()->json($response);
        }
    }
}
