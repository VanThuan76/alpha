<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sales\User;
use App\Traits\CommonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use CommonResponse;

    public function resetPassword(Request $request)
    {
        $userId = $request->input('user_id');
        $loggedInUser = User::find($userId);
        if (!$loggedInUser) {
            return response()->json(['error' => 'Không tìm thấy người dùng.'], 404);
        }
        if (!Hash::check($request->old_password, $loggedInUser->password)) {
            return response()->json(['error' => 'Mật khẩu cũ không đúng.'], 400);
        }
        if ($request->new_password !== $request->password_confirmation) {
            return response()->json(['error' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.'], 400);
        }

        $loggedInUser->password = Hash::make($request->new_password);
        $loggedInUser->save();
        $response = $this->_formatBaseResponse(200, [], 'Mật khẩu đã được đặt lại thành công');
        return response()->json($response);
    }
}
