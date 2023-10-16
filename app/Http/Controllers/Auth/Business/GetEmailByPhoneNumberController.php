<?php

namespace App\Http\Controllers\Auth\Business;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class GetEmailByPhoneNumberController extends Controller
{
    use CommonResponse;

    private function maskEmail($email)
    {
        list($local, $domain) = explode('@', $email);

        $maskedLocal = substr($local, 0, 2) . str_repeat('*', strlen($local) - 2) . substr($local, -2);
        $maskedDomain = substr($domain, 0, 1) . str_repeat('*', strlen($domain) - 2) . substr($domain, -1);

        return $maskedLocal . '@' . $maskedDomain;
    }
    public function getEmail(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:255',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();
        if ($user) {
            $maskedEmail = $this->maskEmail($user->email);
            return $this->_formatBaseResponse(200, null, 'Đã lấy email thành công', ['mask_email' => $maskedEmail]);
        } else {
            return $this->_formatBaseResponse(404, null, 'Không tìm thấy người dùng với số điện thoại được cung cấp');
        }
    }
}
