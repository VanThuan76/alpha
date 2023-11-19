<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Core\Position;
use App\Models\Facility\Branch;
use App\Models\Hrm\Employee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Hrm_EmployeeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nhân sự';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Employee());

        $grid->column('branch.name', __('Chi nhánh'));
        $grid->column('code', __('Mã'))->filter('like');
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('gender', __('Giới tính'))->using(Constant::SEX)->filter(Constant::SEX);
        $grid->column('date_of_birth', __('Ngày sinh'));
        $grid->column('phone_number', __('Số điện thoại'))->filter('like');
        $grid->column('email', __('Email'))->filter('like');
        $grid->column('address', __('Địa chỉ'))->filter('like');
        $grid->column('avatar', __('Ảnh đại diện'))->image();
        $grid->column('position_id', __('Vị trí'))->display(function ($positionId) {
            $positionRecord = DatabaseHelper::getRecordByField(Position::class, 'id', $positionId);
            return $positionRecord ? $positionRecord->name : "";
        });
        $grid->column('level', __('Hạng'))->filter('like');
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->filter(Constant::STATUS);
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
        $show = new Show(Employee::findOrFail($id));

        $show->field('branch.name', __('Chi nhánh'));
        $show->field('code', __('Mã'));
        $show->field('name', __('Tên'));
        $show->field('gender', __('Giới tính'));
        $show->field('date_of_birth', __('Ngày sinh'));
        $show->field('phone_number', __('Số điện thoại'));
        $show->field('email', __('Email'));
        $show->field('address', __('Địa chỉ'));
        $show->field('avatar', __('Ảnh đại diện'))->image();
        $show->field('position_id', __('Vị trí'))->as(function ($positionId) {
            $positionRecord = DatabaseHelper::getRecordByField(Position::class, 'id', $positionId);
            return $positionRecord ? $positionRecord->name : "";
        });
        $show->field('level', __('Hạng'));
        $show->field('status', __('Trạng thái'))->using(Constant::STATUS)->filter(Constant::STATUS);
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
        $form = new Form(new Employee());

        $branchs = DatabaseHelper::getOptionsForSelect(Branch::class, "name" , "id", []);
        $positions = DatabaseHelper::getOptionsForSelect(Position::class, "name", "id", []);
        $tranferId = Utils::generateEmployeeCode("HRM");
        $genderOptions = Constant::SEX;

        $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
        $form->text("code", __('Mã'))->default($tranferId)->readonly();
        $form->text('name', __('Tên'));
        $form->select('gender', __('Giới tính'))->options($genderOptions)->required();
        $form->date('date_of_birth', __('Ngày sinh'));
        $form->text('phone_number', __('Số điện thoại'));
        $form->text('email', __('Email'));
        $form->text('address', __('Địa chỉ'));
        $form->image('avatar', __('Ảnh đại diện'));
        $form->select('position_id', __('Vị trí'))->options($positions);
        $form->number('level', __('Hạng'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);

        return $form;
    }
}
