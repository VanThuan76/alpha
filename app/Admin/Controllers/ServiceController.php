<?php

namespace App\Admin\Controllers;

use App\Models\Service;
use App\Models\Unit;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ServiceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Dịch vụ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service());
        
        $grid->column('name', __('Name'))->filter('like');
        $grid->column('code', __('Code'))->filter('like');
        $grid->column('duration', __('Duration'))->filter('like');
        $grid->column('staff_number', __('Số nhân viên'));
        $grid->column('price', __('Price'))->number()->filter('like');
        $grid->column('company_amount', __('Số tiền tính vào chi phí công ty'))->number();
        $grid->column('id', __('Số tiền tính vào chi phí hộ kinh doanh'))->display(function(){
            return number_format($this->price - $this->company_amount);
        });
        $grid->column('unit.name', __('Unit id'))->filter('like');
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
        $show = new Show(Service::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('code', __('Code'));
        $show->field('duration', __('Duration'));
        $show->field('staff_number', __('Số nhân viên'));
        $show->field('price', __('Price'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('unit_id', __('Unit id'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Service());

        $form->text('name', __('Name'));
        $form->text('code', __('Code'));
        $form->number('duration', __('Duration'));
        $form->number('staff_number', __('Số nhân viên'));
        $form->currency('price', __('Price'));
        $form->currency('company_amount', __('Số tiền tính vào chi phí công ty'));
        $form->select('status', __('Status'))->options(Constant::STATUS)->default(1);
        $form->select('unit_id', __('Unit id'))->options(Unit::pluck('name', 'id'))->required();
        // callback before save
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
        });
        return $form;
    }
}
