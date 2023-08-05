<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'work_schedules';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
	protected $hidden = [
    ];

	protected $guarded = [];
}
