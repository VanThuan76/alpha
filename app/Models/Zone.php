<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zones';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
