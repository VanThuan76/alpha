<?php

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
