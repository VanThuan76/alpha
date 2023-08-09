<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\Unit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BranchController extends AdminController
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

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('address', __('Address'));
        $grid->column('unit.name', __('Unit id'));
        $grid->column('status', __('Status'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Created at'))->vndate();
        $grid->column('updated_at', __('Updated at'))->vndate();
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

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('address', __('Address'));
        $show->field('unit.name', __('Unit id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));
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

        $form->text('name', __('Name'))->required();
        $form->text('address', __('Address'))->required();
        $form->select('unit_id', __('Unit id'))->options(Unit::pluck('name', 'id'))->required();
        $form->select('status', __('Status'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
