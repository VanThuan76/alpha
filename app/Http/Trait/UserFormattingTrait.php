<?php

namespace App\Traits;
use App\Admin\Controllers\Utils;

trait UserFormattingTrait
{
    private function _formatUser($user, Utils $commonController)
    {
        $user->sex = $commonController->commonCodeFormat('Gender', 'description_vi', $user->sex);
        return $user;
    }
}
