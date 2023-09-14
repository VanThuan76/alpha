<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
	protected $hidden = [
    ];

	protected $guarded = [];
}
