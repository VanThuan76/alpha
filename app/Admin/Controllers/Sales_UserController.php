<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Core\Source;
use App\Models\Crm\Customer;
use App\Models\Sales\User;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Hash;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Sales_UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Khách hàng';
    protected $point, $accumulated_amount;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('email', __('Email'))->filter('like');
        $grid->column('id_number', __('Mã số'))->filter('like');
        $grid->column('dob', __('Ngày sinh'));
        $grid->column('sex', __('Giới tính'))->using(Constant::SEX);
        $grid->column('phone_number', __('Số điện thoại'))->filter('like');
        $grid->column('source.name', __('Tên nguồn'))->filter('like');
        $grid->column('photo', __('Hình ảnh'))->image();
        $grid->column('unit.name', __('Tên cơ sở'));
        $grid->column('customerType.name', __('Loại khách'));
        $grid->column('point', __('Điểm'))->modal('Lịch sử nạp điểm', function ($model) {
            $topups = $model->pointTopups()->take(10)->orderBy('id', 'DESC')->get()->map(function ($topup) {
                return [$topup['id'], number_format($topup['amount']), number_format($topup['added_amount']), number_format($topup['original_amount']), number_format($topup['next_amount']), $topup['created_at']];
            });
            return new Table(['ID', 'Số tiền nạp', 'Số tiền được cộng', 'Số điểm ban đầu', 'Số điểm sau khi thêm', 'release time'], $topups->toArray());
        })->display(function ($title) {
            return str_replace($this->point, number_format($this->point), str_replace('<i class="fa fa-clone"></i>', '', $title));
        });
        $grid->column('accumulated_amount', __('Số tiền tích luỹ'))->expand(function ($model) {
            $topups = $model->pointTopups()->take(10)->orderBy('id', 'DESC')->get()->map(function ($topup) {
                return [$topup['id'], number_format($topup['amount']), number_format($topup['added_amount']), number_format($topup['original_amount']), number_format($topup['next_amount']), $topup['created_at']];
            });
            return new Table(['ID', 'Số tiền nạp', 'Số tiền được cộng', 'Số điểm ban đầu', 'Số điểm sau khi thêm', 'release time'], $topups->toArray());
        })->display(function ($title) {
            return str_replace($this->accumulated_amount, number_format($this->accumulated_amount), str_replace('<i class="fa fa-clone"></i>', '', $title));
        });
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        //ToDo: Customize DatabaseHelper
        $grid->model()->where('unit_id', '=', Admin::user()->active_unit_id)->orderBy('id', 'desc');
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
        $show = new Show(User::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Xác nhận Email lúc'));
        $show->field('password', __('Mật khẩu'));
        $show->field('remember_token', __('Token nhớ mật khẩu'));
        $show->field('created_at', __('Ngày tạo'))->vndate();
        $show->field('updated_at', __('Ngày cập nhật'))->vndate();
        $show->field('id_number', __('Số CMND/CCCD'));
        $show->field('dob', __('Ngày sinh'))->vndate();
        $show->field('sex', __('Giới tính'));
        $show->field('phone_number', __('Số điện thoại'));
        $show->field('status', __('Trạng thái'));
        $show->field('point', __('Điểm'));
        $show->field('source_id', __('Nguồn'));
        $show->field('photo', __('Ảnh'));
        $show->field('unit_id', __('Đơn vị'))->label(['class' => 'label-success']);
        $show->field('customer_type', __('Loại khách hàng'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());
        $optionSources = DatabaseHelper::getOptionsForSelect(Source::class, 'name', 'id', []);

        $form->text('name', __('Tên'));
        $form->email('email', __('Email'))
            ->creationRules(['required', 'unique:users'])
            ->updateRules(['required', 'unique:users,email,{{id}}']);
        $form->text('id_number', __('Số CMND/CCCD'));
        $form->file('photo', __('Ảnh'));
        $form->date('dob', __('Ngày sinh'))->default(date('d-m-Y'));
        $form->select('sex', __('Giới tính'))->options(Constant::SEX)->default(2)->setWidth(2, 2);
        $form->text('phone_number', __('Số điện thoại'));
        $form->password('password', __('Mật khẩu'));
        $form->hidden('status', __('Trạng thái'))->default(1);
        $form->hidden('point', __('Điểm'))->default(0);
        $form->select('source_id', __('Nguồn'))->options($optionSources)->required();
        $form->hidden('unit_id', __('Đơn vị'))->default(Admin::user()->active_unit_id);
        $form->hidden('customer_type', __('Loại khách hàng'))->default(0);
        // callback after save
        $form->saved(function (Form $form) {
            if ($form->model()->phone_number) {
                $customer = DatabaseHelper::getRecordByField(Customer::class, 'phone_number', $form->model()->phone_number);
                if (is_null($customer)) {
                    $customer = new Customer();
                    $customer->name = $form->model()->name;
                    $customer->phone_number = $form->model()->phone_number;
                    $customer->sale_id = Admin::user()->id;
                    $customer->source_id = $form->model()->source_id;
                    $customer->save();
                }
            }
        });
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });
        return $form;
    }
}
