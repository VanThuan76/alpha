<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Branch;
use App\Models\Product\Service;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Prod_ServiceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Dịch vụ';
    protected $price, $company_amount;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service());
        
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('code', __('Mã'))->filter('like');
        $grid->column('image', __('Hình ảnh'))->image();
        $grid->column('duration', __('Khoảng thời gian'))->filter('like');
        $grid->column('staff_number', __('Số nhân viên'));
        $grid->column('price', __('Giá'))->number()->filter('like');
        $grid->column('company_amount', __('Số tiền tính vào chi phí công ty'))->number();
        $grid->column('id', __('Số tiền tính vào chi phí hộ kinh doanh'))->display(function(){
            return number_format($this->price - $this->company_amount);
        });
        $grid->column('branch.name', __('Tên chi nhánh'))->filter('like');
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->fixColumns(0, 0);

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

        $show->field('branch_id', __('ID chi nhánh'));
        $show->field('name', __('Tên'));
        $show->field('code', __('Mã'));
        $show->field('image', __('Hình ảnh'));
        $show->field('duration', __('Khoảng thời gian'));
        $show->field('staff_number', __('Số nhân viên'));
        $show->field('price', __('Giá'));
        $show->field('status', __('Trạng thái'));
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
        $form = new Form(new Service());

        $form->text('name', __('Tên'));
        $form->text('code', __('Mã'));
        $form->image('image', __('Hình ảnh'));
        $form->number('duration', __('Khoảng thời gian'));
        $form->number('staff_number', __('Số nhân viên'));
        $form->currency('price', __('Giá'));
        $form->currency('company_amount', __('Số tiền tính vào chi phí công ty'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1);
        $form->select('branch_id', __('Chi nhánh'))->options(Branch::pluck('name', 'id'))->required();
        // callback before save
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
        });
        return $form;
    }
}
