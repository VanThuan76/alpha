<?php

namespace App\Admin\Controllers;

use App\Models\Msg;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MsgController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Msg';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Msg());

        $grid->column('id', __('Id'));
        $grid->column('first_name', __('First name'));
        $grid->column('last_name', __('Last name'));
        $grid->column('phone_number', __('Phone number'));
        $grid->column('txt', __('Txt'))->display(function($txt){
            $messages = json_decode($txt, true);
            $messagesTxt = "";
            foreach($messages as $time => $message){
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

        $show->field('id', __('Id'));
        $show->field('txt', __('Txt'))->display(function($txt){
            $messages = json_encode($txt, true);
            $messagesTxt = "";
            foreach($messages as $time => $message){
                $messagesTxt .= "$time : $message\n";
            }
            //return $messagesTxt;
        });
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('user_id', __('User id'));
        $show->field('first_name', __('First name'));
        $show->field('last_name', __('Last name'));
        $show->field('info', __('Info'));
        $show->field('phone_number', __('Phone number'));

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

        $form->text('txt', __('Txt'));
        $form->text('user_id', __('User id'));
        $form->text('first_name', __('First name'));
        $form->text('last_name', __('Last name'));
        $form->text('info', __('Info'));
        $form->text('phone_number', __('Phone number'));

        return $form;
    }
}
