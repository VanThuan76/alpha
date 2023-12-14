<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Hrm_EmployeeController extends Controller
{
    use CommonResponse;
    public function getTechnicians(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 20);
        $previousLastTechnicianId = $request->input('previous_last_technician_id', 0);
        $branchId = $request->input('branch_id');
        $serviceId = $request->input('service_id');
        $availableTimes = [];

        if ($request->input('limit')) {
            $limit = 20;
        }

        $employeeQuery = Employee::where('position_id', 2)->orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_technician_id') !== null) {
            $employeeQuery->where('id', '<', $previousLastTechnicianId);
        }

        if ($request->input('branch_id') !== null) {
            $employeeQuery->where('branch_id', '=', $branchId);
        }

        if ($request->input('service_id') !== null) {
            if ($request->input('service_id') !== null) {
                $service = Service::where("id", $serviceId)->first();
                $employeesId = [];
                if ($service) {
                    $branches = $service->branches;
                    if (is_array($branches)) {
                        $workShifts = WorkShift::where('status', 0)->get(); //Dang ranh
                        foreach ($workShifts as $workShift) {
                            $bed = Bed::find($workShift->bed_id);
                            if ($bed && in_array($bed->branch_id, $branches)) {
                                $employeesId[] = $workShift->employee_id;
                            }
                        }
                        $employeeQuery->whereIn('id', $employeesId);
                    } else {
                    }
                }
            }

        }

        if($request->input('schedule_time') !== null){
            if($request->input('branch_id') !== null){
                $today = Carbon::now();
                $branchId = $request->input('branch_id');
                $scheduleTime = strtotime($request->input('schedule_time'));
                $bedIds = Bed::where('branch_id', $branchId)->pluck('id')->toArray();
                $workShifts = WorkShift::whereIn('bed_id', $bedIds)
                ->whereDate('date', '>=', $today->toDateString())
                ->get()
                ->filter(function ($workShift) use ($scheduleTime) {
                    $fromAt = strtotime($workShift->from_at);
                    $toAt = strtotime($workShift->to_at);
                    return $fromAt <= $scheduleTime && $scheduleTime <= $toAt;
                });
                $employeeIds = $workShifts->pluck('employee_id')->unique()->toArray();
                foreach ($workShifts as $workShift) {
                    $fromAt = $workShift->from_at;
                    $toAt = $workShift->to_at;
                    $availableTimes[] = $fromAt . ' - ' . $toAt;
                }
                $employeeQuery->whereIn('id', $employeeIds);
            }
        }

        if ($request->input('search_keyword') !== null) {
            $searchKeyword = $request->input('search_keyword');
            $employeeQuery->where(function ($query) use ($searchKeyword) {
                $query->where('name', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('phone_number', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('address', 'LIKE', '%' . $searchKeyword . '%')
                    ->orWhere('rate', 'LIKE', '%' . $searchKeyword . '%');
            });
        }
        $technicians = $employeeQuery->get();

        $result = [
            'technicians' => $technicians->map(function ($technician) use ($availableTimes) {
                return [
                    'id' => $technician->id,
                    'name' => $technician->name,
                    'branch_id' => $technician->branch_id,
                    'avatar_url' => $technician->avatar ? "https://erp.senbachdiep.com/storage/" . $technician->avatar : null ,
                    'level' => $technician->level,
                    'served_user_count' => $technician->served_user_count,
                    'rate' => $technician->rate,
                    'available_times' => $availableTimes,
                    'status' => $technician->status,
                    'experience' => $technician->experience,
                    'specialize' => $technician->specialize,
                    'service' => $technician->service,
                    'force' => $technician->force,
                ];
            })
        ];
        if ($user) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
