<?php

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $table = 'beds';

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function orders()
    {
        return $this->hasMany(RoomOrder::class);
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
