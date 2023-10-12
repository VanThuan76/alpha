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
    protected $title = 'ReceiverAccount';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReceiverAccount());

        $grid->column('id', __('Id'));
        $grid->column('unit.name', __('Unit id'));
        $grid->column('name', __('Name'));
        $grid->column('bank_name', __('Bank name'));
        $grid->column('account_number', __('Account number'));
        $grid->column('status', __('Status'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Created at'))->vndate();
        $grid->column('updated_at', __('Updated at'))->vndate();
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

        $show->field('id', __('Id'));
        $show->field('unit_id', __('Unit id'));
        $show->field('name', __('Name'));
        $show->field('bank_name', __('Bank name'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));
        $show->field('account_number', __('Account number'));

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
        $form->select('unit_id', __('Unit id'))->options(Unit::pluck('name', 'id'))->required();
        $form->text('name', __('Name'))->required();
        $form->select('bank_name', __('Bank name'))->options(BankBin::pluck('name', 'bin'))->required();
        $form->text('account_number', __('Account number'))->required();
        $form->select('status', __('Status'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);

        return $form;
    }
}
