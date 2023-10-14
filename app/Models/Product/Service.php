<?php

namespace App\Models\Product;

use App\Models\Facility\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
