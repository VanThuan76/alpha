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
    public function getAll(Request $request)
    {
        $query = User::query()->orderBy("created_at", "asc");
        $filters = $request->input('filters', []);
        foreach ($filters as $filter) {
            $field = $filter['field'];
            $value = $filter['value'];

            if (!empty($value)) {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }
        $sorts = $request->input('sorts', []);
        foreach ($sorts as $sort) {
            $field = $sort['field'];
            $direction = $sort['direction'];
            if (!empty($field) && !empty($direction)) {
                $query->orderBy($field, $direction);
            }
        }
        $size = $request->input('size', 10);
        $users = $query->paginate($size, ['*'], 'page', $request->input('page', 1));

        $formattedUsers = $users->map(function ($user) {
            $employee = Employee::where('id', $user->personal_technician_id)->first();
            $branch = Branch::where("id", $user->personal_branch_id)->first();
            return [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => $employee ? $employee->name : null,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => $branch ? $branch->name : null,
                    'address' => $branch ? $branch->address : null,
                ],
                'full_name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'customer_type' => $user->customer_type,
                'points' => $user->point,
                'bonus_coins' => $user->bonus_coins,
                'avatar_path' => $user->photo != null ? 'https://erp.senbachdiep.com/storage/' . $user->photo : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });
        $totalPages = $users->lastPage();
        return response()->json(
            $this->_formatCountResponse(
                $formattedUsers,
                $users->perPage(),
                $totalPages
            )
        );
    }

    public function get()
    {
        $user = auth()->user();
        $employee = Employee::where('id', $user->personal_technician_id)->first();
        $branch = Branch::where("id", $user->personal_branch_id)->first();
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => $employee ? $employee->name : null,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => $branch ? $branch->name : null,
                    'address' => $branch ? $branch->address : null,
                ],
                'full_name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'customer_type' => $user->customer_type,
                'points' => $user->point,
                'bonus_coins' => $user->bonus_coins,
                'avatar_path' => $user->photo != null ? 'https://erp.senbachdiep.com/storage/' . $user->photo : null,
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
        $employee = Employee::where('id', $user->personal_technician_id)->first();
        $branch = Branch::where("id", $user->personal_branch_id)->first();

        if ($request->input('full_name')) {
            $user->name = $request->input('full_name');
        }
        if ($request->input('email')) {
            $user->email = $request->input('email');
        }
        if ($request->input('address')) {
            $user->address = $request->input('address');
        }
        if ($request->has('gender')) {
            $user->sex = $request->input('gender');
        }
        if ($request->input('birthday')) {
            $user->dob = date("Y-m-d", strtotime($request->input('birthday')));
        }
        if ($request->input('personal_branch_id')) {
            $user->personal_branch_id = $request->input('personal_branch_id');
        }
        if ($request->input('personal_technician_id')) {
            $user->personal_technician_id = $request->input('personal_technician_id');
        }
        if ($request->hasFile('avatar')) {
            $imagePath = $request->file('avatar')->store('avatars', 'public');
            $user->photo = $imagePath;
        }
        $user->save();
        $result = [
            'user' => [
                'id' => $user->id,
                'birthday' => $user->dob,
                'gender' => $user->sex,
                'personal_technician' => [
                    'id' => $user->personal_technician_id,
                    'name' => $employee ? $employee->name : null,
                ],
                'personal_branch' => [
                    'id' => $user->personal_branch_id,
                    'name' => $branch ? $branch->name : null,
                    'address' => $branch ? $branch->address : null,
                ],
                'full_name' => $user->name,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'customer_type' => $user->customer_type,
                'points' => $user->point,
                'bonus_coins' => $user->bonus_coins,
                'avatar_path' => $user->photo != null ? 'https://erp.senbachdiep.com/storage/' . $user->photo : null,
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
    public function delete(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->access_token = null;
            $user->is_deleted = 1;
            $user->save();

            $response = $this->_formatBaseResponse(200, null, 'Xoá tài khoản thành công', []);
            return response()->json($response);
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
            } else {
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
    public function getUserErp()
    {
        $user = auth()->user();
        $result = [
            "username" => $user->username,
            "name" => $user->name,
            "phone" => $user->phone_number,
            "avatar" => $user->avatar,
            "active_branch_id" => $user->active_branch_id,
            "status" => $user->status,
        ];
        if ($user) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
    public function createCustomerErp(Request $request)
    {
        $auth = auth()->user();
        $user = new User();
        $user->name = $request->input('full_name');
        $user->phone_number = $request->input('phone_number');
        $user->email = $request->input('email');
        $user->customer_type = $request->input('customer_type');

        if ($request->input('national')) {
            $user->address = $request->input('national');
        }
        if ($request->input('province_city')) {
            $user->address = $request->input('province_city');
        }
        if ($request->input('district')) {
            $user->address = $request->input('district');
        }
        if ($request->input('address')) {
            $user->address = $request->input('address');
        }
        if ($request->input('wards')) {
            $user->address = $request->input('wards');
        }
        if ($request->input('birthday')) {
            $user->dob = date("Y-m-d", strtotime($request->input('birthday')));
        }
        if ($request->has('gender')) {
            $user->sex = $request->input('gender');
        }
        if ($request->has('note')) {
            $user->sex = $request->input('note');
        }
        if ($request->input('branch_id')) {
            $user->personal_branch_id = $request->input('branch_id');
        }
        if ($request->input('person_in_charge')) {
            $user->personal_technician_id = $request->input('person_in_charge');
        }
        //Todo: Chua biet
        if ($request->input('campaign_id')) {
            $user->campaign_id = $request->input('campaign_id');
        }
        if ($request->input('customer_resources')) {
            $user->source_id = $request->input('customer_resources');
        }
        if ($request->input('presenter_id')) {
            $user->presenter_id = $request->input('presenter_id');
        }
        if ($request->input('caring_service_id')) {
            $user->caring_service_id = $request->input('caring_service_id');
        }
        $user->save();
        if ($auth) {
            if ($user) {
                $response = $this->_formatBaseResponse(200, null, 'Tạo khách hàng thành công', []);
                return response()->json($response);
            } else {
                $response = $this->_formatBaseResponse(401, null, 'Tạo khách hàng không thành công', ['errors' => 'Unauthorised']);
                return response()->json($response, 401);
            }
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Tạo khách hàng không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
