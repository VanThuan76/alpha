<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{

    public function find(Request $request)
    {
        $id = $request->get('q');
        $user = User::find($id);
        $customerType = $user->customerType;
        return [$user, $customerType];
    }
    
}
