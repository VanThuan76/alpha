<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\Utils;
use App\Http\Response\CommonResponse;
use App\Http\Response\ScheduleResponse;
use App\Models\CommonCode;
use App\Models\Operation\WorkShift;
use App\Models\Operation\WorkShiftService;
use App\Models\Product\Service;
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
        $status = $request->input('status');

        if ($request->input('limit')) {
            $limit = 20;
        }
        $scheduleOrderQuery = ScheduleOrder::where('user_id', $user->id)->orderBy('id', 'desc')->limit($limit);
        if ($request->input('previous_last_schedule_id') !== null) {
            $scheduleOrderQuery->where('id', '<', $previousLastTechnicianId);
        }
        if ($request->input('status') !== null) {
            $scheduleOrderQuery->where('status', $status);
        }
        $schedules = $scheduleOrderQuery->get();

        $result = [
            'schedules' => $schedules->map(function ($schedule) {
                $workShiftServiceIds = explode(',', $schedule->work_shift_services);
                $workShiftServices = WorkShiftService::whereIn('id', $workShiftServiceIds)->get();
                $services = [];
                $workShifts = [];

                foreach ($workShiftServices as $workShiftService) {
                    $services[] = Service::where("id", $workShiftService->service_id)->first();
                    $workShifts[] = WorkShift::where("id", $workShiftService->work_shift_id)->first();
                }
                $servicesCollection = collect($services);
                $workShiftsCollection = collect($workShifts);
                $servicesArrayMap = $this->_formatServiceResponse($servicesCollection, $workShiftsCollection, $schedule);

                $status = CommonCode::where("type", "Schedule")->where("value", $schedule->status)->first()->value;
                return [
                    'id' => $schedule->id,
                    'time' => Carbon::parse($schedule->date)->format('d/m/Y'),
                    'branch' => $this->_formatBranchResponse($schedule->branch_id),
                    'schedule_services' => $servicesArrayMap,
                    'note' => [
                        'force' => $schedule->note_force,
                        'pain_attention' => $schedule->pain_attention,
                        'pathology_attention' => $schedule->pathology_attention,
                        'another' => $schedule->note
                    ],
                    'status' => $status,
                    'order_id' => null //Chỉ xuất hiện khi lịch hẹn là loại Hoàn thành
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
        //Validate
        $dateFromRequest = $request->input('date');
        $isValidDate = Carbon::createFromFormat('d/m/Y', $dateFromRequest)->isValid();
        if (!$isValidDate) {
            $response = $this->_formatBaseResponse(422, null, 'Sai định dạng date', []);
            return response()->json($response);
        }

        //Handle Logic
        $scheduleServices = $request->input('schedule_services');
        if ($scheduleServices) {
            $workShiftServiceIDs = [];
            foreach ($scheduleServices as $scheduleService) {
                $schedule = new ScheduleOrder;
                //Schedule
                $schedule->branch_id = $request->input('branch')['id'];
                $schedule->user_id = $user->id;
                $schedule->code = Utils::generateCommonCode("schedule_order", "BK");
                $schedule->date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d H:i:s');
                $schedule->book_at = $scheduleService['time'];
                $schedule->note_force = $request->input('note')['force'];
                $schedule->note_pain_attention = $request->input('note')['pain_attention'];
                $schedule->note_pathology_attention = $request->input('note')['pathology_attention'];
                $schedule->note = $request->input('note')['another'];
                //WorkShiftService 
                $workShiftService = new WorkShiftService;
                $workShiftService->service_id = $scheduleService['service']['id'];

                $employees = $scheduleService['selected_technicians'];
                if ($employees) {
                    foreach ($employees as $employee) {
                        $workShiftFilter = WorkShift::where('date', Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'))
                            ->where('status', 0)
                            ->whereRaw("TIME(from_at) <= ?", [$scheduleService['time']])
                            ->where('employee_id', $employee['id'])
                            ->first();
                        if ($workShiftFilter) {
                            $workShiftService->work_shift_id = $workShiftFilter->id;
                            //WorkShift
                            $workShift = WorkShift::find($workShiftFilter->id);
                            $fromAtWorkShift = strtotime($workShift->from_at);
                            $toAtWorkShift = strtotime($workShift->to_at);
                            $currentTimeExits = date('H:i:s', $fromAtWorkShift + (Service::where('id', $scheduleService['service']['id'])->first()->duration * 60));
                            if ($toAtWorkShift < $currentTimeExits) {
                                $workShift->status = 1;
                            } else {
                                $workShift->from_at = $currentTimeExits;
                            }
                            $workShift->save();
                            $workShiftService->save();
                            $workShiftServiceIDs[] = $workShiftService->id;
                        } else {
                            $response = $this->_formatBaseResponse(422, null, 'Nhân viên đang bận hoặc hết ca làm', []);
                            return response()->json($response);
                        }
                    }
                } else {
                    $response = $this->_formatBaseResponse(400, null, 'Không tìm thấy nhân viên', []);
                    return response()->json($response);
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
    public function updateSchedule(Request $request, $id)
    {
        $user = auth()->user();
        //Validate
        $dateFromRequest = $request->input('date');
        $isValidDate = Carbon::createFromFormat('d/m/Y', $dateFromRequest)->isValid();
        if (!$isValidDate) {
            $response = $this->_formatBaseResponse(422, null, 'Sai định dạng date', []);
            return response()->json($response);
        }


        //Handle Logic
        $scheduleOrder = ScheduleOrder::findOrFail($id);
        $scheduleServices = $request->input('schedule_services');

        if ($scheduleServices) {
            $workShiftServiceIDs = [];

            foreach ($scheduleServices as $scheduleService) {
                //WorkShiftService
                $workShiftService = new WorkShiftService;
                $workShiftService->service_id = $scheduleService['service']['id'];
                //Schedule
                $scheduleOrder->branch_id = $request->input('branch_id');
                $scheduleOrder->book_at = $scheduleService['time'];
                $scheduleOrder->date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d H:i:s');
                $scheduleOrder->note_force = $request->input('note')['force'];
                $scheduleOrder->note_pain_attention = $request->input('note')['pain_attention'];
                $scheduleOrder->note_pathology_attention = $request->input('note')['pathology_attention'];
                $scheduleOrder->note = $request->input('note')['another'];

                $employees = $scheduleService['selected_technicians'];

                if ($employees) {
                    foreach ($employees as $employee) {
                        $timeBook = Carbon::createFromFormat('H:i', $scheduleService['time']);
                        $timeBookUpdate = $timeBook->addMinutes(Service::where('id', $scheduleService['service']['id'])->first()->duration);
                        $workShiftFilter = WorkShift::where('date', Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'))
                            ->where('status', 0)
                            ->whereRaw("TIME(from_at) <= ?", [$timeBookUpdate->format('H:i')])
                            ->where('employee_id', $employee['id'])
                            ->first();
                        if ($workShiftFilter) {
                            $workShiftService->work_shift_id = $workShiftFilter->id;
                            //WorkShift
                            // $workShift = WorkShift::find($workShiftFilter->id);
                            // $fromAtWorkShift = strtotime($workShift->from_at);
                            // $toAtWorkShift = strtotime($workShift->to_at);
                            // $currentTimeExits = date('H:i:s', $fromAtWorkShift + ($scheduleService['service']['duration'] * 60));

                            // if ($toAtWorkShift < $currentTimeExits) {
                            //     $workShift->status = 1;
                            // } else {
                            //     $workShift->from_at = $currentTimeExits;
                            // }

                            // $workShift->save();
                            $workShiftService->save();
                            $workShiftServiceIDs[] = $workShiftService->id;
                        }
                    }
                }
            }

            $scheduleOrder->work_shift_services = implode(',', $workShiftServiceIDs);
            $scheduleOrder->save();
        }

        if ($user) {
            $response = $this->_formatBaseResponse(200, null, 'Cập nhật lịch thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Cập nhật lịch không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }

    public function cancelSchedule($id)
    {
        $user = auth()->user();
        $scheduleOrder = ScheduleOrder::findOrFail($id);
        if ($scheduleOrder) {
            $scheduleOrder->status = 3;
            $scheduleOrder->save();
        }
        $workShiftServiceIds = explode(',', $scheduleOrder->work_shift_services);
        $workShiftServices = WorkShiftService::whereIn('id', $workShiftServiceIds)->get();
        $workShifts = [];
        $services = [];

        foreach ($workShiftServices as $workShiftService) {
            $workShift = WorkShift::where("id", $workShiftService->work_shift_id)->first();
            $service = Service::where("id", $workShiftService->service_id)->first();

            if ($workShift && $service) {
                $workShifts[] = $workShift;
                $services[] = $service;
            }
        }

        foreach ($workShifts as $workShift) {
            foreach ($services as $service) {
                $workShiftFilter = WorkShift::find($workShift->id);
                $fromAtWorkShift = strtotime($workShiftFilter->from_at);
                $currentTimeExits = date('H:i:s', $fromAtWorkShift - ($service->duration * 60));
                $workShiftFilter->from_at = $currentTimeExits;
                $workShiftFilter->save();
            }
        }
        if ($user && $scheduleOrder->status == 3) {
            $response = $this->_formatBaseResponse(405, null, 'Lịch đã được huỷ', []);
            return response()->json($response);
        } else if ($user) {
            $response = $this->_formatBaseResponse(200, null, 'Huỷ lịch thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Huỷ lịch không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
