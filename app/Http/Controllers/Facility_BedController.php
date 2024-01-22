<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Branch;
use App\Models\Facility\Bed;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use Illuminate\Http\Request;

class Facility_BedController extends Controller
{
    use CommonResponse;
    public function getErp(Request $request)
    {
        $limit = $request->input('limit', 20);
        $previousLastBedId = $request->input('previous_last_bed_id', 0);
        $keyWords = $request->input('search_keyword');
        if ($request->input('limit')) {
            $limit = 20;
        }

        $bedsQuery = Bed::orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_bed_id') !== null) {
            $bedsQuery->where('id', '<', $previousLastBedId);
        }

        if ($request->input('search_keyword') !== null) {
            $bedsQuery->where(function ($query) use ($keyWords) {
                $query->where('name', 'like', '%' . $keyWords . '%');
            });
        }
        $beds = $bedsQuery->get();

        $result = $beds->map(function ($bed) {
            return [
                'id' => $bed->id,
                'branch' => Branch::where('id', $bed->branch_id)->first(),
                'zone' => Zone::where('id', $bed->zone_id)->first(),
                'room' => Room::where('id', $bed->room_id)->first(),
                'name' => $bed->name,
                'status' => $bed->status,
            ];
        });
        if ($result) {
            $response = $this->_formatBaseResponse(200, $result, 'Lấy thông tin thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lấy thông tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
