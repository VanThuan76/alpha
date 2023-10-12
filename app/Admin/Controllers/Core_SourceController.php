<?php

namespace App\Admin\Controllers;

use App\Models\Core\Source;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Core_SourceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nguồn khách';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Source());

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
        $show = new Show(Source::findOrFail($id));

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
        $form = new Form(new Source());

        $form->text('name', __('Tên'));

        return $form;
    }
}
