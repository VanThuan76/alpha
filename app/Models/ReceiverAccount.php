<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiverAccount extends Model
{
    protected $table = 'receiver_accounts';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
