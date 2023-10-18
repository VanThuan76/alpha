<?php

namespace App\Admin\Controllers;

use App\Models\Core\Position;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Core_PositionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Chức vụ';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Position());

        $grid->column('name', __('Tên'));
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();

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
        $show = new Show(Position::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Position());
        $form->text('name', __('Tên'));
        return $form;
    }
}
