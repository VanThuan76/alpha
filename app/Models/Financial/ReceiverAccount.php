<?php

namespace App\Models\Financial;

use App\Models\Facility\Branch;
use Illuminate\Database\Eloquent\Model;

class ReceiverAccount extends Model
{
    protected $table = 'receiver_accounts';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
