<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Branch;
use App\Models\Hrm\Employee;
use App\Models\Sales\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use CommonResponse;
    public function get()
    {
        $user = auth()->user();
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => Employee::where('id', $user->personal_technician_id)->first()->name,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => Branch::where("id", $user->personal_branch_id)->first()->name,
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
        $user = auth()->user();

        if($request->input('full_name') ){
            $user->name = $request->input('full_name');
        }
        if($request->input('email')){
            $user->email = $request->input('email');
        }
        if($request->input('address')){
            $user->address = $request->input('address');
        }
        if ($request->has('gender')) {
            $user->sex = $request->input('gender');
        }
        if($request->input('birthday')){
            $user->dob = date("Y-m-d", strtotime($request->input('birthday')));
        }
        if($request->input('personal_branch_id')){
            $user->personal_branch_id = $request->input('personal_branch_id');
        }
        if($request->input('personal_technician_id')){
            $user->personal_technician_id = $request->input('personal_technician_id');
        }
        if($request->input('avatar')){
            $user->photo = $request->input('avatar');
        }
        $user->save();
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => Employee::where('id', $user->personal_technician_id)->first()->name,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => Branch::where("id", $user->personal_branch_id)->first()->name,
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
        $user = auth()->user();
        
        $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if (!Hash::check($request->input('old_password'), $user->password)) {
            $errorsMessage = ["old_password" => ["Mật khẩu cũ không chính xác"]];
            $response = $this->_formatBaseResponse(400, null, 'Thay đổi mật khẩu không thành công', ["errors" => $errorsMessage]);
            return response()->json($response, 400);
        } else {
            $errorsMessage = ["password" => ["Mật khẩu mới phải có ít nhất 6 ký tự"]];
            $newPassword = $request->input('password');
            if (strlen($newPassword) < 6) {
                $response = $this->_formatBaseResponse(400, null, 'Thay đổi mật khẩu không thành công', ["errors" => $errorsMessage]);
                return response()->json($response, 400);
            }else{
                $user->password = Hash::make($request->input('password'));
                $user->save();
                $response = $this->_formatBaseResponse(200, null, 'Thay đổi mật khẩu thành công', []);
                return response()->json($response);
            }
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = User::where('phone_number', 'LIKE', '%' . $query . '%')->get();
        return view('components.search_result', ['results' => $results]);
    }
    
}
