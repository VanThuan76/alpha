<?php

namespace App\Http\Response;

use App\Models\Facility\Bed;

trait BedResponse
{
    private function _formatBed($bedId)
    {
        $bed = Bed::with(['branch', 'zone', 'room'])->find($bedId);

        if (!$bed) {
            return null;
        }

        $branchData = [
            'id' => $bed->branch->id,
            'name' => $bed->branch->name,
            'address' => $bed->branch->address,
            'status' => $bed->branch->status,
            'created_at' => $bed->branch->created_at,
            'updated_at' => $bed->branch->updated_at,
        ];

        $zoneData = [
            'id' => $bed->zone->id,
            'name' => $bed->zone->name,
            'status' => $bed->zone->status,
            'created_at' => $bed->zone->created_at,
            'updated_at' => $bed->zone->updated_at,
        ];

        $roomData = [
            'id' => $bed->room->id,
            'zone_id' => $bed->room->zone_id,
            'name' => $bed->room->name,
            'status' => $bed->room->status,
            'created_at' => $bed->room->created_at,
            'updated_at' => $bed->room->updated_at,
        ];

        return [
            'id' => $bed->id,
            "name" => $bed->name,
            "status" => $bed->status,
            "employee_id" => $bed->employee_id,
            "created_at" => $bed->created_at,
            "updated_at" => $bed->updated_at,
            'branch' => $branchData,
            'zone' => $zoneData,
            'room' => $roomData,
        ];
    }
}
