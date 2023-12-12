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
    protected $title = 'Danh sách bán hàng';

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
        $grid->column('user_type', __('Tệp khách hàng'))->display(function ($userTypeId) {
            $userType = Utils::commonCodeFormat('User', 'description_vi', $userTypeId);
            if ($userType) {
                return $userType;
            } else {
                return "";
            }
        });
        $grid->column('customer_code', __('Mã khách hàng'))->display(function ($userCode) {
            if ($userCode != null) {
                return "000000" . $userCode;
            } else {
                return "";
            }
        });
        $grid->column('customer_type', __('Loại khách hàng'))->display(function ($typeId) {
            if ($typeId) {
                $customerType = CustomerType::where('id', $typeId);
                if ($customerType) {
                    return $customerType->first()->name;
                } else {
                    return "";
                }
            } else {
                return "";
            }
        });
        $grid->column('customer_name', __('Tên khách hàng'));
        $grid->column('service_quantity', __('Số lượng dịch vụ'));
        $grid->column('product_quantitiy', __('Số lượng sản phẩm'));
        $grid->column('id', __('Mua vé chi tiết'))->display($orderDetailURL);
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('Sales', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
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
        $show->field('customer_code', __('Mã khách hàng'))->as(function ($userCode) {
            if ($userCode != null) {
                return "000000" . $userCode;
            } else {
                return "";
            }
        });
        $show->field('customer_type', __('Loại khách hàng'))->as(function ($typeId) {
            if ($typeId) {
                $customerType = CustomerType::where('id', $typeId);
                if ($customerType) {
                    return $customerType->first()->name;
                } else {
                    return "";
                }
            } else {
                return "";
            }
        });
        $show->field('customer_name', __('Tên khách hàng'));
        $show->field('service_quantity', __('Số lượng dịch vụ'));
        $show->field('product_quantitiy', __('Số lượng sản phẩm'));
        $show->field('id', __('Mua vé chi tiết'))->as($orderDetailURL);
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('Sales', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
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
        $customerTypes = DatabaseHelper::getOptionsForSelect(CustomerType::class, "name", "id", []);
        $userTypes = Utils::commonCodeOptionsForSelect('User', 'description_vi', 'value');
        $statuses = Utils::commonCodeOptionsForSelect('Sales', 'description_vi', 'value');

        $form = new Form(new Sales());

        $form->hidden("branch_id", __('Chi nhánh'))->disable();
        $form->hidden("code", __('Mã hoá đơn'))->disable();
        $form->select('user_type', 'Tệp khách hàng')->options($userTypes)->default(0)->required();
        $form->text('customer_code', __('Mã khách hàng'));
        $form->select('customer_type', 'Loại khách hàng')->options($customerTypes);
        $form->text('customer_name', __('Tên khách hàng(đại diện)'));
        $form->number('service_quantity', __('Số lượng dịch vụ'));
        $form->number('product_quantitiy', __('Số lượng sản phẩm'));
        $form->text('vat', __('Thuế VAT'))->default("8")->help("Theo phần trăm %");
        $form->select('status', __('Trạng thái'))->options($statuses)->default(0)->required();

        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                $form->branch_id = Admin::user()->active_branch_id;
                $form->code = Utils::generateCommonCode("sales", "BK");
            } else {
                if ($form->status == 1) {
                    //Ticket order
                }
            }
        });

        $form->saved(function (Form $form) {
            if ($form->isCreating()) {
                $sales = $form->model();
                $serviceQuantity = $sales->service_quantity;
                for ($i = 0; $i < $serviceQuantity; $i++) {
                    $salesDetail = new SalesDetail();
                    $salesDetail->sales_id = $sales->id;
                    $salesDetail->status = $sales->status;
                    $salesDetail->save();
                }
            } else {
                $sales = $form->model();
                $this->updateSalesDetails($sales);
            }
        });
        $script = <<<EOT
        $(function() {
            var userType = $(".user_type");
            var customerCodeSelect = $(".customer_code");
            var customerTypeSelect = $(".customer_type");
            userType.on('change', function() {
                if ($(this).val() === '0') {
                    customerCodeSelect.prop('disabled', false);
                    customerTypeSelect.prop('disabled', false);
                }else{
                    customerCodeSelect.prop('disabled', true);
                    customerTypeSelect.prop('disabled', true);
                    customerCodeSelect.empty();
                    customerTypeSelect.empty();
                }
            });
        })
        EOT;
        Admin::script($script);
        return $form;
    }

    protected function updateSalesDetails($sales)
    {
        if ($sales->salesDetails) {
            $serviceQuantity = $sales->service_quantity;
            $currentDetails = $sales->salesDetails->count();

            if ($serviceQuantity > $currentDetails) {
                $newRecords = $serviceQuantity - $currentDetails;
                for ($i = 0; $i < $newRecords; $i++) {
                    $salesDetail = new SalesDetail();
                    $salesDetail->sales_id = $sales->id;
                    $salesDetail->status = $sales->status;
                    $salesDetail->save();
                }
            } elseif ($serviceQuantity < $currentDetails) {
                $deleteRecords = $currentDetails - $serviceQuantity;
                $sales->salesDetails()->orderBy('id', 'desc')->limit($deleteRecords)->delete();
            }
        }
    }
}
