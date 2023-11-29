<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Facility\Bed;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkShift;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Operation_WorkShiftController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ca làm việc';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WorkShift());

        $grid->column('date', __('Ngày'))->display(function ($date) {
            return Utils::formatDate($date);
        });
        $grid->column('bed.zone_id', __('Tên khu'))->display(function ($zoneId) {
            $zoneRecord = DatabaseHelper::getRecordByField(Zone::class, 'id', $zoneId);
            if ($zoneRecord) {
                return $zoneRecord->name;
            } else {
                return "";
            }
        });
        $grid->column('bed.room_id', __('Tên phòng'))->display(function ($roomId) {
            $roomRecord = DatabaseHelper::getRecordByField(Room::class, 'id', $roomId);
            if ($roomRecord) {
                return $roomRecord->name;
            } else {
                return "";
            }
        });
        $grid->column('bed.name', __('Tên giường'));
        $grid->column('employee.name', __('Tên nhân viên kỹ thuật'));
        $grid->column('from_at', __('Từ giờ'));
        $grid->column('to_at', __('Đến giờ'));
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('StatusWorkShift', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->model()->whereHas('bed', function ($query) {
            $query->where('branch_id', Admin::user()->active_branch_id);
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
        $show = new Show(WorkShift::findOrFail($id));

        $show->field('date', __('Ngày'))->as(function ($date) {
            return Utils::formatDate($date);
        });
        $show->field('bed.zone_id', __('Tên khu'))->as(function ($zoneId) {
            $zoneRecord = DatabaseHelper::getRecordByField(Zone::class, 'id', $zoneId);
            if ($zoneRecord) {
                return $zoneRecord->name;
            } else {
                return "";
            }
        });
        $show->field('bed.room_id', __('Tên phòng'))->as(function ($roomId) {
            $roomRecord = DatabaseHelper::getRecordByField(Room::class, 'id', $roomId);
            if ($roomRecord) {
                return $roomRecord->name;
            } else {
                return "";
            }
        });
        $show->field('bed.name', __('Tên giường'));
        $show->field('employee.name', __('Tên nhân viên kỹ thuật'));
        $show->field('from_at', __('Từ giờ'));
        $show->field('to_at', __('Đến giờ'));
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('StatusWorkShift', 'description_vi', $statusId);
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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WorkShift());
        $beds = DatabaseHelper::getOptionsForSelect(Bed::class, "name", "id", ['branch_id', Admin::user()->active_branch_id]);
        $employees = DatabaseHelper::getOptionsForSelect(Employee::class, "name", "id", ['branch_id', Admin::user()->active_branch_id]);
        $statuses = Utils::commonCodeOptionsForSelect('StatusWorkShift', 'description_vi', 'value');

        $form->date('date', __('Ngày'));
        $form->select('bed_id', __('Tên giường'))->options($beds)->required();
        $form->select('employee_id', __('Tên nhân viên kỹ thuật'))->options($employees)->required();
        $form->time('from_at', __('Từ giờ'));
        $form->time('to_at', __('Đến giờ'));
        $form->select('status', __('Trạng thái'))->options($statuses)->default(0)->required();
        return $form;
    }
}
