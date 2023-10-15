<?php

namespace App\Http\Controllers;

use App\Models\Facility\Zone;
use Illuminate\Http\Request;

class Facility_ZoneController extends Controller
{
    public function find(Request $request)
    {
        $branchId = $request->get('branch_id');
        $zone = Zone::where('branch_id', $branchId)->get();
        return $zone;
    }
    public function getById(Request $request)
    {
        $id = $request->get('q');
        $zone = Zone::find($id);
        return $zone;
    }
}
