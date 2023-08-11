<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomOrder extends Model
{
    protected $table = 'room_orders';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function technician()
    {
        return $this->belongsTo(AdminUser::class, 'technician_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
