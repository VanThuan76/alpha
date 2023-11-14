<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Facility\Bed;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use App\Models\Hrm\Employee;
use App\Models\Operation\ScheduleOrder;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Operation_ScheduleOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Đặt lịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ScheduleOrder());

        $grid->column('service.name', __('Tên dịch vụ'));
        $grid->column('workShift.bed_id', __('Nhân viên(chính) - khu - phòng - giường'))->display(function ($bedId) {
            $workShiftRecord = DatabaseHelper::getRecordByField(WorkShift::class, 'bed_id', $bedId);
            if($workShiftRecord){
                $employeeRecord = DatabaseHelper::getRecordByField(Employee::class, 'id', $workShiftRecord->employee_id);
            }
            $bedRecord = DatabaseHelper::getRecordByField(Bed::class, 'id', $bedId);
            if ($bedRecord) {
                $zoneRecord = DatabaseHelper::getRecordByField(Zone::class, 'id', $bedRecord->zone_id);
                $roomRecord = DatabaseHelper::getRecordByField(Room::class, 'id', $bedRecord->room_id);
                if ($zoneRecord) {
                    return $employeeRecord->name . " - " . $zoneRecord->name . " - " . $roomRecord->name . " - " . $bedRecord->name . " (Làm từ {$workShiftRecord->from_at} đến {$workShiftRecord->to_at})";
                } else {
                    return "";
                }
            } else {
                return "";
            }
        });
        $grid->column('date', __('Ngày đặt'))->display(function ($date) {
            return Utils::formatDate($date);
        });
        $grid->column('employee.name', __('Tên nhân viên kỹ thuật(thêm)'))->display(function ($employee) {
            if ($employee) {
                return $employee;
            } else {
                return 'Không có';
            }
        });
        $grid->column('from_at', __('Từ giờ'));
        $grid->column('to_at', __('Đến giờ'));
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('Schedule', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->fixColumns(0, 0);
        $grid->model()->whereHas('workShift.bed', function ($query) {
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
        $show = new Show(ScheduleOrder::findOrFail($id));

        $show->field('service.name', __('Tên dịch vụ'));
        $show->field('workShift.bed_id', __('Tên khu - phòng'))->as(function ($bedId) {
            $workShiftRecord = DatabaseHelper::getRecordByField(WorkShift::class, 'bed_id', $bedId);
            if($workShiftRecord){
                $employeeRecord = DatabaseHelper::getRecordByField(Employee::class, 'id', $workShiftRecord->employee_id);
            }
            $bedRecord = DatabaseHelper::getRecordByField(Bed::class, 'id', $bedId);
            if ($bedRecord) {
                $zoneRecord = DatabaseHelper::getRecordByField(Zone::class, 'id', $bedRecord->zone_id);
                $roomRecord = DatabaseHelper::getRecordByField(Room::class, 'id', $bedRecord->room_id);
                if ($zoneRecord) {
                    return $employeeRecord->name . " - " . $zoneRecord->name . " - " . $roomRecord->name . " - " . $bedRecord->name . " (Làm từ {$workShiftRecord->from_at} đến {$workShiftRecord->to_at})";
                } else {
                    return "";
                }
            } else {
                return "";
            }
        });
        $show->field('date', __('Ngày'))->as(function ($date) {
            return Utils::formatDate($date);
        });
        $show->field('employee.name', __('Tên nhân viên kỹ thuật(thêm)'))->as(function ($employee) {
            if ($employee) {
                return $employee;
            } else {
                return 'Không có';
            }
        });
        $show->field('from_at', __('Từ giờ'));
        $show->field('to_at', __('Đến giờ'));
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('Schedule', 'description_vi', $statusId);
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
        $form = new Form(new ScheduleOrder());

        $services = DatabaseHelper::getOptionsForSelect(Service::class, "name", "id", []);
        $filteredServices = [];
        foreach ($services as $serviceId => $serviceName) {
            $service = Service::find($serviceId);
            if ($service && is_array($service->branches)) {
                if (in_array(Admin::user()->active_branch_id, $service->branches)) {
                    $filteredServices[$serviceId] = $serviceName;
                }
            }
        }
        $workShifts = DatabaseHelper::getOptionsForSelect(WorkShift::class, "bed_id", "id", []);
        $bedNames = [];
        foreach ($workShifts as $workShiftId => $bedId) {
            $bed = Bed::find($bedId);
            if ($bed) {
                if($bed->branch_id == Admin::user()->active_branch_id){
                    $employeeId = WorkShift::where('bed_id', $bed->id)->first()->employee_id;
                    $employeeName = Employee::where('id', $employeeId)->first()->name;
                    $fromAt = WorkShift::where('bed_id', $bed->id)->first()->from_at;
                    $toAt = WorkShift::where('bed_id', $bed->id)->first()->to_at;
                    $zoneName = Zone::where('id', $bed->zone_id)->first()->name;
                    $roomName = Room::where('id', $bed->room_id)->first()->name;
                    $bedNames[$workShiftId] = $employeeName . " - " . $zoneName . " - " . $roomName . " - " . $bed->name . " (Làm từ {$fromAt} đến {$toAt})";
                }else{
                    $bedNames[$workShiftId] = null;
                }
            } else {
                $bedNames[$workShiftId] = null;
            }
        }
        $uniqueBedNames = array_unique($bedNames);
        $employees = DatabaseHelper::getOptionsForSelect(Employee::class, "name", "id", ['branch_id', Admin::user()->active_branch_id]);
        $statuses = Utils::commonCodeOptionsForSelect('Schedule', 'description_vi', 'value');

        $form->select('service_id', __('Tên dịch vụ'))->options($filteredServices)->required();
        $form->date('date', __('Ngày'));
        $form->select('work_shift_id', __('Ca làm việc'))->options($uniqueBedNames)->required();
        $form->select('employee_id', __('Tên nhân viên kỹ thuật(thêm)'))->options($employees);
        $form->time('from_at', __('Từ giờ'));
        $form->time('to_at', __('Đến giờ'));
        $form->select('status', __('Trạng thái'))->options($statuses)->required()->default(0);
        return $form;
    }
}
