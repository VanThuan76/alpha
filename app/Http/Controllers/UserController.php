<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Sales\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use CommonResponse;

    public function get()
    {
        $user = auth('api')->user();
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => $user->personal_technician_id,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => $user->personal_branch_id,
                ],
                'full_name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'customer_type' => $user->customer_type,
                'points' => $user->point,
                'bonus_coins' => $user->bonus_coins,
                'avatar_path' => $user->photo
            ]
        ];
        if ($user) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
    public function update(Request $request)
    {
        $user = auth('api')->user();

        $user->name = $request->input('full_name');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->sex = $request->input('gender');
        $user->dob = date("Y-m-d", strtotime($request->input('birthdate')));
        $user->personal_branch_id = $request->input('personal_branch_id');
        $user->personal_technician_id = $request->input('personal_technician_id');
        $user->photo = $request->input('avatar');

        if ($user->isDirty(['full_name', 'email', 'address', 'gender', 'birthdate', 'personal_branch_id', 'personal_technician_id', 'avatar'])) {
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
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => $user->personal_technician_id,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => $user->personal_branch_id,
                ],
                'full_name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'customer_type' => $user->customer_type,
                'points' => $user->point,
                'bonus_coins' => $user->bonus_coins,
                'avatar_path' => $user->photo
            ]
        ];
        if ($user) {
            $response = $this->_formatBaseResponse(200, $result, 'Cập nhật thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Cập nhật không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
    public function changePassword(Request $request)
    {
        $user = auth('api')->user();
        $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($request->input('old_password'), $user->password)) {
            $response = $this->_formatBaseResponse(400, null, 'Mật khẩu cũ không chính xác', []);
            return response()->json($response, 400);
        } else {
            $user->password = Hash::make($request->input('password'));
            $user->save();
            $response = $this->_formatBaseResponse(200, null, 'Thay đổi mật khẩu thành công', []);
            return response()->json($response);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = User::where('phone_number', 'LIKE', '%' . $query . '%')->get();
        return view('components.search_result', ['results' => $results]);
    }
    
}
