<?php

namespace App\Admin\Controllers;

use App\Admin\Helpers\DatabaseHelper;
use App\Models\Facility\Bed;
use App\Models\Facility\Zone;
use App\Models\Facility\Branch;
use App\Models\Facility\Room;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Facility_BedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Giường';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bed());

        $grid->column('branch.name', __('Chi nhánh'));
        $grid->column('zone.name', __('Khu vực'));
        $grid->column('name', __('Tên'));
        $grid->column('room.name', __('Phòng'));
        $grid->column('created_at', __('Ngày tạo'))->vndate();;
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();;
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('StatusBed', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
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
        $show = new Show(Bed::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Tên'));
        $show->field('room_id', __('ID Phòng'));
        $show->field('created_at', __('Ngày tạo'))->vndate();;
        $show->field('updated_at', __('Ngày cập nhật'))->vndate();;
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('StatusBed', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $branchs = DatabaseHelper::getOptionsForSelect(Branch::class, "name" , "id", []);
        $statuses = Utils::commonCodeOptionsForSelect('StatusBed', 'description_vi', 'value');

        $form = new Form(new Bed());
        if ($form->isEditing()) {
            $id = request()->route()->parameter('bed');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $zones = DatabaseHelper::getOptionsForSelect(Zone::class, "name" , "id", [['branch_id', '=', $branchId]]);
            $zoneId = $form->model()->find($id)->getOriginal("zone_id");
            $rooms = DatabaseHelper::getOptionsForSelect(Room::class, "name" , "id", [['zone_id', '=', $zoneId]]);
            $roomId = $form->model()->find($id)->getOriginal("room_id");
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->required();
            $form->select('zone_id', __('Tên khu vực'))->options($zones)->default($zoneId)->required();
            $form->select('room_id', __('Tên phòng'))->options($rooms)->default($roomId)->required();
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('zone_id', __('Tên khu vực'))->options()->required()->disable();
            $form->select('room_id', __('Tên phòng'))->options()->required()->disable();
        }
        $form->text('name', __('Tên'));
        $form->select('status', __('Trạng thái'))->options($statuses)->default(1)->required();

        $urlZone = env('APP_URL') . '/api/zone';
        $urlRoom = env('APP_URL') . '/api/room';
        $script = <<<EOT
        $(function() {    
            var branchSelect = $(".branch_id");
            var zoneSelect = $(".zone_id");
            var zoneSelectDOM = document.querySelector('.zone_id');
            var roomSelect = $(".room_id");
            var roomSelectDOM = document.querySelector('.room_id');

            branchSelect.on('change', function() {
                zoneSelect.empty();
                optionsZone = {};
                $("#class_name").val("")
                var selectedBranchId = $(this).val();
                if(!selectedBranchId) return
                $.get("$urlZone", { branch_id: selectedBranchId }, function (zones) {
                    zoneSelectDOM.removeAttribute('disabled');
                    var zonesActive = zones.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(zonesActive, function (index, cls) {
                        optionsZone[cls.id] = cls.name;
                    });
                    zoneSelect.empty();
                    zoneSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsZone, function (id, zoneName) {
                        zoneSelect.append($('<option>', {
                            value: id,
                            text: zoneName
                        }));
                    });
                    zoneSelect.trigger('change');
                });
            });
            zoneSelect.on('change', function() {
                roomSelect.empty();
                optionsRoom = {};
                $("#class_name").val("")
                var selectedZoneId = $(this).val();
                if(!selectedZoneId) return
                $.get("$urlRoom", { zone_id: selectedZoneId }, function (rooms) {
                    roomSelectDOM.removeAttribute('disabled');
                    var roomsActive = rooms.filter(function (cls) {
                        return cls.status === 1;
                    });                    
                    $.each(roomsActive, function (index, cls) {
                        optionsRoom[cls.id] = cls.name;
                    });
                    roomSelect.empty();
                    roomSelect.append($('<option>', {
                        value: '',
                        text: ''
                    }));
                    $.each(optionsRoom, function (id, roomName) {
                        roomSelect.append($('<option>', {
                            value: id,
                            text: roomName
                        }));
                    });
                    roomSelect.trigger('change');
                });
            });
        });
        EOT;
        Admin::script($script);
        return $form;
    }
}
