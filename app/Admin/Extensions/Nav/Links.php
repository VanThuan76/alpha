<?php

namespace App\Admin\Extensions\Nav;

use Encore\Admin\Facades\Admin;

class Links
{
    public function __toString()
    {
    $name = Admin::user()->name;
        return 
            <<<HTML
                <li>
                    <p style="font-weight: bold; margin: 15px;">Xin chào: $name</p>
                </li>
            HTML;
    }
}