<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
