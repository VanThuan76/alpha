<?php

namespace App\Admin\Controllers;

use App\Models\Marketing\News;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class Mkt_NewsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tin tức';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new News());

        $grid->column('title', __('Tiêu đề'));
        $grid->column('view', __('Số lượt xem'));
        $grid->column('thumbnail', __('Hình ảnh đại diện'))->image();
        $grid->column('image', __('Hình ảnh'))->image();
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('created_at', __('Ngày tạo'));
        $grid->column('updated_at', __('Ngày cập nhật'));
        $grid->fixColumns(0, 0);

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
        $show = new Show(News::findOrFail($id));

        $show->field('title', __('Tiêu đề'));
        $show->field('content', __('Nội dung'));
        $show->field('thumbnail', __('Hình ảnh đại diện'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('status', __('Trạng thái'));
        $show->field('image', __('Hình ảnh'));
        $show->field('unit_id', __('ID Đơn vị'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new News());

        $form->text('title', __('Tiêu đề'));
        $form->textarea('content', __('Nội dung'));
        $form->image('thumbnail', __('Hình ảnh đại diện'));
        $form->image('image', __('Hình ảnh'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1)->setWidth(2, 2);
        $form->hidden('unit_id', __('ID Đơn vị'))->default(Admin::user()->active_unit_id);

        return $form;
    }
}
