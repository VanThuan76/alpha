<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Config;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Service;
use App\Models\Bed;
use App\Models\AdminUser;
use App\Models\BedOrder;
use App\Admin\Controllers\Utils;
use Illuminate\Support\Facades\View;

class BedOrderController extends Controller
{

    public function showBed(Request $request)
    {
        $bed = Bed::find($request->post('bed_id'));
        $unitId = Utils::getUnitIdFromBed($request->post('bed_id'));
        
        $customers = User::whereIn('id', BedOrder::where('status', 0)->where('unit_id', $unitId)->pluck('user_id'))->get();
        $services = array();
        if (count($customers) > 0){
            $services = Service::whereIn('id', BedOrder::where('status', 0)->where('user_id', $customers->first()->id)->pluck('service_id'))->pluck('name', 'id');
        }
        $staffs = AdminUser::where('active_unit_id', $unitId)->pluck('name', 'id');
        return View::make('admin.bed_select_modal', compact('bed', 'customers', 'services', 'staffs'));
    }

    public function getServices(Request $request)
    {
        $bedOrders = BedOrder::where('status', 0)->where('user_id', $request->post('userId'))->get();
        return View::make('admin.service_select', compact('bedOrders'));
    }

    public function selectBed(Request $request)
    {
        
    }

    public function updateStatus(Request $request)
    {
        $bed = Bed::find($request->post('bed_id'));
        if ($bed->status == -1){
            $bed->status = 1;
        } else {
            $bed->status = -1;
        }
        $bed->save();
        return json_encode($bed);
    }
    
}
