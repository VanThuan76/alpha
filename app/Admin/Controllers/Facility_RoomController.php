<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Facility_RoomController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Phòng';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Room());

        $grid->column('name', __('Tên'));
        $grid->column('zone.name', __('Khu vực'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
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
        $show = new Show(Room::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('zone_id', __('Id Khu vực'));
        $show->field('status', __('Trạng thái'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
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
        $form = new Form(new Room());

        $form->select('zone_id', __('Khu vực'))->options(Zone::pluck('name', 'id'))->required();
        $form->text('name', __('Tên'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
        });
        return $form;
    }
}
