<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';

    public function getLogoAttribute($value)
    {
        if ($value) {
            return Storage::disk('admin')->url($value);
        }
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
