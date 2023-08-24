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
        $roomOrder = $this->checkOrder($room);
        if (is_null($roomOrder)){
            $roomOrder = RoomOrder::find($request->get('id'));
            $roomOrder->room_id = $room->id;
            $roomOrder->status = 1;
            $roomOrder->technician_id = $request->get('technician_id');
            if (Service::find($roomOrder->service_id)->staff_number == 2){
                $roomOrder->technician_id1 = $request->get('technician_id1');
            }
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
                $services[$roomOrder->id] = $roomOrder->user->name . " : " . $roomOrder->service->name . " Vé thứ: " . $roomOrder->order;
            }
            $room = Room::find($this->row->id);
            $zone = Zone::find($room->zone_id);
            $this->text('room_id', 'Phòng')->default($room->name)->readOnly();
            $this->select('id', 'Khách hàng - Dịch vụ')->options($services)->required();
            $this->select('technician_id', 'Kỹ thuật viên')->options($this->getTechnicians($zone->branch_id))->required();
            $this->select('technician_id1', 'Kỹ thuật viên 2')->options($this->getTechnicians($zone->branch_id))->disable();
            $this->text('start_time', 'Bắt đầu')->default(Carbon::now())->readOnly();
            $this->text('duration', 'Thời gian')->readOnly();
        } else { 
            $url = env('APP_URL') . '/api/customer/services';
            $this->name = "Kết thúc";
            $services = array();
            $roomOrders = RoomOrder::where('unit_id', Admin::user()->active_unit_id)->where('status', 0)->get();
            foreach( $roomOrders as $i => $roomOrder) {
                $services[$roomOrder->id] = $roomOrder->user->name . " : " . $roomOrder->service->name . " Vé thứ: " . $roomOrder->order;
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
            if (!is_null($roomOrder->technician_id1)){
                $this->text('tech_id1', 'Kỹ thuật viên 2')->default(AdminUser::find($roomOrder->technician_id1)->name)->readOnly();
            }
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
            $service = Service::find($order->service_id);
            if (!is_null($order->technician_id)){
                $html .= "Khách hàng: ". User::find($order->user_id)->name . "<br/>Kỹ thuật viên: " . AdminUser::find($order->technician_id)->name . "<br/>";
                if (!is_null($order->technician_id1) ){
                    $html .= "Kỹ thuật viên 2: " . AdminUser::find($order->technician_id1)->name . "<br/>";
                }
                if ($order->status == 1){
                    $startTime = new Carbon($order->start_time);
                    $usedTime = Carbon::now()->diffInMinutes($startTime);
                    $html .= "Bắt đầu: .$order->start_time <br/>";
                    $html .= "Trạng thái: Đang thực hiện. <br/>";
                    $html .= "Dịch vụ: $service->name. <br/>";
                    $html .= "Thời gian: $service->duration phút. <br/>";
                    $html .= "<div id='room-$id' class='room-countdown'> <input type='hidden' class='start-time' value='$order->start_time'>".
                    "<input type='hidden' class='duration' value='$service->duration'><span class='countdown'>Thời gian còn lại: ". ($service->duration - $usedTime) ." phút</span>".
                    "<br/><span class='label label-danger finish-room'>Kết thúc</span></div>";
                }
            }
        }
        $url = env('APP_URL') . '/api/roomOrder/getService';
        $script = <<<EOT
        $(document).on('change', ".form-group select", function () {
            $.getJSON("$url",{q : this.value}, function (data) {
                $(".form-group #duration").val(data.duration);
                if(data.staff_number == 1){
                    $(".form-group .technician_id1").prop( "disabled", true );
                    $(".form-group .technician_id1").removeAttr('required');
                } else {
                    $(".form-group .technician_id1").prop( "disabled", false );
                    $(".form-group .technician_id1").attr('required', 'required');
                }
            });
        });
        setInterval(function () {
            $('.room-countdown').each(function(){
                var start_time = $(this).find('.start-time').val();
                var duration = parseInt($(this).find('.duration').val());
                var used_time = Math.abs(new Date() - new Date(start_time.replace(/-/g,'/'))) / 60000;
                $(this).find('.countdown').html('Thời gian còn lại: ' + parseFloat(duration - used_time > 0 ? duration - used_time : 0).toFixed(2) + ' phút');
            });
        }, 10000);
        EOT;
        Admin::script($script);
        return $html == '' ? '<span class="label label-success">Chọn phòng</span>' : $html;
    }
}