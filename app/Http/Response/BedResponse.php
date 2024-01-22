<?php

namespace App\Http\Response;

use App\Models\Facility\Bed;
use App\Models\Facility\Branch;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use App\Models\Hrm\Employee;

trait BedResponse
{
    private function _formatBed($bedId)
    {
        $bed = Bed::where('id', $bedId)->first();
        if (!$bed) {
            return null;
        }
        $branch = Branch::where('id', $bed->branch_id)->first();
        $zone = Zone::where('id', $bed->zone_id)->first();
        $room = Room::where('id', $bed->room_id)->first();
        $employee = Employee::where('id', $bed->employee_id)->first();
        return [
            'id' => $bed->id,
            "name" => $bed->name,
            "status" => $bed->status,
            "employee_id" => $bed->employee_id,
            "branch_id" => $bed->branch_id,
            "zone_id" => $bed->zone_id,
            "created_at" => $bed->created_at,
            "updated_at" => $bed->updated_at,
            'employee' => $employee,
            'branch' => $branch,
            'zone' => $zone,
            'room' => $room,
        ];
    }
}
