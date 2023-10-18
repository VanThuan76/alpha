<?php

namespace App\Http\Controllers;

use App\Models\Facility\Room;
use Illuminate\Http\Request;

class Facility_RoomController extends Controller
{
    public function find(Request $request)
    {
        $zoneId = $request->get('zone_id');
        $room = Room::where('zone_id', $zoneId)->get();
        return $room;
    }
    public function getById(Request $request)
    {
        $id = $request->get('q');
        $room = Room::find($id);
        return $room;
    }
}
