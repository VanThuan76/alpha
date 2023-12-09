<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use Illuminate\Http\Request;

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
            'technicians' => $technicians->map(function ($technician) {
                return [
                    'id' => $technician->id,
                    'name' => $technician->name,
                    'branch_id' => $technician->branch_id,
                    'avatar_url' => $technician->avatar ? "https://erp.senbachdiep.com/storage/" . $technician->avatar : null ,
                    'level' => $technician->level,
                    'served_user_count' => $technician->served_user_count,
                    'rate' => $technician->rate,
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
