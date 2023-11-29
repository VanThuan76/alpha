<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\CommonCode;
use App\Models\Core\CustomerType;
use App\Models\Facility\Bed;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use App\Models\Hrm\Employee;
use App\Models\Operation\ScheduleOrder;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Carbon\Carbon;
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

        $grid->column('user_type', __('Loại khách'))->display(function ($userTypeId) {
            $userType = Utils::commonCodeFormat('User', 'description_vi', $userTypeId);
            if ($userType) {
                return $userType;
            } else {
                return "";
            }
        });
        $grid->column('user.name', __('Tên khách hàng'));
        $grid->column('user.id', __('Mã khách hàng'))->display(function ($userId) {
            return 'SBD' . $userId;
        });
        $grid->column('user.phone_number', __('Số điện thoại'));
        $grid->column('user.sex', __('Giới tính'))->display(function ($sex) {
            $sexTitle = CommonCode::where('type', 'Gender')->where('value', $sex)->first();
            return $sexTitle ? $sexTitle->description_vi : "";
        });
        $grid->column('user.dob', __('Ngày sinh'));
        $grid->column('user.customer_type', __('Hạng thành viên'))->display(function ($customerType) {
            $customerTypeRecord = DatabaseHelper::getRecordByField(CustomerType::class, 'id', $customerType);
            return $customerTypeRecord ? $customerTypeRecord->name : "";
        });
        $grid->column('service.code', __('Mã dịch vụ'));
        $grid->column('service.name', __('Tên dịch vụ'));
        $grid->column('service.duration', __('Khoảng thời gian'));
        $grid->column('date', __('Ngày đặt'))->display(function ($date) {
            return Utils::formatDate($date);
        });
        $grid->column('book_at', __('Giờ đặt'));
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
        $grid->column('employee.name', __('Tên nhân viên kỹ thuật(thêm)'))->display(function ($employee) {
            if ($employee) {
                return $employee;
            } else {
                return 'Không có';
            }
        });
        $grid->column('verify_at', __('Giờ xác nhận'));
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
        $grid->column('note', __('Lưu ý'));
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

        $show->field('user_type', __('Loại khách'))->as(function ($userTypeId) {
            $userType = Utils::commonCodeFormat('User', 'description_vi', $userTypeId);
            if ($userType) {
                return $userType;
            } else {
                return "";
            }
        });
        $show->field('user.name', __('Tên khách hàng'));
        $show->field('user.id', __('Mã khách hàng'))->as(function ($userId) {
            return 'SBD' . $userId;
        });
        $show->field('user.phone_number', __('Số điện thoại'));
        $show->field('user.sex', __('Giới tính'))->as(function ($sex) {
            $sexTitle = CommonCode::where('type', 'Gender')->where('value', $sex)->first();
            return $sexTitle ? $sexTitle->description_vi : "";
        });
        $show->field('user.dob', __('Ngày sinh'));
        $show->field('user.customer_type', __('Hạng thành viên'))->as(function ($customerType) {
            $customerTypeRecord = DatabaseHelper::getRecordByField(CustomerType::class, 'id', $customerType);
            return $customerTypeRecord ? $customerTypeRecord->name : "";
        });
        $show->field('service.code', __('Mã dịch vụ'));
        $show->field('service.name', __('Tên dịch vụ'));
        $show->field('service.duration', __('Khoảng thời gian'));
        $show->field('date', __('Ngày đặt'))->as(function ($date) {
            return Utils::formatDate($date);
        });
        $show->field('book_at', __('Giờ đặt'));
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
        $show->field('employee.name', __('Tên nhân viên kỹ thuật(thêm)'))->as(function ($employee) {
            if ($employee) {
                return $employee;
            } else {
                return 'Không có';
            }
        });
        $show->field('verify_at', __('Giờ xác nhận'));
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
        $employeesChoosen = [];
        foreach ($workShifts as $workShiftId => $bedId) {
            $bed = Bed::find($bedId);
            if ($bed) {
                if ($bed->branch_id == Admin::user()->active_branch_id) {
                    $employeeId = WorkShift::where('bed_id', $bed->id)->where("date", ">=", Carbon::today()->toDateString())->where('status', 0)->first();
                    if ($employeeId) {
                        $employeesChoosen[] = $employeeId->employee_id;
                        $employeeName = Employee::where('id', $employeeId->employee_id)->first()->name;
                        $fromAt = WorkShift::where('bed_id', $bed->id)->first()->from_at;
                        $toAt = WorkShift::where('bed_id', $bed->id)->first()->to_at;
                        $zoneName = Zone::where('id', $bed->zone_id)->first()->name;
                        $roomName = Room::where('id', $bed->room_id)->first()->name;
                        $bedNames[$workShiftId] = $employeeName . " - " . $zoneName . " - " . $roomName . " - " . $bed->name . " (Làm từ {$fromAt} đến {$toAt})";
                    }
                } else {
                    $bedNames[$workShiftId] = null;
                }
            } else {
                $bedNames[$workShiftId] = null;
            }
        }
        $uniqueBedNames = array_unique($bedNames);
        // $employeeFreeId = WorkShift::where("date", ">=", Carbon::today()->toDateString())->where('status', 0)->whereNotIn('employee_id', $employeesChoosen)->get(); //Xem lại
        $employees = DatabaseHelper::getOptionsForSelect(Employee::class, "name", "id", ['branch_id', Admin::user()->active_branch_id]);
        $statuses = Utils::commonCodeOptionsForSelect('Schedule', 'description_vi', 'value');
        $userTypes = Utils::commonCodeOptionsForSelect('User', 'description_vi', 'value');

        $form->text("code", __('Mã'))->readonly();
        $form->select('user_type', 'Tệp khách hàng')->options($userTypes)->default(0);
        $form->select('user_id', __('Tên khách hàng'))->options($users)->required();
        $form->select('service_id', __('Tên dịch vụ'))->options($filteredServices)->required();
        $form->select('work_shift_id', __('Ca làm việc'))->options($uniqueBedNames)->required();
        $form->select('employee_id', __('Tên nhân viên kỹ thuật(thêm)'))->options($employees)->disable();
        $form->date('date', __('Ngày đặt'))->required();
        $form->time('book_at', __('Giờ đặt'))->required();
        $form->time('verify_at', __('Giờ xác nhận'))->disable()->required();
        $form->select('status', __('Trạng thái'))->options($statuses)->default(0)->required();
        $form->textarea('note', __('Ghi chú'));

        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                $form->code = Utils::generateCommonCode("schedule_order", "BK");
            }
        });
        
        $form->saved(function (Form $form) {
            if ($form->isCreating()) {
                $workShiftId = $form->model()->work_shift_id;
                $workShift = WorkShift::where("id", $workShiftId)->first();
                if ($workShift) {
                    $workShift->status = 1;
                    $workShift->save();
                }
            };
        });

        $script = <<<EOT
        $(function() {
            var serviceSelect = $(".service_id");
            var employeeOther = $(".employee_id");
            var userType = $(".user_type");
            var userId = $(".user_id");
            var status = $(".status");
            var bookAt = $(".book_at");
            var verifyAt = $(".verify_at");

            serviceSelect.on('change', function() {
                var apiUrl = 'http://127.0.0.1:8000/api/web/v1/services' + '/' + $(this).val();
                $.get(apiUrl, function (service) {
                    if(service.data.staff_number > 1){
                        employeeOther.prop('disabled', false);
                    }else{
                        employeeOther.prop('disabled', true);
                    }
                });
            });


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
                } else if($(this).val() === '1'){
                    bookAt.prop('disabled', true);
                    verifyAt.prop('disabled', false);
                } else if($(this).val() === '2'){
                    bookAt.prop('disabled', true);
                    verifyAt.prop('disabled', true);
                } 
            });

        });
        EOT;
        Admin::script($script);
        return $form;
    }
}
