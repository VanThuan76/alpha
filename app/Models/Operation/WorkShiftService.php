<?php

namespace App\Models\Operation;

use App\Models\Product\Service;
use Illuminate\Database\Eloquent\Model;

class WorkShiftService extends Model
{
    protected $table = 'work_shift_service';

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }
    public function service_id()
    {
        return $this->belongsTo(Service::class, 'employee_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
