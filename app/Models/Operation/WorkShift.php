<?php

namespace App\Models\Operation;

use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $table = 'work_shift';

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
