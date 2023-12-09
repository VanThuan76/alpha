<?php

namespace App\Models\Sales;

use App\Models\Facility\Branch;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'sales';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
