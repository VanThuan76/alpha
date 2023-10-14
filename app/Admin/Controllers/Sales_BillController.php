<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Operation\BedOrder;
use App\Models\Product\Service;
use App\Models\Sales\Bill;
use App\Models\AdminUser;
use App\Models\Sales\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class Sales_BillController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Hoá đơn';
    protected $total_amount, $id;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bill());

        $grid->column('user.name', __('Khách hàng'));
        $grid->column('service_id', __('Dịch vụ'))->display(function ($serviceIds) {
            $html = "";
            foreach ($serviceIds as $id => $count) {
                $service = DatabaseHelper::getValueByField(Service::class, $id);
                if (!is_null($service) && $count > 0) {
                    $html .= $service->name . " : " . $count . " : " . number_format($service->price * $count) . "<br/>";
                }
            }
            return ($html);
        })->width(200);
        $grid->column('seller.name', __('Người bán'));
        $grid->column('payment_method', __('Phương thức thanh toán'))->using(Constant::PAYMENT_METHOD);
        $grid->column('bill', __('Hoá đơn'))->display(function () {
            return Utils::generateQr($this->total_amount, "senbachdiep:$this->id");
        });
        $grid->column('total_amount', __('Tổng cộng'))->number();
        $grid->column('branch.name', __('Chi nhánh'));
        $grid->column('creator.name', __('Người tạo'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('id', __('In hoá đơn'))->display(function ($id) {
            return "<a class=\"fa fa-print\" href='pdf?id=" . $id . "' target='_blank'></a>";
        });
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->disableRowSelector();
        //ToDo: Customize DatabaseHelper
        $grid->model()->where('branch_id', '=', Admin::user()->active_branch_id)->orderBy('id', 'desc');
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
        $show = new Show(Bill::findOrFail($id));

        $show->field('branch_id', __('ID Chi nhánh'));
        $show->field('user_id', __('ID Người dùng'));
        $show->field('service_id', __('ID Dịch vụ'));
        $show->field('number', __('Số lượng'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));
        $show->field('payment_method', __('Phương thức thanh toán'))->using(Constant::PAYMENT_METHOD);
        $show->field('bill', __('Hóa đơn'));
        $show->field('total_amount', __('Tổng cộng'));

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
        $form = new Form(new Bill());
        $optionUsers = DatabaseHelper::getOptionsForSelect(User::class, 'name', 'id', [['branch_id', '=', Admin::user()->active_branch_id]]);
        $optionSellers = DatabaseHelper::getOptionsForSelect(AdminUser::class, 'name', 'id', [['active_branch_id', '=', Admin::user()->active_branch_id]]);

        $form->select('user_id', __('Khách hàng'))->options($optionUsers)->required()->setWidth(2, 2);
        $form->embeds('service_id', "Chọn dịch vụ", function ($form) {
            //Todo: Customize DatabaseHelper
            $services = Service::where('branch_id', '=', Admin::user()->active_branch_id)->get();
            foreach ($services as $id => $service) {
                $form->number($service->id, $service->name . " (Giá tiền: " . number_format($service->price) . ")")->default(0)->width(15, 2);
            }
        });
        $form->textarea('detail', 'Chi tiết')->disable()->readonly();
        $form->currency('total_amount', __('Tổng cộng'))->readonly();
        $form->select('payment_method', __('Phương thức thanh toán'))->options(Constant::PAYMENT_METHOD)->required()->setWidth(2, 2);
        $form->file('bill', __('Hóa đơn'));
        $form->select('seller_id', __('Người bán'))->options($optionSellers)->required()->setWidth(2, 2);
        $form->hidden('branch_id', __('ID Chi nhánh'))->default(Admin::user()->active_branch_id);
        $form->hidden('creator_id', __('ID Người tạo'))->default(Admin::user()->id);
        // callback after save
        $form->saved(function (Form $form) {
            $serviceIds = $form->service_id;
            $order = 0;
            foreach ($serviceIds as $id => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $bedOrder = new BedOrder();
                    $bedOrder->bill_id = $form->model()->id;
                    $bedOrder->user_id = $form->model()->user_id;
                    $bedOrder->service_id = $id;
                    $bedOrder->branch_id = Admin::user()->active_branch_id;
                    $bedOrder->duration = Service::find($id)->duration;
                    $order++;
                    $bedOrder->order = $order;
                    $bedOrder->save();
                }
            }
        });

        $url = env('APP_URL') . '/api/customer';
        //Todo: Customize DatabaseHelper
        $services = json_encode(Service::where('branch_id', '=', Admin::user()->active_branch_id)->get());
        $script = <<<EOT
        var services = $services;
        console.log(services);
        $(document).ready(function() {
            $("input").on("change paste keyup", function() {
                var total = 0;
                var content = '';
                services.forEach ((service) => {
                    var amount = $('.service_id_' + service.id).val();
                    content += "Gói: " + service.name + ' Số lượng: ' + amount + ' Tổng số: ' + (service.price * amount).toLocaleString('it-IT', {style : 'currency', currency : 'VND'}) + String.fromCharCode(13);
                    total +=  service.price * amount;
                })
                $('.detail').val(content);
                $('.total_amount').val(total);
            }); 
        });
        EOT;
        Admin::script($script);
        return $form;
    }
}
