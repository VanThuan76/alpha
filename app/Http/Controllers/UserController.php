<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use CommonResponse;

    public function get()
    {
        $user = auth('api')->user();
        unset($user->access_token);

        if ($user) {
            $response = $this->_formatBaseResponse(200, $user, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
    public function update(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'address' => 'required',
            'gender' => 'required',
            'birthdate' => 'required',
            'personal_branch_id' => 'required',
            'personal_technician_id' => 'required',
            'avatar' => 'required',
        ]);

        $user->address = $request->input('address');
        $user->sex = $request->input('gender');
        $user->dob = date("Y-m-d", strtotime($request->input('birthdate')));
        $user->personal_branch_id = $request->input('personal_branch_id');
        $user->personal_technician_id = $request->input('personal_technician_id');
        $user->photo = $request->input('avatar');

        if ($user->isDirty(['address', 'gender', 'birthdate', 'personal_branch_id', 'personal_technician_id', 'avatar'])) {
            if ($user->verify === 0) {
                if ($user->package_type == 1) {
                    $user->expire_time = now()->addMonths(3);
                } else {
                    $user->package_type = 1;
                    $user->expire_time = now()->addMonths(3);
                }
                $user->verify = 1;
            }
        }

        $user->save();
        unset($user->access_token);
        $response = $this->_formatBaseResponse(200, $user, 'Cập nhật thành công', []);
        return response()->json($response);
    }
    public function changePassword(Request $request)
    {
        $user = auth('api')->user();
        $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($request->input('old_password'), $user->password)) {
            $response = $this->_formatBaseResponse(400, 'Mật khẩu cũ không chính xác', null, []);
            return response()->json($response, 400);
        } else {
            $user->password = Hash::make($request->input('password'));
            $user->save();
            $response = $this->_formatBaseResponse(200, null, 'Thay đổi mật khẩu thành công', []);
            return response()->json($response);
        }
    }
}
