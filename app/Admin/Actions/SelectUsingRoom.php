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

class SelectUsingRoom extends RowAction
{
    public $name = 'Bán gối';

    public function handle(Room $room, Request $request)
    {
        $orders = $this->checkOrder($room);
        if (count($orders) == 1){
            $roomOrder = RoomOrder::find($request->get('id'));
            $roomOrder->room_id = $room->id;
            $roomOrder->status = 1;
            $roomOrder->technician_id = $request->get('technician_id');
            if (Service::find($roomOrder->service_id)->staff_number == 2){
                $roomOrder->technician_id1 = $request->get('technician_id1');
            }
            $roomOrder->start_time = Carbon::now();
            $roomOrder->save();
            // return a new html to the front end after saving
            return $this->response()->success('Cập nhật thành công!')->refresh();
        } else {
            return $this->response();
        }
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
        $orders = $this->checkOrder($room);
        
        if (count($orders) == 1){
            $order = end($orders);
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
            $this->select('technician_id1', 'Kỹ thuật viên 2')->options($this->getTechnicians($zone->branch_id))->readOnly();
            $this->text('start_time', 'Bắt đầu')->default(Carbon::now())->readOnly();
            $this->text('duration', 'Thời gian')->readOnly();
        } else if (count($orders) == 2) { 
            $url = env('APP_URL') . '/api/customer/services';
            $this->name = "Kết thúc";
            $services = array();
            $roomOrder =  end($orders);
            $room = Room::find($roomOrder->room_id);
            $zone = Zone::find($room->zone_id);
            $user = User::find($roomOrder->user_id);
            $service = Service::find($roomOrder->service_id);
            $this->text('room_id', 'Phòng')->default($room->name)->readOnly();
            $this->text('user_id', 'Khách hàng')->default($user->name)->readOnly();
            $this->text('service_id', 'Dịch vụ')->default($service->name)->readOnly();
            $this->text('tech_id', 'Kỹ thuật viên')->default(AdminUser::find($roomOrder->technician_id)->name)->readOnly();
            if (!is_null($roomOrder->technician_id1)){
                $this->text('tech_id1', 'Kỹ thuật viên 2')->default(AdminUser::find($roomOrder->technician_id1)->name)->readOnly();
            }
            $this->text('start_time', 'Bắt đầu')->default($roomOrder->start_time)->readOnly();
        } else {

        }

    }
    
    private function checkOrder($room){
        $orders = array();
        foreach($room->orders as $i => $order){
            if ($order->status == 1) {
                array_push($orders, $order); 
            }
        }
        return $orders;
    }

    // This method displays different icons in this column based on the value of the `star` field.
    public function display($field)
    {
        $html = '';
        $id = $this->row->id;
        $room = Room::find($id);
        $orders = $this->checkOrder($room);
        if (count($orders) == 2){
            $order = end($orders);
            $service = Service::find($order->service_id);
            if (!is_null($order->technician_id)){
                $html .= "Khách hàng: ". User::find($order->user_id)->name . "<br/>Kỹ thuật viên: " . AdminUser::find($order->technician_id)->name . "<br/>";
                if (!is_null($order->technician_id1) ){
                    $html .= "Kỹ thuật viên 2: " . AdminUser::find($order->technician_id1)->name . "<br/>";
                }
                $startTime = new Carbon($order->start_time);
                $usedTime = Carbon::now()->diffInMinutes($startTime);
                $html .= "Bắt đầu: .$order->start_time <br/>";
                $html .= "Trạng thái: Đang thực hiện. <br/>";
                $html .= "Dịch vụ: $service->name. <br/>";
                $html .= "Thời gian: $service->duration phút. <br/>";
            }
        } else if (count($orders) == 1){
            $order = end($orders);
            $service = Service::find($order->service_id);
            $startTime = new Carbon($order->start_time);
            $usedTime = Carbon::now()->diffInMinutes($startTime);
            $html .= "<div id='room-$id' class='room-countdown'> <input type='hidden' class='start-time' value='$order->start_time'>".
            "<input type='hidden' class='duration' value='$service->duration'><span class='countdown' style='display:none'>Thời gian còn lại: ". ($service->duration - $usedTime) ." phút</span>".
            "<br/><span class='label label-warning assign-room' style='". ($service->duration - $usedTime > 30 ? "display:none" : ""). "'>Bán gối</span></div>";
        } else {

        }
        $url = env('APP_URL') . '/api/roomOrder/getService';
        $script = <<<EOT
        $(document).on('change', ".form-group select", function () {
            $.getJSON("$url",{q : this.value}, function (data) {
                $(".form-group #duration").val(data.duration);
                if(data.staff_number == 1){
                    $(".form-group .technician_id1").attr('readonly','readonly');
                } else {
                    $(".form-group .technician_id1").removeAttr('readonly');
                }
            });
        });
        setInterval(function () {
            $('.room-countdown').each(function(){
                var start_time = $(this).find('.start-time').val();
                var duration = parseInt($(this).find('.duration').val());
                var used_time = Math.abs(new Date() - new Date(start_time.replace(/-/g,'/'))) / 60000;
                var left_time = duration - used_time > 0 ? duration - used_time : 0;
                $(this).find('.countdown').html('Thời gian còn lại: ' + parseFloat(left_time).toFixed(2) + ' phút');
                if (left_time < 30) {
                    $(this).find('.assign-room').show();
                }
            });
        }, 10000);
        EOT;
        Admin::script($script);
        return $html;
    }
}