<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Service;

class SalesDetail extends Model
{
    protected $table = 'sales_detail';

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    protected $hidden = [
    ];

    protected $guarded = [];
}
