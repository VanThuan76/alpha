<?php

namespace App\Admin\Controllers;

use App\Models\Core\Position;
use App\Models\Facility\Department;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Facility_DepartmentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Phòng ban';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Department());

        $grid->column('name', __('Tên'));
        $grid->column('positions', "Chức vụ")->display(function ($positions) {
            if (is_array($positions) && count($positions) > 0) {
                $positionName = "";
                foreach ($positions as $i => $position) {
                    $positionModel = Position::find($position);
                    $positionName .= $positionModel ? $positionModel->name . " , " : "";
                }
                return "<span style='color:blue'>$positionName</span>";
            } else {
                return "";
            }
        });
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
        $show = new Show(Department::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('address', __('Địa chỉ'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));
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
        $form = new Form(new Department());

        $form->text('name', __('Tên'))->required();
        $form->multipleSelect('positions', "Chức vụ")->options(Position::all()->pluck('name', 'id'));
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
            $form->address = ucfirst($form->address);
        });
        return $form;
    }
}
