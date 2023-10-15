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
        $grid->column('created_at', __('Ngày tạo'));
        $grid->column('updated_at', __('Ngày cập nhật'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);

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
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));

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

        $form = new Form(new Bed());
        if ($form->isEditing()) {
            $id = request()->route()->parameter('bed');
            $branchId = $form->model()->find($id)->getOriginal("branch_id");
            $zones = DatabaseHelper::getOptionsForSelect(Zone::class, "name" , "id", [['branch_id', '=', $branchId]]);
            $zoneId = $form->model()->find($id)->getOriginal("zone_id");
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->default($branchId)->required();
            $form->select('zone_id', __('Tên khu vực'))->options($zones)->default($zoneId)->required();
        }else{
            $form->select('branch_id', __('Tên chi nhánh'))->options($branchs)->required();
            $form->select('zone_id', __('Tên khu vực'))->options()->required()->disable();
        }
        $form->text('name', __('Tên'));
        $form->select('room_id', __('Phòng'))->options(Room::pluck('name', 'id'))->required();
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);

        $urlZone = env('APP_URL') . '/api/zone';
        $script = <<<EOT
        $(function() {    
            var branchSelect = $(".branch_id");
            var zoneSelect = $(".zone_id");
            var zoneSelectDOM = document.querySelector('.zone_id');

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
        });
        EOT;
        Admin::script($script);
        return $form;
    }
}
