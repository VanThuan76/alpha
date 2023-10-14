<?php

namespace App\Models\Financial;

use App\Models\AdminUser;
use App\Models\Core\CustomerType;
use App\Models\Facility\Branch;
use App\Models\Sales\User;
use Illuminate\Database\Eloquent\Model;

class PointTopup extends Model
{
    protected $table = 'point_topups';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type');
    }

    public function staff()
    {
        return $this->belongsTo(AdminUser::class, 'staff_id');
    }

    public function sale()
    {
        return $this->belongsTo(AdminUser::class, 'sale_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
