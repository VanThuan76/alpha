<?php

namespace App\Admin\Controllers;

abstract class Constant
{
    const STATUS = array(0 => "Không hoạt động", 1 => "Đang hoạt động");
    const SEX = array(0 => "Nữ", 1 => "Nam", 2 => "Không biết");
    const PAYMENT_METHOD = array(1 => "Chuyển khoản", 2 => "Tiền mặt", 3 => "Visa/Master");
    const SHIFT = array(0 => "Sáng", 1 => "Chiều");
}
