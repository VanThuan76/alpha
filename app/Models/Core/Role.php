<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Role extends Model
{
    use ModelTree, AdminBuilder;

    protected $table = 'admin_roles';


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('name');
    }

	protected $hidden = [
    ];

	protected $guarded = [];
}
