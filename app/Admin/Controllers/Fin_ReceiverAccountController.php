<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Unit;
use App\Models\Financial\BankBin;
use App\Models\Financial\ReceiverAccount;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Fin_ReceiverAccountController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tài khoản';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReceiverAccount());

        $grid->column('unit.name', __('Đơn vị'))->sortable();
        $grid->column('name', __('Tên'));
        $grid->column('bank_name', __('Tên ngân hàng'));
        $grid->column('account_number', __('Số tài khoản'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
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
        $show = new Show(ReceiverAccount::findOrFail($id));

        $show->field('unit_id', __('Đơn vị'));
        $show->field('name', __('Tên'));
        $show->field('bank_name', __('Tên ngân hàng'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));
        $show->field('account_number', __('Số tài khoản'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReceiverAccount());
        
        $form->select('unit_id', __('Đơn vị'))->options(Unit::pluck('name', 'id'))->required();
        $form->text('name', __('Tên'))->required();
        $form->select('bank_name', __('Tên ngân hàng'))->options(BankBin::pluck('name', 'bin'))->required();
        $form->text('account_number', __('Số tài khoản'))->required();
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        
        return $form;
    }
}
