<?php

namespace App\Http\Response;

use App\Models\Facility\Bed;
use App\Models\Facility\Branch;
use App\Models\Hrm\Employee;
use App\Models\Product\Service;

trait ScheduleResponse
{
    private function _formatBranchResponse($branchId)
    {
        $branch = Branch::where('id', $branchId)->first();
        return $branch ? ['id' => $branch->id, "name" => $branch->name] : [];
    }
    private function _formatServiceResponse($services, $workShifts, $schedule)
    {
        $result = $services->map(function ($service) use ($workShifts, $schedule) {
            $branchesExist = Service::where("id", $service->id)->first()->branches;
            $branchesArray = is_array($branchesExist) ? $branchesExist : array_map('trim', explode(',', $branchesExist));
            $employeeExist = [];
            foreach ($workShifts as $workShift) {
                $bedBranches = Bed::where("id", $workShift->bed_id)->pluck('branch_id')->toArray();
                $intersect = array_intersect($bedBranches, $branchesArray);
                if (!empty($intersect)) {
                    $employee = Employee::where("id", $workShift->employee_id)->first();

                    if ($employee) {
                        $mergedEmployee = $employee->toArray();
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
                'service' => [
                    'id' => $service->id,
                    'title' => $service->name,
                    'duration' => $service->duration,
                    'image_url' => $service->image,
                ],
                'time' => $schedule->book_at,
                'technician_count' => $service->staff_number,
                'selected_technicians' => $employeeExist,
            ];
        });
        return $result;
    }
}
