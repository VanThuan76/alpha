<?php

namespace App\Models\Operation;

use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Illuminate\Database\Eloquent\Model;

class ScheduleOrder extends Model
{
    protected $table = 'schedule_order';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function workShiftService()
    {
        return $this->belongsTo(WorkShiftService::class, 'work_shift_services');
    }
    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
