<?php

namespace App\Models\Hrm;

use App\Models\Facility\Branch;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
