<?php

namespace App\Admin\Controllers;

use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;
use App\Models\AdminUser;
use App\Models\Facility\Branch;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Hash;
use Encore\Admin\Controllers\UserController;

class System_CustomUserController extends UserController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.administrator');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdminUser());

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('branchs', "Chi nhánh")->display(function ($branchs) {
            if (is_array($branchs) && count($branchs) > 0) {
                $branchName = "";
                foreach ($branchs as $i => $branch) {
                    $branchModel = Branch::find($branch);
                    $branchName .= $branchModel ? $branchModel->name . " , " : "";
                }
                return "<span style='color:blue'>$branchName</span>";
            } else {
                return "";
            }
        });
        $grid->column('activeBranch.name', 'Cở sở hoạt động');
        $grid->column('created_at', trans('admin.created_at'))->vndate();
        $grid->column('updated_at', trans('admin.updated_at'))->vndate();
        $grid->model()->orderBy('id', 'desc');
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });
        $grid->fixColumns(0, 0);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(AdminUser::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
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

        $form->display('id', 'ID');
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
        $form->multipleSelect('branchs', "Chi nhánh")->options(Branch::all()->pluck('name', 'id'))->default(array(Admin::user()->active_branch_id));
        $form->select('active_branch_id', "Chi nhánh hoạt động")->options(Branch::all()->pluck('name', 'id'))->default(Admin::user()->active_branch_id);

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}