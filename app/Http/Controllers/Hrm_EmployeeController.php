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
        $branchId = $request->input('branch_id');

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
