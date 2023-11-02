<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Core\CustomerType;
use App\Models\Facility\Branch;
use App\Models\Product\Promotion;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Illuminate\Http\Request;

class Prod_PromotionController extends BaseController
{
    use CommonResponse;
    public function get(Request $request)
    {
        $user = auth('api')->user();
        $limit = $request->input('limit', 20);
        $previousLastPromotionId = $request->input('previous_last_service_id', 0);

        if ($request->input('limit')) {
            $limit = 20;
        }

        $promotionQuery = Promotion::where('status', 1)->orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_service_id') !== null) {
            $promotionQuery->where('id', '<', $previousLastPromotionId);
        }

        $promotions = $promotionQuery->get();

        $result = [
            'promotions' => $promotions->map(function ($promotion) {
                $tagsArray = is_array($promotion->tags) ? $promotion->tags : array_map('trim', explode(',', $promotion->tags));
                $branchesArray = is_array($promotion->branches) ? $promotion->branches : array_map('trim', explode(',', Branch::where("id", $promotion->branches)->first()->name));
                $ranksArray = is_array($promotion->ranks) ? $promotion->ranks : array_map('trim', explode(',', CustomerType::where("id", $promotion->ranks)->first()->name));
                $usersArray = is_array($promotion->users) ? $promotion->users : array_map('trim', explode(',', User::where("id", $promotion->users)->first()->name));
                $servicesArray = is_array($promotion->services) ? $promotion->services : array_map('trim', explode(',', Service::where("id", $promotion->services)->first()->name));
                $productsArray = is_array($promotion->products) ? $promotion->products : array_map('trim', explode(',', $promotion->products));


                return [
                    'id' => $promotion->id,
                    'image_url' => $promotion->image_url,
                    'title' => $promotion->title,
                    'start_date' => $promotion->start_date,
                    'end_date' => $promotion->end_date,
                    'applied_branchs' => $branchesArray,
                    'applied_ranks' => $ranksArray,
                    'applied_users' => $usersArray,
                    'applied_services' => $servicesArray,
                    'applied_products' => $productsArray,
                    'details' => $promotion->details,
                    'tags' => $tagsArray,
                    'used_percent' => $promotion->used_percent,
                    'status' => $promotion->status,
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
