<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use App\Models\Operation\RoomOrder;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\AdminController;
use App\Admin\Actions\SelectRoom;
use App\Admin\Actions\SelectUsingRoom;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Operation_RoomOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Quản lý đặt phòng';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RoomOrder());

        $grid->column('zone.name', __('Zone id'));
        $grid->column('name', __('Tên phòng'));
        $grid->column('id', __('Chọn phòng'))->action(SelectRoom::class);
        $grid->column('bill_id', 'Bán gối')->action(SelectUsingRoom::class);
        $grid->column('status', 'Lịch sử dùng phòng')->expand(function ($id) {
            $orders = $this->orders()->take(10)->get()->map(function ($order) {
                return $order->only(['id', 'service', 'start_time', 'end_time']);
            });
            return new Table(['ID', 'Dịch vụ', 'Bắt đầu', 'Kết thúc'], $orders->toArray());
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->model()->whereIn('zone_id', Zone::select("id")->whereIn('branch_id', 
            Branch::select('id')->where('unit_id', Admin::user()->active_unit_id)->get()))->where('status', 1)->orderBy('zone_id', 'DESC');
        
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
        $show = new Show(RoomOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('room_id', __('Room id'));
        $show->field('user_id', __('User id'));
        $show->field('technican_id', __('Technican id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('status', __('Status'));
        $show->field('service_id', __('Service id'));
        $show->field('start_time', __('Start time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RoomOrder());

        $form->number('room_id', __('Room id'));
        $form->number('user_id', __('User id'));
        $form->number('technican_id', __('Technican id'));
        $form->number('status', __('Status'))->default(1);
        $form->text('service_id', __('Service id'));
        $form->text('start_time', __('Start time'));

        return $form;
    }
}
