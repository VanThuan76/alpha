<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Core\CustomerType;
use App\Models\Sales\Sales;
use App\Models\Sales\SalesDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;


class Sales_SalesController extends AdminController
{

    /***
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Danh sách đơn mua vé';

    /***
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $orderDetailURL = function ($value) {
            if (!$value)
                return;
            return "<a href='http://127.0.0.1:8000/admin/sales/report-sales-detail/$value' style='text-decoration: underline'>Mua vé chi tiết</a>";
        };

        $grid = new Grid(new Sales());
        $grid->column('code', __('Mã hoá đơn'));
        $grid->column('user_code', __('Mã khách hàng'))->display(function ($userCode) {
            return "000000" . $userCode;
        });
        $grid->column('user_type', __('Loại khách hàng'))->display(function ($typeId) {
            return CustomerType::where('id', $typeId)->first()->name;
        });
        $grid->column('customer_name', __('Tên khách hàng'));
        $grid->column('service_quantity', __('Số lượng dịch vụ'));
        $grid->column('product_quantitiy', __('Số lượng sản phẩm'));
        $grid->column('id', __('Mua vé chi tiết'))->display($orderDetailURL);
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->model()->where('branch_id', '=', Admin::user()->active_branch_id);
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
        $orderDetailURL = function ($value) {
            if (!$value)
                return;
            return "<a href='http://127.0.0.1:8000/admin/order/order-detail/$value' style='text-decoration: underline'>Mua vé chi tiết</a>";
        };
        $show = new Show(Sales::findOrFail($id));

        $show->field('code', __('Mã hoá đơn'));
        $show->field('user_code', __('Mã khách hàng'))->as(function ($userCode) {
            return "000000" . $userCode;
        });
        $show->field('user_type', __('Loại khách hàng'))->as(function ($typeId) {
            return CustomerType::where('id', $typeId)->first()->name;
        });
        $show->field('customer_name', __('Tên khách hàng'));
        $show->field('service_quantity', __('Số lượng dịch vụ'));
        $show->field('product_quantitiy', __('Số lượng sản phẩm'));
        $show->field('id', __('Mua vé chi tiết'))->as($orderDetailURL);
        $show->field('created_at', __('Ngày tạo'))->vndate();
        $show->field('updated_at', __('Ngày cập nhật'))->vndate();
        return $show;
    }

    /**
     * Make a form builder
     *
     * @return Form
     */
    protected function form()
    {
        $userTypes = DatabaseHelper::getOptionsForSelect(CustomerType::class, "name", "id", []);

        $form = new Form(new Sales());

        $form->text("branch_id", __('Chi nhánh'))->disable();
        $form->text("code", __('Mã hoá đơn'))->disable();
        $form->text('user_code', __('Mã khách hàng'));
        $form->select('user_type', __('Trạng thái'))->options($userTypes);
        $form->text('customer_name', __('Tên khách hàng(đại diện)'));
        $form->number('service_quantity', __('Số lượng dịch vụ'));
        $form->number('product_quantitiy', __('Số lượng sản phẩm'));

        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                $form->branch_id = Admin::user()->active_branch_id;
                $form->code = Utils::generateCommonCode("sales", "BK");
            }
        });

        $form->saved(function (Form $form) {
            $sales = $form->model();
            $serviceQuantity = $sales->service_quantity;
            for ($i = 0; $i < $serviceQuantity; $i++) {
                $salesDetail = new SalesDetail();
                $salesDetail->sales_id = $sales->id;
                $salesDetail->save();
            }
        });
        return $form;
    }
}
