<?php

namespace App\Admin\Controllers;

use App\Models\Customer;
use App\Models\AdminUser;
use App\Models\Source;
use App\Models\Msg;
use App\Models\Bill;
use App\Models\User;
use App\Models\Service;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use App\Admin\Actions\Customer\SaleAssign;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CustomerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Customer';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Customer());

        $grid->column('name', __('Name'))->filter('like');
        $grid->column('address', __('Address'))->filter('like');
        $grid->column('sale.name', __('Sale'))->filter('like');
        $grid->column('phone_number', __('Phone number'))->filter('like');
        $grid->column('telcom', __('Telcom'))->filter('like');
        $grid->column('source.name', __('Source'))->filter('like');
        $grid->column('call', __('Call'))->filter('like');
        $grid->column('sale_note', __('Sale note'))->filter('like')->editable('textarea');
        $grid->column('service_id', __('Dịch vụ'))->display(function($service_id){
            $user = User::where('phone_number', $this->phone_number)->first();
            if ($user){
                $bill = Bill::where("user_id", $user->id)->first();
                if ($bill){
                    $services = "Ngày mua: $bill->created_at <br/>";
                    foreach ($bill->service_id as $service=>$count){
                        $services .= Service::find($service)->name . "<br/>";
                    }
                    return $services;
                }
            }
        });
        $grid->column('id', __('Facebook msg'))->display(function($id){
            $msg = Msg::where('phone_number', $this->phone_number)->first();
            if ($msg){
                $messages = json_decode($msg->txt, true);
                $messagesTxt = "";
                foreach($messages as $time => $message){
                    $messagesTxt .= "$time : $message<br/>";
                }
                return $messagesTxt;
            }
        });
        $grid->column('next_appointment', __('Next appointment'))->filter('date')->editable('date');
        $grid->column('status', __('Status'))->using(Constant::STATUS)->filter(Constant::STATUS);
        $grid->column('created_at', __('Created at'))->vndate();
        $grid->column('updated_at', __('Updated at'))->vndate();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new SaleAssign());
        });
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
        $show = new Show(Customer::findOrFail($id));

        $show->field('name', __('Name'));
        $show->field('address', __('Address'));
        $show->field('sale', __('Sale'));
        $show->field('status', __('Status'));
        $show->field('phone_number', __('Phone number'));
        $show->field('telcom', __('Telcom'));
        $show->field('source', __('Source'));
        $show->field('call', __('Call'));
        $show->field('sale_note', __('Sale note'));
        $show->field('next_appointment', __('Next appointment'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Customer());

        $form->text('name', __('Name'))->required();
        $form->text('address', __('Address'));
        $form->select('sale_id', __('Sale'))->options(AdminUser::where('active_unit_id', '=', Admin::user()->active_unit_id)->pluck('name', 'id'))->required()->setWidth(2,2);
        $form->text('phone_number', __('Phone number'))->required();
        $form->text('telcom', __('Telcom'));
        $form->select('source_id', __('Source'))->options(Source::pluck('name', 'id'))->required();
        $form->number('call', __('Call'));
        $form->textarea('sale_note', __('Sale note'));
        $form->date('next_appointment', __('Next appointment'));
        $form->number('status', __('Status'))->default(1);
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
            $form->address = ucfirst($form->address);
        });
        return $form;
    }
}
