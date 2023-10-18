<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\AdminUser;
use App\Models\Core\Source;
use App\Models\Crm\ProspectCustomer;
use App\Models\Crm\Msg;
use App\Models\Product\Service;
use App\Models\Sales\Bill;
use App\Models\Sales\User;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use App\Admin\Actions\Customer\SaleAssign;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Crm_ProspectCustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Khách hàng tiềm năng';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected $phone_number;
    protected function grid()
    {
        $grid = new Grid(new ProspectCustomer());

        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('address', __('Địa chỉ'))->filter('like');
        $grid->column('sale.name', __('Nhân viên bán hàng'))->filter('like');
        $grid->column('phone_number', __('Số điện thoại'))->filter('like');
        $grid->column('telcom', __('Nhà mạng'))->filter('like');
        $grid->column('source.name', __('Nguồn'))->filter('like');
        $grid->column('call', __('Cuộc gọi'))->filter('like');
        $grid->column('sale_note', __('Ghi chú bán hàng'))->filter('like')->editable('textarea');
        $grid->column('service_id', __('Dịch vụ'))->display(function ($service_id) {
            $user = DatabaseHelper::getRecordByField(User::class, 'phone_number', $this->phone_number);
            if ($user) {
                $bill = DatabaseHelper::getRecordByField(Bill::class, 'user_id', $user->id);
                if ($bill) {
                    $services = "Ngày mua: $bill->created_at <br/>";
                    foreach ($bill->service_id as $service => $count) {
                        $services = DatabaseHelper::getValueByField(Service::class, $service, 'name') . "<br/>";
                    }
                    return $services;
                }
            }
        });
        $grid->column('id', __('Tin nhắn Facebook'))->display(function ($id) {
            $msg = DatabaseHelper::getRecordByField(Msg::class, 'phone_number', $this->phone_number);
            if ($msg) {
                $messages = json_decode($msg->txt, true);
                $messagesTxt = "";
                foreach ($messages as $time => $message) {
                    $messagesTxt .= "$time : $message<br/>";
                }
                return $messagesTxt;
            }
        });
        $grid->column('next_appointment', __('Cuộc hẹn tiếp theo'))->filter('date')->editable('date');
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->filter(Constant::STATUS);
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new SaleAssign());
        });
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
        $show = new Show(ProspectCustomer::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('address', __('Địa chỉ'));
        $show->field('sale', __('Nhân viên bán hàng'));
        $show->field('status', __('Trạng thái'));
        $show->field('phone_number', __('Số điện thoại'));
        $show->field('telcom', __('Nhà mạng'));
        $show->field('source', __('Nguồn'));
        $show->field('call', __('Cuộc gọi'));
        $show->field('sale_note', __('Ghi chú bán hàng'));
        $show->field('next_appointment', __('Cuộc hẹn tiếp theo'));
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
        $form = new Form(new ProspectCustomer());
        $optionSales = DatabaseHelper::getOptionsForSelect(AdminUser::class, 'name', 'id', [['active_branch_id', '=', Admin::user()->active_branch_id]]);
        $optionSources = DatabaseHelper::getOptionsForSelect(Source::class, 'name', 'id', []);

        $form->text('name', __('Tên'))->required();
        $form->text('address', __('Địa chỉ'));
        $form->select('sale_id', __('Nhân viên bán hàng'))
            ->options($optionSales)
            ->required()
            ->setWidth(2, 2);
        $form->text('phone_number', __('Số điện thoại'))->required();
        $form->text('telcom', __('Nhà mạng'));
        $form->select('source_id', __('Nguồn'))
            ->options($optionSources)
            ->required();
        $form->number('call', __('Cuộc gọi'));
        $form->textarea('sale_note', __('Ghi chú bán hàng'));
        $form->date('next_appointment', __('Cuộc hẹn tiếp theo'));
        $form->number('status', __('Trạng thái'))->default(1);
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
            $form->address = ucfirst($form->address);
        });

        return $form;
    }
}
