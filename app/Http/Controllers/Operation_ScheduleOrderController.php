<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\Utils;
use App\Http\Response\CommonResponse;
use App\Http\Response\ScheduleResponse;
use App\Models\CommonCode;
use App\Models\Operation\WorkShift;
use App\Models\Operation\WorkShiftService;
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
    public function createSchedule(Request $request)
    {
        $user = auth()->user();
        $services = $request->input('services');
        if ($services) {
            $workShiftServiceIDs = [];
            foreach ($services as $service) {
                $schedule = new ScheduleOrder;
                $workShiftService = new WorkShiftService;
                $schedule->branch_id = $request->input('branch_id');
                $schedule->user_id = $user->id;
                $schedule->code = Utils::generateCommonCode("schedule_order", "BK");
                $schedule->date = $request->input('date');
                $schedule->book_at = $request->input('time');
                $schedule->note = $request->input('note');
                $workShiftService->service_id = $service['id'];

                $employees = $service['technicians'];
                if ($employees) {
                    foreach ($employees as $employee) {
                        $workShiftFilter = WorkShift::where("date", "=", $request->input('date'))
                            ->where('status', 0)
                            ->where("employee_id", $employee['id'])
                            ->first();
                        if ($workShiftFilter) {
                            $workShiftService->work_shift_id = $workShiftFilter->id;
                            $workShift = WorkShift::find($workShiftFilter->id);
                            $fromAtWorkShift = strtotime($workShift->from_at);
                            $toAtWorkShift = strtotime($workShift->to_at);
                            $currentTimeExits = date('H:i:s', $fromAtWorkShift + ($service['duration'] * 60));
                            if ($toAtWorkShift < $currentTimeExits) {
                                $workShift->status = 1;
                            } else {
                                $workShift->from_at = $currentTimeExits;
                            }
                            $workShift->save();
                            $workShiftService->save();
                            $workShiftServiceIDs[] = $workShiftService->id;
                        }
                    }
                }
            }
            $schedule->work_shift_services = implode(',', $workShiftServiceIDs);
            $schedule->save();
        }
        if ($user) {
            $response = $this->_formatBaseResponse(200, null, 'Đặt lịch thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Đặt lịch không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }

    }
    public function cancelSchedule($id)
    {
        $user = auth()->user();
        $scheduleOrder = ScheduleOrder::where('user_id', $user->id);
        if ($scheduleOrder) {
            $schedule = new ScheduleOrder;
            $schedule->status = 4; //Huy
            $schedule->save();
        }
        if ($user) {
            $response = $this->_formatBaseResponse(200, null, 'Huỷ lịch thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Huỷ lịch không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
    public function updateSchedule(Request $request, $id)
    {
        $user = auth()->user();
        $services = $request->input('services');
        if ($services) {
            $workShiftServiceIDs = [];
            foreach ($services as $service) {
                $schedule = ScheduleOrder::find($id);
                if (!$schedule) {
                    return response()->json(['message' => 'Không tìm thấy lịch trình cần cập nhật'], 404);
                }
                $workShiftService = new WorkShiftService;
                $workShiftService->service_id = $service['id'];

                $employees = $service['technicians'];
                if ($employees) {
                    foreach ($employees as $employee) {
                        $workShiftFilter = WorkShift::where("date", "=", $request->input('date'))
                            ->where('status', 0)
                            ->where("employee_id", $employee['id'])
                            ->first();
                        if ($workShiftFilter) {
                            $workShiftService->work_shift_id = $workShiftFilter->id;
                            $workShift = WorkShift::find($workShiftFilter->id);
                            $fromAtWorkShift = strtotime($workShift->from_at);
                            $toAtWorkShift = strtotime($workShift->to_at);
                            $currentTimeExits = date('H:i:s', $fromAtWorkShift + ($service['duration'] * 60));
                            if ($toAtWorkShift < $currentTimeExits) {
                                $workShift->status = 1;
                            } else {
                                $workShift->from_at = $currentTimeExits;
                            }
                            $workShift->save();
                            $workShiftService->save();
                            $workShiftServiceIDs[] = $workShiftService->id;
                        }
                    }
                }
            }
            $schedule->work_shift_services = implode(',', $workShiftServiceIDs);
            $schedule->save();
        }
        if ($user) {
            $response = $this->_formatBaseResponse(200, null, 'Cập nhật lịch thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Cập nhật lịch không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }

}
