<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use App\Models\AdminUser;
use App\Models\Facility\Unit;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Hash;
use Encore\Admin\Controllers\AdminController;

class System_CustomSettingController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('Cài đặt người dùng');
    }
    public function display(Content $content)
    {
        $id = Admin::user()->id;

        return parent::edit($id, $content);
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = new Form(new AdminUser());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);
        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });
        $form->ignore(['password_confirmation']);
        $form->multipleSelect('roles', trans('admin.roles'))->options(app(config('admin.database.roles_model'))->all()->pluck('name', 'id'));
        $form->mobile("phone_number", "Phone number")->options(['mask' => '999 9999 9999']);
        $form->multipleSelect('units', "Cơ sở")->options(Unit::all()->pluck('name', 'id'))->default(array(Admin::user()->active_unit_id));
        $form->select('active_unit_id', "Cơ sở hoạt động")->options(Unit::all()->pluck('name', 'id'))->default(Admin::user()->active_unit_id);
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
            $tools->disableView();
            $tools->disableBackButton();
            $tools->disableListButton();
        });

        return $form;
    }
}