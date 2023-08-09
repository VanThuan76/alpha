<?php

namespace App\Admin\Controllers;

use App\Models\Bill;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Service;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use DB;

class BillController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Bill';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bill());

        $grid->column('id', __('Id'));
        $grid->column('user.name', __('Khách hàng'));
        $grid->column('service_id', __('Dịch vụ'))->display(function($serviceIds){
            $html = "";
            foreach($serviceIds as $id => $count){
                $service = Service::find($id);
                if (!is_null($service) && $count > 0){
                    $html .= $service->name . " : " . $count . " : " . number_format($service->price * $count) ."<br/>";
                }
            }
            return ($html);
        })->width(200);
        $grid->column('seller.name', __('Người bán'));
        $grid->column('payment_method', __('Payment method'))->using(Constant::PAYMENT_METHOD);
        $grid->column('bill', __('Bill'))->image();
        $grid->column('total_amount', __('Total amount'))->number();
        $grid->column('unit.name', __('Unit id'));
        $grid->column('creator.name', __('Người tạo'));
        $grid->column('status', __('Status'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('id', __('In hoá đơn'))->display(function ($id) {
            return "<a class=\"fa fa-print\" href='pdf?id=".$id."' target='_blank'></a>";
        });
        $grid->column('created_at', __('Created at'))->vndate();
        $grid->column('updated_at', __('Updated at'))->vndate();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->disableRowSelector();

        $grid->model()->where('unit_id', Admin::user()->active_unit_id)->orderBy('id', 'DESC');
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

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        //$show->field('service_id', __('Service id'));
        $show->field('number', __('Number'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));
        $show->field('payment_method', __('Payment method'))->using(Constant::PAYMENT_METHOD);
        $show->field('bill', __('Bill'));
        $show->field('total_amount', __('Total amount'));
        $show->field('unit_id', __('Unit id'));
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

        $form->select('user_id', __('Khách hàng'))->options(User::select(DB::raw('CONCAT(name, " - ", IFNULL(phone_number,"")) AS name, id'))
        ->where('unit_id', '=', Admin::user()->active_unit_id)->pluck('name', 'id'))->required()->setWidth(2,2);
        $form->embeds('service_id', "Chọn dịch vụ", function ($form) {
            $services = Service::where('unit_id', '=', Admin::user()->active_unit_id)->get();
            foreach($services as $id=>$service){
                $form->number($service->id, $service->name . " (Giá tiền: " . number_format($service->price) . ")")->default(0)->width(15,2);
            }
        });

        $form->textarea('detail', 'Chi tiết')->disable()->readonly();
        $form->currency('total_amount', __('Total amount'))->readonly();
        $form->select('payment_method', __('Payment method'))->options(Constant::PAYMENT_METHOD)->required()->setWidth(2,2);
        $form->file('bill', __('Bill'));
        $form->select('seller_id', __('Người bán'))->options(AdminUser::select(DB::raw('CONCAT(name, " - ", IFNULL(phone_number,"")) AS name, id'))
        ->where('active_unit_id', '=', Admin::user()->active_unit_id)->pluck('name', 'id'))->required()->setWidth(2,2);
        $form->hidden('unit_id', __('Unit id'))->default(Admin::user()->active_unit_id);
        $form->hidden('creator_id', __('Unit id'))->default(Admin::user()->id);

        $url = env('APP_URL') . '/api/customer';
        $services = json_encode(Service::where('unit_id', '=', Admin::user()->active_unit_id)->get());
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
