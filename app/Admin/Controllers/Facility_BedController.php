<?php

namespace App\Admin\Controllers;

use App\Models\Facility\Bed;
use App\Models\Facility\Room;
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
        $form = new Form(new Bed());

        $form->text('name', __('Tên'));
        $form->select('room_id', __('Phòng'))->options(Room::pluck('name', 'id'))->required();
        $form->number('status', __('Trạng thái'))->default(1);

        return $form;
    }
}
