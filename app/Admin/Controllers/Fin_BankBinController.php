<?php

namespace App\Admin\Controllers;

use App\Models\Financial\BankBin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Fin_BankBinController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Mã ngân hàng';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BankBin());

        $grid->column('name', __('Tên'));
        $grid->column('bin', __('Bin'));
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);

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
        $show = new Show(BankBin::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('bin', __('Bin'));
        $show->field('created_at', __('Ngày tạo'))->vndate();
        $show->field('updated_at', __('Ngày cập nhật'))->vndate();
        $show->field('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BankBin());

        $form->text('name', __('Tên'));
        $form->text('bin', __('Bin'));
        $form->number('status', __('Trạng thái'))->default(1);

        return $form;
    }
}
