<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Config;
use Carbon\Carbon;
use App\Models\Bed;
use App\Models\WorkSchedule;
use App\Models\RoomOrder;
use App\Models\Service;

class BedOrderController extends Controller
{

    public function updateStatus(Request $request)
    {
        $bed = Bed::find($request->post('bed_id'));
        if ($bed->status == -1){
            $bed->status = 1;
        } else {
            $bed->status = -1;
        }
        $bed->save();
        return json_encode($bed);
    }
    
}
