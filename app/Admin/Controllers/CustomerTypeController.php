<?php

namespace App\Admin\Controllers;

use App\Models\CustomerType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerTypeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Loại thành viên';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CustomerType());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('accumulated_money', __('Accumulated money'));
        $grid->column('discount', __('Discount'));
        $grid->column('order', __('Order'));

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(CustomerType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('accumulated_money', __('Accumulated money'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('discount', __('Discount'));
        $show->field('order', __('Order'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CustomerType());

        $form->text('name', __('Name'));
        $form->number('accumulated_money', __('Accumulated money'));
        $form->number('discount', __('Discount'));
        $form->number('order', __('Order'));

        return $form;
    }
}
