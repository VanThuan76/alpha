<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Branch;
use App\Models\Hrm\Employee;
use Illuminate\Http\Request;

class Hrm_EmployeeController extends Controller
{
    use CommonResponse;
    public function getTechnicians(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 20);
        $previousLastTechnicianId = $request->input('previous_last_technician_id', 0);

        if ($request->input('limit')) {
            $limit = 20;
        }
        
        $employeeQuery = Employee::where('position_id', 2)->orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_technician_id') !== null) {
            $employeeQuery->where('id', '<', $previousLastTechnicianId);
        }
        
        $technicians = $employeeQuery->get();

        $result = [
            'technicians' => $technicians->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
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
