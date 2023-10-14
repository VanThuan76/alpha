<?php

namespace App\Admin\Extensions\Nav;

use App\Models\Facility\Branch;
use Encore\Admin\Facades\Admin;

class Links
{
    public function __toString()
    {
    $name = Admin::user()->name;
    $activeBranch = Admin::user()->active_branch_id;
    $branchName = Branch::where('id', $activeBranch)->value('name');
    return 
            <<<HTML
                <li>
                    <p style="font-weight: bold; margin: 15px;">Xin chào: $name</p>
                </li>
                <li>
                    <p style="font-weight: bold; margin: 15px; color: white; background-color: #3c8dbc; border-color: #367fa9; border-radius: 4px; padding: 1px 10px;">Chi nhánh hoạt động: $branchName</p>
                </li>
            HTML;
    }
}