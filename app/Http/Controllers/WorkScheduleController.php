<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\Branch;

class WorkScheduleController extends Controller
{

    public function generate(Request $request)
    {
        $branches = Branch::where('status', 1)->pluck('name', 'id');
        foreach($branches as $id=>$name){
            $today = Carbon::today()->toDateString();
            for ($i = 0; $i < 7; $i++) {
                $today = Carbon::parse($today)->addDay()->toDateString();
                $workSchedule = WorkSchedule::where("branch_id", $id)->where("date", $today)->first();
                if (is_null($workSchedule)){
                    $workSchedule = new WorkSchedule();
                    $workSchedule->branch_id = $id;
                    $workSchedule->date = $today;
                    $workSchedule->save();
                }
            }
        }
        return json_encode($branches);
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
