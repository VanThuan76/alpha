<?php

namespace App\Http\Controllers;

use App\Http\Response\CommonResponse;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\CommonCode;
use App\Models\Core\CustomerType;
use App\Models\Facility\Branch;
use App\Models\Product\Promotion;
use App\Models\Product\PromotionUser;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Illuminate\Http\Request;

class Prod_PromotionController extends BaseController
{
    use CommonResponse;
    public function get(Request $request)
    {
        $user = auth()->user();
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
                $branchIds = $promotion->branches;
                $branchesArray = [];
                if (is_array($branchIds)) {
                    $branchesArray = Branch::whereIn("id", $branchIds)->pluck('name')->toArray();
                }
                $ranksIds = $promotion->ranks;
                $ranksArray = [];
                if (is_array($ranksIds)) {
                    $ranksArray = CustomerType::whereIn("id", $ranksIds)->pluck('name')->toArray();
                }
                $userIds = $promotion->users;
                $usersArray = [];
                if (is_array($userIds)) {
                    $usersArray = User::whereIn("id", $userIds)->pluck('name')->toArray();
                }
                $serviceIds = $promotion->services;
                $servicesArray = [];
                if (is_array($serviceIds)) {
                    $servicesArray = Service::whereIn("id", $serviceIds)->pluck('name')->toArray();
                }
                $productsArray = is_array($promotion->products) ? $promotion->products : array_map('trim', explode(',', $promotion->products));
                $statusName = CommonCode::where("id", $promotion->status)->pluck('description_vi');

                return [
                    'id' => $promotion->id,
                    'image_url' => $promotion->image_url != null ? 'https://dev.erp.senbachdiep.com:8443/storage/' . $promotion->image_url : null,
                    'title' => $promotion->title,
                    'start_date' => $promotion->start_date,
                    'end_date' => $promotion->end_date,
                    'applied_branches' => $branchesArray,
                    'applied_ranks' => $ranksArray,
                    'applied_users' => $usersArray,
                    'applied_services' => $servicesArray,
                    'applied_products' => $productsArray,
                    'details' => $promotion->details,
                    'tags' => $tagsArray,
                    'used_percent' => $promotion->used_percent,
                    'status' => $statusName,
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
    public function save(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $promotionId = $request->input("id");
        $promotionUserModel = PromotionUser::firstOrNew([
            'user_id' => $userId,
            'promotion_id' => $promotionId,
        ]);
        $promotionUserModel->save();

        if ($promotionUserModel) {
            $response = $this->_formatBaseResponse(200, null, 'Lưu thành công', []);
            return response()->json($response);
        } else {
            $response = $this->_formatBaseResponse(401, null, 'Lưu không thành công', []);
            return response()->json($response, 401);
        }
    }
    public function getPromotionSave(Request $request)
    {
        $user = auth()->user();
        $limit = $request->input('limit', 20);
        $previousLastPromotionId = $request->input('previous_last_service_id', 0);

        if ($request->input('limit')) {
            $limit = 20;
        }

        $promotionQuery = PromotionUser::where('user_id', $user->id)->orderBy('id', 'desc')->limit($limit);

        if ($request->input('previous_last_service_id') !== null) {
            $promotionQuery->where('id', '<', $previousLastPromotionId);
        }

        $promotions = $promotionQuery->get();

        $result = [
            'promotions' => $promotions->map(function ($promotion) {
                $tagsArray = is_array($promotion->tags) ? $promotion->tags : array_map('trim', explode(',', $promotion->tags));
                $branchIds = $promotion->branches;
                $branchesArray = [];
                if (is_array($branchIds)) {
                    $branchesArray = Branch::whereIn("id", $branchIds)->pluck('name')->toArray();
                }
                $ranksIds = $promotion->ranks;
                $ranksArray = [];
                if (is_array($ranksIds)) {
                    $ranksArray = CustomerType::whereIn("id", $ranksIds)->pluck('name')->toArray();
                }
                $userIds = $promotion->users;
                $usersArray = [];
                if (is_array($userIds)) {
                    $usersArray = User::whereIn("id", $userIds)->pluck('name')->toArray();
                }
                $serviceIds = $promotion->services;
                $servicesArray = [];
                if (is_array($serviceIds)) {
                    $servicesArray = Service::whereIn("id", $serviceIds)->pluck('name')->toArray();
                }
                $productsArray = is_array($promotion->products) ? $promotion->products : array_map('trim', explode(',', $promotion->products));
                $statusName = CommonCode::where("id", $promotion->status)->pluck('description_vi');

                return [
                    'id' => $promotion->id,
                    'image_url' => $promotion->image_url != null ? 'https://erp.senbachdiep.com/storage/' . $promotion->image_url : null,
                    'title' => $promotion->title,
                    'start_date' => $promotion->start_date,
                    'end_date' => $promotion->end_date,
                    'applied_branches' => $branchesArray,
                    'applied_ranks' => $ranksArray,
                    'applied_users' => $usersArray,
                    'applied_services' => $servicesArray,
                    'applied_products' => $productsArray,
                    'details' => $promotion->details,
                    'tags' => $tagsArray,
                    'used_percent' => $promotion->used_percent,
                    'status' => $statusName,
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
