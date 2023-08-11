<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

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
