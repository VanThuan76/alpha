<?php

namespace App\Models\Operation;

use App\Models\Facility\Bed;
use App\Models\Hrm\Employee;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    protected $table = 'ticket_order';

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_code', 'code');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_code', 'code');
    }
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }
    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class, 'employee_id');
    // }
    // public function bed()
    // {
    //     return $this->belongsTo(Bed::class, 'bed_id');
    // }
    protected $hidden = [
    ];

    protected $guarded = [];
}
