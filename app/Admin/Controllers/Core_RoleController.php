<?php

namespace App\Admin\Controllers;

use App\Models\Core\Role;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Core_RoleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vai trò';

    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('Quyền');
            $content->description('Phân cấp vai trò');
            $content->body(Role::tree());
        });
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Role());

        $grid->column('name', __('Tên'));
        $grid->column('slug', __('Slug'));
        $grid->column('created_at', __('Ngày tạo'));
        $grid->column('updated_at', __('Ngày cập nhật'));
        $grid->column('parent_id', __('Parent id'));
        $grid->column('order', __('Order'));

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
        $show = new Show(Role::findOrFail($id));

        $show->field('name', __('Tên'));
        $show->field('slug', __('Slug'));
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('parent_id', __('Parent id'));
        $show->field('order', __('Order'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Role());

        $form->text('name', __('Tên'));
        $form->text('slug', __('Slug'));
        $form->number('parent_id', __('Parent id'));
        $form->number('order', __('Order'));

        return $form;
    }
}
