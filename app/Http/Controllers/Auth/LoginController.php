<?php

namespace App\Http\Controllers\Auth;

use App\Admin\Controllers\UtilsCommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Member;
use App\Traits\MemberFormattingTrait;
use App\Traits\ResponseFormattingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ResponseFormattingTrait, MemberFormattingTrait;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request, UtilsCommonHelper $commonController)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $member = Member::find($user->member_id);

            if ($member) {
                $transformedMember = $this->_formatMember($member, $commonController);
                $transformedUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $user->remember_token,
                ];

                $response = $this->_formatBaseResponse(200, [
                    'member' => $transformedMember,
                    'user' => $transformedUser,
                ], 'Tạo tài khoản thành công');
            } else {
                $transformedUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $user->remember_token,
                ];
                $response = $this->_formatBaseResponse(200, [
                    'member' => 0,
                    'user' => $transformedUser,
                ], 'Thông tin thành viên không tồn tại');
            }
        } else {
            $response = $this->_formatBaseResponse(400, null, 'Đăng nhập không thành công');
        }

        return response()->json($response);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}
