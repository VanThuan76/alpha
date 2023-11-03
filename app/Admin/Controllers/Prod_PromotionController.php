<?php

namespace App\Admin\Controllers;

use App\Models\CommonCode;
use App\Models\Core\CustomerType;
use App\Models\Facility\Branch;
use App\Models\Product\Promotion;
use App\Models\Product\Service;
use App\Models\Sales\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Prod_PromotionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Khuyến mãi';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Promotion());

        $grid->column('id', __('Mã khuyến mại'));
        $grid->column('branches', "Chi nhánh")->display(function ($branchs) {
            return Utils::renderStringToGrid($branchs, Branch::class);
        });
        $grid->column('ranks', "Xếp hạng")->display(function ($ranks) {
            return Utils::renderStringToGrid($ranks, CustomerType::class);
        });
        $grid->column('users', "Người dùng")->display(function ($users) {
            return Utils::renderStringToGrid($users, User::class);
        });
        $grid->column('services', "Dịch vụ")->display(function ($services) {
            return Utils::renderStringToGrid($services, Service::class);
        });
        // $grid->column('products', "Chi nhánh")->display(function ($tags) {
        //     return Utils::renderStringToGrid($tags, CommonCode::class);
        // });
        // $grid->column('tags', "Chi nhánh")->display(function ($tags) {
        //     return Utils::renderStringToGrid($tags, CommonCode::class);
        // });
        $grid->column('title', __('Tiêu đề'));
        $grid->column('image_url', __('Hình ảnh'))->image();
        $grid->column('details', __('Chi tiết'));
        $grid->column('used_precent', __('Phần trăm đã sử dụng khuyến mại'));
        $grid->column('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $grid->column('start_date', __('Ngày bắt đầu'))->vndate();
        $grid->column('end_date', __('Ngày kết thúc'))->vndate();
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
        $show = new Show(Promotion::findOrFail($id));

        $show->field('id', __('Mã khuyến mại'));
        $show->field('branches', "Chi nhánh")->as(function ($branchs) {
            return Utils::renderStringToGrid($branchs, Branch::class);
        });
        $show->field('ranks', "Xếp hạng")->as(function ($ranks) {
            return Utils::renderStringToGrid($ranks, CustomerType::class);
        });
        $show->field('users', "Người dùng")->as(function ($users) {
            return Utils::renderStringToGrid($users, User::class);
        });
        $show->field('services', "Dịch vụ")->as(function ($services) {
            return Utils::renderStringToGrid($services, Service::class);
        });
        // $show->field('tags', "Chi nhánh")->as(function ($tags) {
        //     return Utils::renderStringToGrid($tags, CommonCode::class);
        // });
        $show->field('title', __('Tiêu đề'));
        $show->field('image_url', __('Hình ảnh'))->image();
        $show->field('details', __('Chi tiết'));
        $show->field('used_precent', __('Phần trăm đã sử dụng khuyến mại'));
        $show->field('status', __('Trạng thái'))->using(Constant::STATUS)->label(Constant::STATUS_LABEL);
        $show->field('start_date', __('Ngày bắt đầu'))->vndate();
        $show->field('end_date', __('Ngày kết thúc'))->vndate();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Promotion());
        $tranferId = Utils::generatePromoCode("PROMO");

        $form->multipleSelect('branches', "Chi nhánh")->options(Branch::all()->pluck('name', 'id'));
        $form->multipleSelect('ranks', "Xếp hạng")->options(CustomerType::all()->pluck('name', 'id'));
        $form->multipleSelect('users', "Người dùng")->options(User::all()->pluck('name', 'id'));
        $form->multipleSelect('services', "Dịch vụ")->options(Service::all()->pluck('name', 'id'));
        // $form->multipleSelect('tags', "Nhãn gán")->options(CommonCode::all()->pluck('name', 'id'));
        $form->text('title', __('Tiêu đề'));
        $form->image('image_url', __('Hình ảnh'));
        $form->textarea('details', __('Chi tiết'));
        $form->number('used_precent', __('Phần trăm đã sử dụng khuyến mại'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1);
        $form->date('start_date', __('Ngày bắt đầu'));
        $form->date('end_date', __('Ngày kết thúc'));
        return $form;
    }
}
