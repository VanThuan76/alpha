<?php

namespace App\Models\Product;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';


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

    public function getRanksAttribute($value)
    {
        return explode(',', $value);
    }

    public function setRanksAttribute($value)
    {
        $this->attributes['ranks'] = implode(',', $value);
    }

    public function getUsersAttribute($value)
    {
        return explode(',', $value);
    }

    public function setUsersAttribute($value)
    {
        $this->attributes['users'] = implode(',', $value);
    }

    public function getServicesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = implode(',', $value);
    }
	protected $hidden = [
    ];

	protected $guarded = [];
}
