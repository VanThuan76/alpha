<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTopup extends Model
{
    protected $table = 'point_topups';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function staff()
    {
        return $this->belongsTo(AdminUser::class, 'staff_id');
    }

    public function sale()
    {
        return $this->belongsTo(AdminUser::class, 'sale_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
