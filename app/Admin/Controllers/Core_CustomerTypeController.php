<?php

namespace App\Admin\Controllers;

use App\Models\Core\CustomerType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Core_CustomerTypeController extends AdminController
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

        $grid->column('name', __('Tên'));
        $grid->column('accumulated_money', __('Tiền tích lũy'));
        $grid->column('discount', __('Giảm giá'));
        $grid->column('order', __('Đơn đặt hàng'));
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();

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

        $show->field('name', __('Tên'));
        $show->field('accumulated_money', __('Tiền tích lũy'));
        $show->field('discount', __('Giảm giá'));
        $show->field('order', __('Đơn đặt hàng'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));

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

        $form->text('name', __('Tên'));
        $form->number('accumulated_money', __('Tiền tích lũy'));
        $form->number('discount', __('Giảm giá'));
        $form->number('order', __('Đơn đặt hàng'));

        return $form;
    }
}
