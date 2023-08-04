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

	protected $hidden = [
    ];

	protected $guarded = [];
}
