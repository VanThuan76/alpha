<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $table = 'beds';

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function orders()
    {
        return $this->hasMany(RoomOrder::class);
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
