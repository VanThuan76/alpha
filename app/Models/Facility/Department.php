<?php

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';

	protected $hidden = [
    ];

	protected $guarded = [];
    public function getPositionsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setPositionsAttribute($value)
    {
        $this->attributes['positions'] = implode(',', $value);
    }
}
