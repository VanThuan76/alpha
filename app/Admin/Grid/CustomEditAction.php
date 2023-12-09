<?php

namespace App\Admin\Grid;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Facades\Admin;

class CustomEditAction extends RowAction
{
    public $name = 'Edit';
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
    return <<<SCRIPT

    $('.grid-edit-row').on('click', function () {
        var id = $(this).data('id');
        var editUrl = '/admin/sales/report-sales-detail/' + id + '/edit';
        window.location.href = editUrl;
    });

    SCRIPT;
    }

    public function render()
    {
        Admin::script($this->script());

        return "<a class='fa fa-edit btn btn-sm btn-secondary grid-edit-row' data-id='{$this->id}'>Sửa</a>";
    }
    public function __toString()
    {
        return $this->render();
    }
}
