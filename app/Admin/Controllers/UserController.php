<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Source;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'))->filter('like');
        $grid->column('email', __('Email'))->filter('like');
        $grid->column('id_number', __('Id number'))->filter('like');
        $grid->column('dob', __('Dob'));
        $grid->column('sex', __('Sex'))->using(Constant::SEX);
        $grid->column('phone_number', __('Phone number'))->filter('like');
        $grid->column('source.name', __('Source id'))->filter('like');
        $grid->column('photo', __('Photo'))->image();
        $grid->column('unit.name', __('Unit id'));
        $grid->column('customerType.name', __('Customer type'));
        $grid->column('point', __('Point'))->modal('Lịch sử nạp điểm', function ($model) {
            $topups = $model->pointTopups()->take(10)->orderBy('id', 'DESC')->get()->map(function ($topup) {
                return [$topup['id'], number_format($topup['amount']), number_format($topup['added_amount']), number_format($topup['original_amount']), number_format($topup['next_amount']), $topup['created_at']];
            });
            return new Table(['ID', 'Số tiền nạp', 'Số tiền được cộng', 'Số điểm ban đầu', 'Số điểm sau khi thêm', 'release time'], $topups->toArray());
        })->display(function ($title) {
            return str_replace($this->point, number_format($this->point), str_replace('<i class="fa fa-clone"></i>', '', $title));
        });
        $grid->column('accumulated_amount', __('Accumulated amount'))->expand(function ($model) {
            $topups = $model->pointTopups()->take(10)->orderBy('id', 'DESC')->get()->map(function ($topup) {
                return [$topup['id'], number_format($topup['amount']), number_format($topup['added_amount']), number_format($topup['original_amount']), number_format($topup['next_amount']), $topup['created_at']];
            });
            return new Table(['ID', 'Số tiền nạp', 'Số tiền được cộng', 'Số điểm ban đầu', 'Số điểm sau khi thêm', 'release time'], $topups->toArray());
         })->display(function ($title) {
            return str_replace($this->accumulated_amount, number_format($this->accumulated_amount), str_replace('<i class="fa fa-clone"></i>', '', $title));
        });
        $grid->column('status', __('Status'))->using(Constant::STATUS);
        $grid->column('created_at', __('Created at'))->vndate();
        $grid->column('updated_at', __('Updated at'))->vndate();
        $grid->model()->where('unit_id', '=', Admin::user()->active_unit_id)->orderBy('id', 'desc');
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

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('id_number', __('Id number'));
        $show->field('dob', __('Dob'));
        $show->field('sex', __('Sex'));
        $show->field('phone_number', __('Phone number'));
        $show->field('status', __('Status'));
        $show->field('point', __('Point'));
        $show->field('source_id', __('Source id'));
        $show->field('photo', __('Photo'));
        $show->field('unit_id', __('Unit id'));
        $show->field('customer_type', __('Customer type'));

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

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->text('id_number', __('Id number'));
        $form->file('photo', __('Photo'));
        $form->date('dob', __('Dob'))->default(date('d-m-Y'));
        $form->select('sex', __('Sex'))->options(Constant::SEX)->default(2)->setWidth(2, 2);
        $form->text('phone_number', __('Phone number'));
        $form->hidden('status', __('Status'))->default(1);
        $form->hidden('point', __('Point'))->default(0);
        $form->select('source_id', __('Source id'))->options(Source::pluck('name', 'id'))->required();
        $form->hidden('unit_id', __('Unit id'))->default(Admin::user()->active_unit_id);
        $form->hidden('customer_type', __('Customer type'))->default(0);

        return $form;
    }
}
