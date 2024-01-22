<?php

namespace App\Http\Controllers;

use App\Http\Response\BedResponse;
use App\Http\Response\CommonResponse;
use App\Models\Core\CustomerType;
use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkSchedule;
use App\Models\Operation\WorkShift;
use App\Models\Operation\WorkShiftService;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Operation_WorkScheduleController extends Controller
{
    use CommonResponse, BedResponse;
    public function generate(Request $request)
    {
        $beds = Bed::where('status', 1)->pluck('name', 'id');
        foreach ($beds as $id => $name) {
            $today = Carbon::today()->toDateString();
            for ($i = 0; $i < 1; $i++) {
                $today = Carbon::parse($today)->addDay()->toDateString();
                $workSchedule = WorkSchedule::where("bed_id", $id)->where("date", $today)->first();
                if (is_null($workSchedule)) {
                    $workSchedule = new WorkSchedule();
                    $workSchedule->bed_id = $id;
                    $workSchedule->date = $today;
                    $workSchedule->save();
                }
            }
        }
        return json_encode($beds);
        $customerTypes = CustomerType::orderBy('order')->pluck('accumulated_money', 'id');
        $users = User::all();
        foreach ($users as $i => $user) {
            if (isset($topups[$user->id])) {
                $user->accumulated_amount = $topups[$user->id];
                foreach ($customerTypes as $customerType => $amount) {
                    if ($user->accumulated_amount > $amount) {
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

    public function getWorkShiftErp(Request $request)
    {
        $query = WorkShift::query()->orderBy("created_at", "asc");
        $filters = $request->input('filters', []);
        foreach ($filters as $filter) {
            $field = $filter['field'];
            $value = $filter['value'];
            if (!empty($value)) {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }
        $sorts = $request->input('sorts', []);
        foreach ($sorts as $sort) {
            $field = $sort['field'];
            $direction = $sort['direction'];
            if (!empty($field) && !empty($direction)) {
                $query->orderBy($field, $direction);
            }
        }
        $size = $request->input('size', 10);
        $workShifts = $query->paginate($size, ['*'], 'page', $request->input('page', 1));

        $formattedWorkShifts = $workShifts->groupBy('bed_id')->map(function ($bedWorkShifts) {
            return [
                'bed' => $this->_formatBed($bedWorkShifts->first()->bed_id),
                'workShifts' => $bedWorkShifts->map(function ($workShift) {
                    return [
                        'id' => $workShift->id,
                        "date" => $workShift->date,
                        "employee" => Employee::where('id', $workShift->employee_id)->first(),
                        "work_shift_services" => WorkShiftService::where('work_shift_id', $workShift->id)
                            ->get()
                            ->map(function ($workShiftService) {
                                $service = Service::where('id', $workShiftService->service_id)->first();
                                return [
                                    'id' => $workShiftService->id,
                                    'work_shift_id' => $workShiftService->work_shift_id,
                                    'service' => $service,
                                    'created_at' => $workShiftService->created_at,
                                    'updated_at' => $workShiftService->updated_at,
                                ];
                            }),
                        "from_at" => $workShift->from_at,
                        "to_at" => $workShift->to_at,
                        "status" => $workShift->status,
                        "created_at" => $workShift->created_at,
                        "updated_at" => $workShift->updated_at,
                    ];
                }),
            ];
        })->values();

        $totalPages = $workShifts->lastPage();

        return response()->json(
            $this->_formatCountResponse(
                $formattedWorkShifts,
                $workShifts->perPage(),
                $totalPages
            )
        );
    }

    public function newWorkShiftErp(Request $request){
        $workShift = new WorkShift();
        $workShift->branch_id = $request->branch_id;
        $workShift->date = $request->date;
        $workShift->zone_id = $request->zone_id;
        $workShift->from_at = $request->from_at;
        $workShift->to_at = $request->to_at;
        $workShift->bed_id = $request->bed_id;
        $workShift->employee_id = $request->employee_id;
        $workShift->save();
        if($workShift){
            $response = $this->_formatBaseResponse(200, null, 'Tạo tin thành công', []);
            return response()->json($response);
        }else{
            $response = $this->_formatBaseResponse(401, null, 'Tạo tin không thành công', ['errors' => 'Unauthorised']);
            return response()->json($response, 401);
        }
    }
}
