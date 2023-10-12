<?php

namespace App\Models\Crm;

use App\Models\AdminUser;
use App\Models\Core\Source;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public function sale()
    {
        return $this->belongsTo(AdminUser::class, 'sale_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
