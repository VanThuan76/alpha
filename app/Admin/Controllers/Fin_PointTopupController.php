<?php

namespace App\Admin\Controllers;

use App\Models\Core\CustomerType;
use App\Models\Financial\PointTopup;
use App\Models\Sales\User;
use DB;
use App\Models\AdminUser;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Fin_PointTopupController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nạp điểm';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PointTopup());

        $grid->column('user.name', __('Người dùng'));
        $grid->column('amount', __('Số tiền'))->number();
        $grid->column('discount', __('Chiết khấu'))->percentage();
        $grid->column('added_amount', __('Số tiền thêm'))->number();
        $grid->column('original_amount', __('Số tiền gốc'))->number();
        $grid->column('next_amount', __('Số tiền kế tiếp'))->number();
        $grid->column('customerType.name', __('Loại khách hàng'));
        $grid->column('customer_accumulated_amount', __('Tổng số tiền tích lũy của khách hàng'))->number();
        $grid->column('branch.name', __('Chi nhánh'));
        $grid->column('staff.name', __('Nhân viên'));
        $grid->column('sale.name', __('Người bán'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('payment_method', __('Phương thức thanh toán'))->using(Constant::PAYMENT_METHOD);
        $grid->column('bill', __('Hóa đơn'))->image();
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->model()->where('branch_id', Admin::user()->active_branch_id)->orderBy('id', 'DESC');
        $grid->enableHotKeys();
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
        $show = new Show(PointTopup::findOrFail($id));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('user_id', __('Người dùng'));
        $show->field('amount', __('Số tiền'));
        $show->field('discount', __('Chiết khấu'));
        $show->field('added_amount', __('Số tiền thêm'));
        $show->field('original_amount', __('Số tiền gốc'));
        $show->field('next_amount', __('Số tiền kế tiếp'));
        $show->field('customer_type', __('Loại khách hàng'));
        $show->field('customer_accumulated_amount', __('Tổng số tiền tích lũy của khách hàng'));
        $show->field('branch_id', __('ID Chi nhánh'));
        $show->field('staff_id', __('Nhân viên'));
        $show->field('sale_id', __('Người bán'));
        $show->field('status', __('Trạng thái'));
        $show->field('payment_method', __('Phương thức thanh toán'));
        $show->field('bill', __('Hóa đơn'));
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
        $form = new Form(new PointTopup());
        //Todo: Customize DatabaseHelper
        $form->select('user_id', __('Người dùng'))->options(User::select(DB::raw('CONCAT(name, " - ", IFNULL(phone_number,"")) AS name, id'))
        ->where('branch_id', '=', Admin::user()->active_branch_id)->pluck('name', 'id'))->required()->setWidth(2, 2);
        $form->currency('amount', __('Số tiền'))->symbol('VND');
        $form->currency('discount', __('Chiết khấu'))->readonly()->setWidth(2, 2);
        $form->currency('added_amount', __('Số tiền thêm'))->readonly()->setWidth(2, 2);
        $form->currency('original_amount', __('Số tiền gốc'))->readonly()->setWidth(2, 2);
        $form->currency('next_amount', __('Số tiền kế tiếp'))->readonly()->setWidth(2, 2);
        $form->select('customer_type', __('Loại khách hàng'))->options(CustomerType::pluck('name', 'id'))->readonly()->setWidth(2, 2);
        $form->currency('customer_accumulated_amount', __('Tổng số tiền tích lũy của khách hàng'))->readonly()->setWidth(2, 2);
        $form->hidden('branch_id', __('Đơn vị'))->default(Admin::user()->active_branch_id);
        $form->hidden('staff_id', __('Nhân viên'))->default(Admin::user()->id);
        $form->select('sale_id', __('Người bán'))->options(AdminUser::select(DB::raw('CONCAT(name, " - ", IFNULL(phone_number,"")) AS name, id'))
            ->where('active_branch_id', '=', Admin::user()->active_branch_id)->pluck('name', 'id'))->required()->setWidth(2, 2);
        $form->hidden('status', __('Trạng thái'))->default(1);
        $form->select('payment_method', __('Phương thức thanh toán'))->options(Constant::PAYMENT_METHOD)->required()->setWidth(2, 2);
        $form->file('bill', __('Hóa đơn'));
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        // callback after save
        $form->saved(function (Form $form) {
            $pointTopup = PointTopup::find($form->model()->id);
            $user = User::find($pointTopup->user_id);
            $user->point += $pointTopup->added_amount;
            $user->save();
        });

        $url = env('APP_URL') . '/api/customer';
        $script = <<<EOT
        var userInfo = [0,0];
        $(document).on('change', ".user_id", function () {
            $.get("$url",{q : this.value}, function (data) {
                userInfo = [data[0].point, data[1].discount];
                $(".discount").val(data[1].discount);
                $(".customer_type").val(data[1].id);
                $(".customer_accumulated_amount").val(data[0].accumulated_amount);
                $(".original_amount").val(data[0].point);
            });
        });
        $(".amount").on("input", function() {
            var amount = parseInt($(this).val().replaceAll(",", ""));
            var added_amount = amount * 100 / (100 - userInfo[1]);
            $(".added_amount").val(added_amount);
            $(".next_amount").val(userInfo[0] + added_amount);
         });
        EOT;

        Admin::script($script);

        return $form;
    }
}
