<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use Illuminate\Http\Request;

class Facility_ZoneController extends Controller
{
    use CommonResponse;
    public function find(Request $request)
    {
        $branchId = $request->get('branch_id');
        $zone = Zone::where('branch_id', $branchId)->get();
        return $zone;
    }
    public function getById(Request $request)
    {
        $id = $request->get('q');
        $zone = Zone::find($id);
        return $zone;
    }
    public function getErp(Request $request)
    {
        $limit = $request->input('limit', 20);
        $previousLastZoneId = $request->input('previous_last_zone_id', 0);
        $keyWords = $request->input('search_keyword');
        if ($request->input('limit')) {
            $limit = 20;
        }

        $zonesQuery = Zone::orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_zone_id') !== null) {
            $zonesQuery->where('id', '<', $previousLastZoneId);
        }

        if ($request->input('search_keyword') !== null) {
            $zonesQuery->where(function ($query) use ($keyWords) {
                $query->where('name', 'like', '%' . $keyWords . '%');
            });
        }
        $zones = $zonesQuery->get();

        $result = $zones->map(function ($zone) {
            return [
                'id' => $zone->id,
                'branch' => Branch::where('id', $zone->branch_id)->first(),
                'name' => $zone->name,
                'status' => $zone->status,
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
