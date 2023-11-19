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
use App\Models\Sales\User;
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

        $grid->column('user.name', __('Tên khách hàng'));
        $grid->column('user_type', __('Loại khách'))->display(function ($userTypeId) {
            $userType = Utils::commonCodeFormat('User', 'description_vi', $userTypeId);
            if ($userType) {
                return $userType;
            } else {
                return "";
            }
        });
        $grid->column('service.name', __('Tên dịch vụ'));
        $grid->column('workShift.bed_id', __('Nhân viên(chính) - khu - phòng - giường'))->display(function ($bedId) {
            $workShiftRecord = DatabaseHelper::getRecordByField(WorkShift::class, 'bed_id', $bedId);
            if ($workShiftRecord) {
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
        $grid->column('book_at', __('Giờ đặt'));
        $grid->column('verify_at', __('Giờ xác nhận'));
        $grid->column('start_at', __('Giờ bắt đầu'));
        $grid->column('to_at', __('Giờ kết thúc'));
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('Schedule', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $grid->column('channel', __('Kênh'))->display(function ($channelId) {
            $channel = Utils::commonCodeFormat('Channel', 'description_vi', $channelId);
            if ($channel) {
                return $channel;
            } else {
                return "";
            }
        });
        $grid->column('note', __('Ghi chú'));
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

        $show->field('user.name', __('Tên khách hàng'));
        $show->field('user_type', __('Loại khách'))->as(function ($userTypeId) {
            $userType = Utils::commonCodeFormat('User', 'description_vi', $userTypeId);
            if ($userType) {
                return $userType;
            } else {
                return "";
            }
        });
        $show->field('service.name', __('Tên dịch vụ'));
        $show->field('workShift.bed_id', __('Tên khu - phòng'))->as(function ($bedId) {
            $workShiftRecord = DatabaseHelper::getRecordByField(WorkShift::class, 'bed_id', $bedId);
            if ($workShiftRecord) {
                $employeeRecord = DatabaseHelper::getRecordByField(Employee::class, 'id', $workShiftRecord->employee_id);
            }
            $bedRecord = DatabaseHelper::getRecordByField(Bed::class, 'id', $bedId);
            if ($bedRecord) {
                $zoneRecord = DatabaseHelper::getRecordByField(Zone::class, 'id', $bedRecord->zone_id);
                $roomRecord = DatabaseHelper::getRecordByField(Room::class, 'id', $bedRecord->room_id);
                if ($zoneRecord) {
                    return $employeeRecord->name . " - " . $zoneRecord->name . " - " . $roomRecord->name . " - " . $bedRecord->name . " (Làm từ {$workShiftRecord->book_at} đến {$workShiftRecord->to_at})";
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
        $show->field('book_at', __('Giờ đặt'));
        $show->field('book_at', __('Giờ đặt'));
        $show->field('verify_at', __('Giờ xác nhận'));
        $show->field('start_at', __('Giờ bắt đầu'));
        $show->field('to_at', __('Giờ kết thúc'));
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('Schedule', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $show->field('channel', __('Kênh'))->as(function ($channelId) {
            $channel = Utils::commonCodeFormat('Channel', 'description_vi', $channelId);
            if ($channel) {
                return $channel;
            } else {
                return "";
            }
        });
        $show->field('note', __('Ghi chú'));
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

        $users = DatabaseHelper::getOptionsForSelect(User::class, "name", "id", []);
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
                if ($bed->branch_id == Admin::user()->active_branch_id) {
                    $employeeId = WorkShift::where('bed_id', $bed->id)->first()->employee_id;
                    $employeeName = Employee::where('id', $employeeId)->first()->name;
                    $fromAt = WorkShift::where('bed_id', $bed->id)->first()->from_at;
                    $toAt = WorkShift::where('bed_id', $bed->id)->first()->to_at;
                    $zoneName = Zone::where('id', $bed->zone_id)->first()->name;
                    $roomName = Room::where('id', $bed->room_id)->first()->name;
                    $bedNames[$workShiftId] = $employeeName . " - " . $zoneName . " - " . $roomName . " - " . $bed->name . " (Làm từ {$fromAt} đến {$toAt})";
                } else {
                    $bedNames[$workShiftId] = null;
                }
            } else {
                $bedNames[$workShiftId] = null;
            }
        }
        $uniqueBedNames = array_unique($bedNames);
        $employees = DatabaseHelper::getOptionsForSelect(Employee::class, "name", "id", ['branch_id', Admin::user()->active_branch_id]);
        $statuses = Utils::commonCodeOptionsForSelect('Schedule', 'description_vi', 'value');
        $userTypes = Utils::commonCodeOptionsForSelect('User', 'description_vi', 'value');

        $form->select('user_type', 'Tệp khách hàng')->options($userTypes)->default(0);
        $form->select('user_id', __('Tên khách hàng'))->options($users)->required();
        $form->select('service_id', __('Tên dịch vụ'))->options($filteredServices)->required();
        $form->date('date', __('Ngày'))->required();
        $form->select('work_shift_id', __('Ca làm việc'))->options($uniqueBedNames)->required();
        $form->select('employee_id', __('Tên nhân viên kỹ thuật(thêm)'))->options($employees);
        $form->time('book_at', __('Giờ đặt'))->disable()->required();
        $form->time('verify_at', __('Giờ xác nhận'))->disable()->required();
        $form->time('start_at', __('Giờ bắt đầu'))->disable()->required();
        $form->time('to_at', __('Giờ kết thúc'))->required();
        $form->select('status', __('Trạng thái'))->options($statuses)->default(4)->required();
        $form->textarea('note', __('Ghi chú'));

        $script = <<<EOT
        $(function() {
            var userType = $(".user_type");
            var userId = $(".user_id");
            var status = $(".status");
            var bookAt = $(".book_at");
            var verifyAt = $(".verify_at");
            var startAt = $(".start_at");

            userType.on('change', function() {
                if ($(this).val() === '0') {
                    userId.prop('disabled', false);
                } else {
                    userId.prop('disabled', true);
                }
            });
            
            status.on('change', function() {
                if ($(this).val() === '0') {
                    bookAt.prop('disabled', false);
                    verifyAt.prop('disabled', true);
                    startAt.prop('disabled', true);
                } else if($(this).val() === '1'){
                    bookAt.prop('disabled', true);
                    verifyAt.prop('disabled', false);
                    startAt.prop('disabled', true);
                } else if($(this).val() === '2'){
                    bookAt.prop('disabled', true);
                    verifyAt.prop('disabled', true);
                    startAt.prop('disabled', false);
                } 
            });

        });
        EOT;
        Admin::script($script);
        return $form;
    }
}
