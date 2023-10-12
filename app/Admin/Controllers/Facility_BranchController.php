<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Branch;
use App\Models\Facility\Unit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Facility_BranchController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chi nhánh';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Branch());

        $grid->column('name', __('Tên'));
        $grid->column('address', __('Địa chỉ'));
        $grid->column('unit.name', __('Đơn vị'))->sortable();
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL)->sortable();
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Branch::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('address', __('Địa chỉ'));
        $show->field('unit.name', __('Đơn vị'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));
        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Branch());

        $form->text('name', __('Tên'))->required();
        $form->text('address', __('Địa chỉ'))->required();
        $form->select('unit_id', __('Unit id'))->options(Unit::pluck('name', 'id'))->required();
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
            $form->address = ucfirst($form->address);
        });
        return $form;
    }
}
