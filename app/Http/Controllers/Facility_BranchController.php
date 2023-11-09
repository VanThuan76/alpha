<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Models\Facility\Branch;
use Illuminate\Http\Request;

class Facility_BranchController extends Controller
{
    use CommonResponse;
    public function get(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 20);
        $previousLastBranchId = $request->input('previous_last_branch_id', 0);
        $keyWords = $request->input('search_keyword');
        if ($request->input('limit')) {
            $limit = 20;
        }
        
        $branchesQuery = Branch::orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_branch_id') !== null) {
            $branchesQuery->where('id', '<', $previousLastBranchId);
        }
        
        if($request->input('search_keyword') !== null){
            $branchesQuery->where(function($query) use ($keyWords) {
                $query->where('name', 'like', '%' . $keyWords . '%')
                      ->orWhere('address', 'like', '%' . $keyWords . '%');
            });
        }
        $branches = $branchesQuery->get();

        $result = [
            'branches' => $branches->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'status' => $branch->status,
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
