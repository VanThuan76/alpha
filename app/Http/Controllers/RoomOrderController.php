<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Config;
use Carbon\Carbon;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\RoomOrder;
use App\Models\Service;

class RoomOrderController extends Controller
{

    public function checkRooms(Request $request)
    {
        $updatedRoomOrders = array();
        $roomOrders = RoomOrder::where('status', 1)->get();
        foreach($roomOrders as $i => $roomOrder){
            $startTime = Carbon::parse($roomOrder->start_time)->timezone(Config::get('app.timezone')); 
            $duration = Service::find($roomOrder->service_id)->duration;
            $startTime->addMinutes($duration);
            if ($startTime < Carbon::now()) {
                $roomOrder->status = 2;
                $roomOrder->end_time = Carbon::now();
                $roomOrder->save();
                array_push($updatedRoomOrders, $roomOrder);
            }
        }
        return json_encode($updatedRoomOrders);
    }

    public function getService(Request $request)
    {
        $roomOrder = RoomOrder::find($request->get('q'));
        return json_encode(Service::find($roomOrder->service_id));
    }
    
}
