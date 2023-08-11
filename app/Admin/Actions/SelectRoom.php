<?php

namespace App\Admin\Actions;

use App\Models\Room;
use App\Models\Branch;
use App\Models\Zone;
use App\Models\Service;
use Carbon\Carbon;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\Bill;
use App\Models\WorkSchedule;
use App\Models\RoomOrder;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Encore\Admin\Actions\RowAction;
use App\Admin\Controllers\Constant;

class SelectRoom extends RowAction
{
    public $name = 'Chọn phòng';

    public function handle(Room $room, Request $request)
    {
        // Switch the value of the `star` field and save
        $roomOrder = $this->checkOrder($room);
        if (is_null($roomOrder)){
            $roomOrder = RoomOrder::find($request->get('id'));
            $roomOrder->room_id = $room->id;
            $roomOrder->status = 1;
            $roomOrder->technician_id = $request->get('technician_id');
            $roomOrder->start_time = Carbon::now();
            $roomOrder->save();
        } else {
            $roomOrder->status = 2;
            $roomOrder->end_time = Carbon::now();
            $roomOrder->save();
        }
        // return a new html to the front end after saving
        return $this->response()->success('Cập nhật thành công!')->refresh();
    }

    private function getTechnicians($branchId){
        $hour = Carbon::now()->format('H');
        $workSchedule = WorkSchedule::where('branch_id',$branchId)->where('date', Carbon::now()->format('Y-m-d 00:00:00'))->first();
        $shift = "";
        if ($hour <= 12){
            $shift = $workSchedule->shift1;
        } else if ($hour < 16){
            $shift = $workSchedule->shift2;
        } else if ($hour < 20){
            $shift = $workSchedule->shift3;
        } else {
            $shift = $workSchedule->shift4;
        }
        $workingTechnicians = RoomOrder::where('unit_id', Admin::user()->active_unit_id)->where('status', 1)->pluck('id', 'technician_id');
        $avaiTechnicians = array();
        foreach($shift as $i => $technicianId){
            if (!isset($workingTechnicians[$technicianId]) ){
                $avaiTechnicians[$technicianId] = AdminUser::find($technicianId)->name;
            }
        }
        
        return $avaiTechnicians;
    }

    public function form()
    {
        $room = Room::find($this->row->id);
        $zone = Zone::find($room->zone_id);
        $order = $this->checkOrder($room);
        
        if (is_null($order)){
            $url = env('APP_URL') . '/api/customer/services';    
            $this->name = "Chọn phòng";
            $services = array();
            $roomOrders = RoomOrder::where('unit_id', Admin::user()->active_unit_id)->where('status', 0)->get();
            foreach( $roomOrders as $i => $roomOrder) {
                $services[$roomOrder->id] = $roomOrder->user->name . " : " . $roomOrder->service->name;
            }
            $room = Room::find($this->row->id);
            $zone = Zone::find($room->zone_id);
            $this->text('room_id', 'Phòng')->default($room->name)->readOnly();
            $this->select('id', 'Khách hàng - Dịch vụ')->options($services)->required();
            $this->select('technician_id', 'Kỹ thuật viên')->options($this->getTechnicians($zone->branch_id))->required();
            $this->text('start_time', 'Bắt đầu')->default(Carbon::now())->readOnly();
        } else { 
            $url = env('APP_URL') . '/api/customer/services';
            $this->name = "Kết thúc";
            $services = array();
            $roomOrders = RoomOrder::where('unit_id', Admin::user()->active_unit_id)->where('status', 0)->get();
            foreach( $roomOrders as $i => $roomOrder) {
                $services[$roomOrder->id] = $roomOrder->user->name . " : " . $roomOrder->service->name;
            }
            $room = Room::find($this->row->id);
            $roomOrder = $this->checkOrder($room);
            $zone = Zone::find($room->zone_id);
            $user = User::find($roomOrder->user_id);
            $service = Service::find($roomOrder->service_id);
            $this->text('room_id', 'Phòng')->default($room->name)->readOnly();
            $this->text('user_id', 'Khách hàng')->default($user->name)->readOnly();
            $this->text('service_id', 'Dịch vụ')->default($service->name)->readOnly();
            $this->text('tech_id', 'Kỹ thuật viên')->default(AdminUser::find($roomOrder->technician_id)->name)->readOnly();
            $this->text('start_time', 'Bắt đầu')->default($roomOrder->start_time)->readOnly();
        }

    }
    
    private function checkOrder($room){
        foreach($room->orders as $i => $order){
            if ($order->status == 1) {
                return $order;
            }
        }
    }

    // This method displays different icons in this column based on the value of the `star` field.
    public function display($id)
    {
        $html = '';
        $room = Room::find($id);
        $order = $this->checkOrder($room);
        if ($order){
            if (!is_null($order->technician_id)){
                $html .= "Khách hàng: ". User::find($order->user_id)->name . "<br/>Kỹ thuật viên: " . AdminUser::find($order->technician_id)->name . "<br/>";
                if ($order->status == 1){
                    $html .= "Bắt đầu: .$order->start_time </br>";
                    $html .= "Đang thực hiện. </br>";
                }
            }
        }
        return $html == '' ? '<span class="label label-success">Phòng trống</span>' : $html;
    }
}