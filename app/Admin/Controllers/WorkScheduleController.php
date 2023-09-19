<?php

namespace App\Admin\Controllers;

use App\Models\WorkSchedule;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Zone;
use App\Models\AdminUser;
use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\View;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WorkScheduleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Xếp lịch';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function index(Content $content)
    {
        $branches = Branch::where('unit_id', Admin::user()->active_unit_id)->pluck("id");
        $zones = Zone::whereIn('branch_id',Branch::where('unit_id', Admin::user()->active_unit_id)->pluck("id"))->where('status', 1)->orderBy('id', 'DESC')->get();
        $tab = new Tab();
        foreach($zones as $zone){
            $rooms = Room::where('zone_id', $zone->id)->get();
            $tab->add($zone->name, View::make('admin.turn_select', compact('rooms')));
        }
        return $content
        ->title($this->title())
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
        $show = new Show(WorkSchedule::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('bed_id', __('Bed id'));
        $show->field('shift1', __('Shift1'));
        $show->field('shift2', __('Shift2'));
        $show->field('date', __('Date'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('shift3', __('Shift3'));
        $show->field('start_1', __('Start 1'));
        $show->field('start_2', __('Start 2'));
        $show->field('start_3', __('Start 3'));
        $show->field('end_1', __('End 1'));
        $show->field('end_2', __('End 2'));
        $show->field('end_3', __('End 3'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WorkSchedule());

        $form->number('bed_id', __('Giường'))->readonly();
        $form->date('date', __('Ngày'))->default(date('Y-m-d'))->readonly();
        $form->select('shift1', __('Nhân viên ca 1'))->options(AdminUser::where('status', 1)->where('active_unit_id', Admin::user()->active_unit_id)->pluck('name', 'id'));
        $form->time('start_1', 'Đầu ca')->format('HH:mm:ss')->default('08:00:00');
        $form->time('end_1', 'Cuối ca')->format('HH:mm:ss')->default('12:00:00');
        $form->select('shift2', __('Nhân viên ca 2'))->options(AdminUser::where('status', 1)->where('active_unit_id', Admin::user()->active_unit_id)->pluck('name', 'id'));
        $form->time('start_2', 'Đầu ca')->format('HH:mm:ss')->default('12:00:00');
        $form->time('end_2', 'Cuối ca')->format('HH:mm:ss')->default('16:00:00');
        $form->select('shift3', __('Nhân viên ca 3'))->options(AdminUser::where('status', 1)->where('active_unit_id', Admin::user()->active_unit_id)->pluck('name', 'id'));
        $form->time('start_3', 'Đầu ca')->format('HH:mm:ss')->default('16:00:00');
        $form->time('end_3', 'Cuối ca')->format('HH:mm:ss')->default('20:00:00');
        return $form;
    }
}
