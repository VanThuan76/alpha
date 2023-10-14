<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\Facility\Bed;
use App\Models\Operation\BedOrder;
use App\Models\Product\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Admin\Controllers\Utils;
use App\Models\Sales\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

class Operation_BedOrderController extends Controller
{

    public function showBed(Request $request)
    {
        $bed = Bed::find($request->post('bed_id'));
        $branchId = Utils::getBranchIdFromBed($request->post('bed_id'));
        
        $customers = User::whereIn('id', BedOrder::where('status', 0)->where('branch_id', $branchId)->pluck('branch_id'))->get();
        $services = array();
        $orders = array();
        if (count($customers) > 0){
            $orders = BedOrder::where('status', 0)->where('user_id', $customers->first()->id)->get();
        }
        $staffs = AdminUser::where('active_branch_id', $branchId)->pluck('name', 'id');
        return View::make('admin.bed_select_modal', compact('bed', 'customers', 'orders', 'staffs'));
    }

    public function getServices(Request $request)
    {
        $bedOrders = BedOrder::where('status', 0)->where('user_id', $request->post('userId'))->get();
        return View::make('admin.service_select', compact('bedOrders'));
    }

    public function selectBed(Request $request)
    {
        $order = BedOrder::find($request->post('order-id'));
        $order->technician_id1 = $request->post('staff_1');
        $order->technician_id2 = $request->post('staff_3');
        $order->technician_id3 = $request->post('staff_4');
        $order->bed_id = $request->post('bed-id');
        $order->status = 1;
        $order->start_time = Carbon::now();
        $order->save();
        return json_encode($order);
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

    public function finishOrder(Request $request)
    {
        $order = BedOrder::find($request->post('order-id'));
        $order->status = 2;
        $order->save();
        return json_encode($order);
    }

    public function checkBeds(Request $request)
    {
        $updatedBedOrders = array();
        $bedOrders = BedOrder::where('status', 1)->get();
        foreach($bedOrders as $i => $bedOrder){
            $startTime = Carbon::parse($bedOrder->start_time)->timezone(Config::get('app.timezone')); 
            $duration = Service::find($bedOrder->service_id)->duration;
            $startTime->addMinutes($duration);
            if ($startTime < Carbon::now()) {
                $bedOrder->status = 2;
                $bedOrder->end_time = Carbon::now();
                $bedOrder->save();
                array_push($updatedBedOrders, $bedOrder);
            }
        }
        return json_encode($updatedBedOrders);
    }
}
