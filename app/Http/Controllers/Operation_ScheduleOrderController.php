<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Http\Response\ScheduleResponse;
use App\Models\CommonCode;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Carbon\Carbon;
use App\Models\Operation\ScheduleOrder;
use Illuminate\Http\Request;

class Operation_ScheduleOrderController extends Controller
{
    use CommonResponse, ScheduleResponse;
    public function getSchedule(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 20);
        $previousLastTechnicianId = $request->input('previous_last_schedule_id', 0);

        if ($request->input('limit')) {
            $limit = 20;
        }
        $scheduleOrderQuery = ScheduleOrder::where('user_id', $user->id)->orderBy('id', 'desc')->limit($limit);
        if ($request->input('previous_last_schedule_id') !== null) {
            $scheduleOrderQuery->where('id', '<', $previousLastTechnicianId);
        }
        $schedules = $scheduleOrderQuery->get();

        $result = [
            'schedules' => $schedules->map(function ($schedule) {
                $services = Service::where("id", $schedule->service_id)->get();
                $workShifts = WorkShift::where("id", $schedule->work_shift_id)->get();
                $servicesArrayMap = $this->_formatServiceResponse($services, $workShifts, $schedule->employee_id);
                $status = CommonCode::where("type", "Schedule")->where("value", $schedule->status)->first()->description_vi;
                return [
                    'id' => "SBD" . $schedule->id . User::where("id", $schedule->user_id)->first()->name,
                    'time' => Carbon::parse($schedule->date),
                    'branch' => $this->_formatBranchResponse($schedule->branch_id),
                    'services' => $servicesArrayMap,
                    'status' => $status
                ];
            })
        ];
        if ($scheduleOrderQuery) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
