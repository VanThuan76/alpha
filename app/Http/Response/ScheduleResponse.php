<?php

namespace App\Http\Response;

use App\Models\Facility\Bed;
use App\Models\Facility\Branch;
use App\Models\Hrm\Employee;
use App\Models\Product\Service;

trait ScheduleResponse
{
    private function _formatBranchResponse($service){
        $branchesArray = is_array($service->branches) ? $service->branches : array_map('trim', explode(',', $service->branches));
        $result = array_map(function ($branch) {
            $branch = Branch::where('id', $branch)->first();
            return $branch ? ['id' => $branch->id, "name" => $branch->name] : [];
        }, $branchesArray);
        return $result;
    }
    private function _formatServiceResponse($services, $workShifts, $employeeAddId)
    {
        $result = $services->map(function ($service) use ($workShifts, $employeeAddId) {
            $branchesExist = Service::where("id", $service->id)->first()->branches;
            $branchesArray = is_array($branchesExist) ? $branchesExist : array_map('trim', explode(',', $branchesExist));

            $employeeExist = [];

            foreach ($workShifts as $workShift) {
                $bedBranches = Bed::where("id", $workShift->bed_id)->pluck('branch_id')->toArray();
                $intersect = array_intersect($bedBranches, $branchesArray);
                if (!empty($intersect)) {
                    $employee = Employee::where("id", $workShift->employee_id)->first();
                    $employeeAdd = Employee::where("id", $employeeAddId)->first();

                    if ($employee && $employeeAdd) {
                        $mergedEmployee = $employee->toArray() + $employeeAdd->toArray();
                        $employeeExist[] = [
                            'id' => $mergedEmployee['id'],
                            'name' => $mergedEmployee['name'],
                            'level' => $mergedEmployee['level'],
                            'rate' => $mergedEmployee['rate'],
                            'served_count' => $mergedEmployee['served_count'],
                        ];
                    } else {
                        $employeeExist[] = [
                            'id' => $employee['id'],
                            'name' => $employee['name'],
                            'level' => $employee['level'],
                            'rate' => $employee['rate'],
                            'served_count' => $employee['served_count'],
                        ];
                    }

                }
            }

            return [
                'id' => $service->id,
                'title' => $service->name,
                'duration' => $service->duration,
                'technicians' => $employeeExist,
                'image_url' => $service->image,
            ];
        });

        return $result;
    }
}
