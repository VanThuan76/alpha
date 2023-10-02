<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function getImageAttribute($value)
    {
        if ($value) {
            return Storage::disk('admin')->url($value);
        }
    }
    
	protected $hidden = [
    ];

	protected $guarded = [];
}
