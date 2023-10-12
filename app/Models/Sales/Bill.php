<?php

namespace App\Models\Sales;

use App\Models\AdminUser;
use App\Models\Facility\Unit;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    public function getServiceIdAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    public function setServiceIdAttribute($value)
    {
        $this->attributes['service_id'] = json_encode($value);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(AdminUser::class, 'seller_id');
    }

    public function creator()
    {
        return $this->belongsTo(AdminUser::class, 'creator_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
