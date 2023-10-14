<?php

namespace App\Models\Marketing;

use App\Models\Facility\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
    public function getThumbnailAttribute($value)
    {
        if ($value) {
            return Storage::disk('admin')->url($value);
        }
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
