<?php

namespace App\Admin\Controllers;

use App\Models\Bed;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Zone;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\View;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Bed';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bed());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('room.name', __('Room name'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('status', __('Status'));

        return $grid;
    }

    public function selectBed(Content $content)
    {
        $branches = Branch::where('unit_id', Admin::user()->active_unit_id)->pluck("id");
        $zones = Zone::whereIn('branch_id',Branch::where('unit_id', Admin::user()->active_unit_id)->pluck("id"))->where('status', 1)->orderBy('id', 'DESC')->get();
        $tab = new Tab();
        foreach($zones as $zone){
            $rooms = Room::where('zone_id', $zone->id)->get();
            $tab->add($zone->name, View::make('admin.bed_select', compact('rooms')));
        }
        $url = env('APP_URL') . '/api';
        $script = <<<EOT
        $('#unlockModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var bedId = button.data('bedid') // Extract info from data-* attributes
            $('.bed-id').val(bedId);
        });
        $('#lockModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var bedId = button.data('bedid'); // Extract info from data-* attributes
            $('.bed-id').val(bedId);
        });
        $('.btn-select-bed').on('click', function(e) {
            var bedId = $(this).data('bedid'); // Extract info from data-* attributes
            $.ajax({
                type: "POST",
                url: "$url/bed/show",
                data: {'bed_id': bedId},
                success: function(response) {
                    $('#bedSelect').find('.modal-body').html(response);
                    $('#bedSelect').modal('show');
                },
                error: function() {
                    alert('Error');
                }
            });
        });
        $('.tag-form-submit').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "$url/bed/status",
                data: {'bed_id': $('.bed-id').val()},
                success: function(response) {
                    $('#unlockModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('Error');
                }
            });
            return false;
        });
        $('#bedSelect').on('change', '#customer-id', function() {
        //$('#customer-id').on('click', function() {
            var userId = $(this).find('option:selected').val();
            $.ajax({
                type: "POST",
                url: "$url/bed/getOrder",
                data: {'userId': userId},
                success: function(response) {
                    $('#bedSelect').find('.modal-body').html(response);
                    $('#bedSelect').modal('show');
                },
                error: function() {
                    alert('Error');
                }
            });
        });
        $('.select-form-submit').on('click', function(e) {
            e.preventDefault();
            var formData = $('#select-form').serializeArray();
            var data = {};
            $.each(formData, function(i, v) {
                data[v.name] = v.value;
            });
            if (data['staff_1'] == data['staff_2'] || data['staff_1'] == data['staff_2'] || data['staff_1'] == data['staff_2']) {
                $.admin.toastr.error('Nhân viên chọn trùng tên!', '', {positionClass:"toast-top-center"}); 
                return;
            }
            $.ajax({
                type: "POST",
                url: "$url/bed/select",
                data: $('#select-form').serialize(),
                success: function(response) {
                    $('#bedSelect').modal('hide');
                    //location.reload();
                },
                error: function() {
                    alert('Error');
                }
            });
            return false;
        });
        EOT;
        Admin::script($script);
        return $content
        ->title("Chọn giường")
        ->description("Chọn giường")
        ->body($tab->render());
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
        $show->field('name', __('Name'));
        $show->field('room_id', __('Room id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Bed());

        $form->text('name', __('Name'));
        $form->select('room_id', __('Room id'))->options(Room::pluck('name', 'id'))->required();
        $form->number('status', __('Status'))->default(1);

        return $form;
    }
}
