<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Zone;
use App\Models\Facility\Branch;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Facility_ZoneController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Khu vực';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Zone());

        $grid->column('name', __('Tên'));
        $grid->column('branch.name', __('Đơn vị'))->sortable();
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
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
        $show = new Show(Zone::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('branch_id', __('Mã chi nhánh'));
        $show->field('status', __('Trạng thái'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
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
        $form = new Form(new Zone());

        $form->text('name', __('Tên'))->required();
        $form->select('branch_id', __('Mã chi nhánh'))->options(Branch::pluck('name', 'id'))->required();
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
        });
        return $form;
    }
}
