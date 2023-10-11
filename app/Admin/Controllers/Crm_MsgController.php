<?php

namespace App\Admin\Controllers;

use App\Models\Crm\Msg;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Crm_MsgController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tin nhắn facebook';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Msg());

        $grid->column('first_name', __('Họ tên'));
        $grid->column('last_name', __('Tên'));
        $grid->column('phone_number', __('Số điện thoại'));
        $grid->column('txt', __('Nội dung tin nhắn'))->display(function ($txt) {
            $messages = json_decode($txt, true);
            $messagesTxt = "";
            foreach ($messages as $time => $message) {
                $messagesTxt .= "$time : $message<br/>";
            }
            return $messagesTxt;
        });
        $grid->column('created_at', trans('admin.created_at'))->vndate();
        $grid->column('updated_at', trans('admin.updated_at'))->vndate();
        $grid->model()->orderBy('id', 'desc');
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();
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
        $show = new Show(Msg::findOrFail($id));

        $show->field('txt', __('Nội dung tin nhắn'))->display(function ($txt) {
            $messages = json_decode($txt, true);
            $messagesTxt = "";
            foreach ($messages as $time => $message) {
                $messagesTxt .= "$time : $message<br/>";
            }
            return $messagesTxt;
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));
        $show->field('user_id', __('ID Người dùng'));
        $show->field('first_name', __('Họ tên'));
        $show->field('last_name', __('Tên'));
        $show->field('info', __('Thông tin'));
        $show->field('phone_number', __('Số điện thoại'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Msg());

        $form->text('txt', __('Nội dung tin nhắn'));
        $form->text('user_id', __('ID Người dùng'));
        $form->text('first_name', __('Họ tên'));
        $form->text('last_name', __('Tên'));
        $form->text('info', __('Thông tin'));
        $form->text('phone_number', __('Số điện thoại'));

        return $form;
    }
}
