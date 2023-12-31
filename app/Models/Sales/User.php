<?php

namespace App\Models\Sales;

use App\Models\Core\CustomerType;
use App\Models\Core\Source;
use App\Models\Facility\Branch;
use App\Models\Financial\PointTopup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type');
    }

    public function pointTopups()
    {
        return $this->hasMany(PointTopup::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->phone_number;
    }

	protected $hidden = ['access_token', 'password'];

	protected $guarded = [];
}
