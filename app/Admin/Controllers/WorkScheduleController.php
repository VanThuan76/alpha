<?php

namespace App\Admin\Controllers;

use App\Models\Branch;
use App\Models\WorkSchedule;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use App\Admin\Actions\UpdateFirstSchedule;
use App\Admin\Actions\UpdateSecondSchedule;
use App\Admin\Actions\UpdateThirdSchedule;
use App\Admin\Actions\UpdateFourthSchedule;

class WorkScheduleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lịch làm việc';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WorkSchedule());

        $grid->column('id', __('Id'));
        $grid->column('branch.name', __('Branch id'));
        $grid->column('date', __('Date'))->vndate();
        $grid->column('shift1', __('Ca 1'))->action(UpdateFirstSchedule::class);
        $grid->column('shift2', __('Ca 2'))->action(UpdateSecondSchedule::class);
        $grid->column('shift3', __('Ca 3'))->action(UpdateThirdSchedule::class);
        $grid->column('shift4', __('Ca 4'))->action(UpdateFourthSchedule::class);
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });
        $grid->model()->whereIn('branch_id', Branch::select("id")->where('unit_id', Admin::user()->active_unit_id)->get())->orderBy('id', 'DESC');
        $grid->paginate(21);
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
        $show = new Show(WorkSchedule::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('branch_id', __('Branch id'));
        $show->field('shift1', __('Shift1'));
        $show->field('shift2', __('Shift2'));
        $show->field('date', __('Date'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('shift3', __('Shift3'));
        $show->field('shift4', __('Shift4'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WorkSchedule());

        $form->number('branch_id', __('Branch id'));
        $form->text('shift1', __('Shift1'));
        $form->text('shift2', __('Shift2'));
        $form->date('date', __('Date'))->default(date('Y-m-d'));
        $form->text('shift3', __('Shift3'));
        $form->text('shift4', __('Shift4'));

        return $form;
    }
}
