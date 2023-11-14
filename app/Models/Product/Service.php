<?php

namespace App\Models\Product;

use App\Models\Facility\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    public function getImageAttribute($value)
    {
        if ($value) {
            return Storage::disk('admin')->url($value);
        }
    }
    public function getBranchesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setBranchesAttribute($value)
    {
        $this->attributes['branches'] = implode(',', $value);
    }
    public function getCustomerTypesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setCustomerTypesAttribute($value)
    {
        $this->attributes['customer_types'] = implode(',', $value);
    }
    public function getTagsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = implode(',', $value);
    }
	protected $hidden = [
    ];

	protected $guarded = [];
}
