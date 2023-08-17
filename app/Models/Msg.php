<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Msg extends Model
{
    protected $table = 'msgs';

    /*public function getTxtAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    public function setTxtAttribute($value)
    {
        $this->attributes['txt'] = json_encode($value);
    }
*/
	protected $hidden = [
    ];

	protected $guarded = [];
}
