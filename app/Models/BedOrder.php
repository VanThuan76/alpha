<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedOrder extends Model
{
    protected $table = 'bed_orders';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function technician1()
    {
        return $this->belongsTo(AdminUser::class, 'technician_id1');
    }

    public function technician2()
    {
        return $this->belongsTo(AdminUser::class, 'technician_id2');
    }

    public function technician3()
    {
        return $this->belongsTo(AdminUser::class, 'technician_id3');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
