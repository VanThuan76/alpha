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

    public function getShift1Attribute($value)
    {
        if (is_null($value)) return [];
        return explode(',', $value);
    }

    public function setShift1Attribute($value)
    {
        $this->attributes['shift1'] = implode(',', $value);
    }

    public function getShift2Attribute($value)
    {
        if (is_null($value)) return [];
        return explode(',', $value);
    }

    public function setShift2Attribute($value)
    {
        $this->attributes['shift2'] = implode(',', $value);
    }

    public function getShift3Attribute($value)
    {
        if (is_null($value)) return [];
        return explode(',', $value);
    }

    public function setShift3Attribute($value)
    {
        $this->attributes['shift3'] = implode(',', $value);
    }

    public function getShift4Attribute($value)
    {
        if (is_null($value)) return [];
        return explode(',', $value);
    }

    public function setShift4Attribute($value)
    {
        $this->attributes['shift4'] = implode(',', $value);
    }
    
	protected $hidden = [
    ];

	protected $guarded = [];
}
