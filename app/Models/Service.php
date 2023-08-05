<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
	protected $hidden = [
    ];

	protected $guarded = [];
}
