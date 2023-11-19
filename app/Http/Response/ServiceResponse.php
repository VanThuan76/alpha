<?php

namespace App\Http\Response;

use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;

trait ServiceResponse
{
    private function _formatServiceResponse($services)
    {
        $result = $services->map(function ($service) {
            $workShifts = WorkShift::get();
            $employeeExist = $workShifts->map(function ($workShift) {
                $beds = Bed::where("id", $workShift->bed_id)->get();
                $employee = $beds->map(function ($bed) use ($workShift) {
                    $serviceExist = Service::where("branch_id", $bed->branch_id)->get();
                    if ($serviceExist) {
                        $employee = Employee::where("id", $workShift->employee_id)->first();
                        return [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'level' => $employee->level,
                            'rate' => $employee->rate,
                            'tag' => $employee->tag,
                        ];
                    } else {
                        return null;
                    }
                });
                return $employee;
            });

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
