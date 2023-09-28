<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PointTopup;
use App\Models\BedOrder;
use App\Models\CustomerType;
use App\Models\Service;

class CustomerController extends Controller
{

    public function find(Request $request)
    {
        $id = $request->get('q');
        $user = User::find($id);
        $customerType = $user->customerType;
        return [$user, $customerType];
    }

    public function services(Request $request)
    {
        $userId = $request->get('q');
        return Service::whereIn('id', BedOrder::where('user_id', $userId)->where('status', 0)->get('service_id'))->get(['id', DB::raw('name as text')]);
    }

    public function update(Request $request)
    {
        $topups = PointTopup::select(["user_id",DB::raw("SUM(added_amount) as amount")])
        ->groupBy(["user_id"])->where('created_at', '>=', Carbon::now()->subDays(30)->toDateTimeString())->pluck('amount', 'user_id');
        $customerTypes = CustomerType::orderBy('order')->pluck('accumulated_money', 'id');
        $users = User::all();
        foreach ($users as $i => $user){
            if (isset($topups[$user->id])){
                $user->accumulated_amount = $topups[$user->id];
                foreach ($customerTypes as $customerType => $amount){
                    if ($user->accumulated_amount > $amount){
                        $user->customer_type = $customerType;
                    }
                }
            } else {
                $user->customer_type = 0;
                $user->accumulated_amount = 0;
            }
            $user->save();
        }
        return json_encode($customerTypes);
    }
    
}
