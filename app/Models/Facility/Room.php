<?php

namespace App\Models\Facility;

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

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
