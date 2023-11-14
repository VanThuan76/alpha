<?php

namespace App\Admin\Controllers;

use App\Models\CommonCode;
use App\Models\Core\CustomerType;
use App\Models\Facility\Branch;
use App\Models\Product\Service;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Prod_ServiceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Dịch vụ';
    protected $price, $company_amount;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service());

        $grid->column('branches', __('Chi nhánh'))->display(function ($branches) {
            if (is_array($branches) && count($branches) > 0) {
                $branchName = "";
                foreach ($branches as $i => $branch) {
                    $branchModel = Branch::find($branch);
                    $branchName .= $branchModel ? $branchModel->name . " , " : "";
                }
                return "<span style='color:blue'>$branchName</span>";
            } else {
                return "";
            }
        });
        $grid->column('customer_types', "Loại khách hàng")->display(function ($customerTypes) {
            if (is_array($customerTypes) && count($customerTypes) > 0) {
                $customerTypeName = "";
                foreach ($customerTypes as $i => $customerType) {
                    $customerTypeModel = CustomerType::find($customerType);
                    $customerTypeName .= $customerTypeModel ? $customerTypeModel->name . " , " : "";
                }
                return "<span style='color:blue'>$customerTypeName</span>";
            } else {
                return "";
            }
        });
        $grid->column('name', __('Tên'))->filter('like');
        $grid->column('code', __('Mã'))->filter('like');
        $grid->column('tags', "Thẻ")->display(function ($tags) {
            if (is_array($tags) && count($tags) > 0) {
                $tagName = "";
                foreach ($tags as $i => $tag) {
                    $tagModel = CommonCode::where('type', 'Service')->where('value', $tag)->first();
                    $tagName .= $tagModel ? $tagModel->description_vi . " , " : "";
                }
                return "<span style='color:blue'>$tagName</span>";
            } else {
                return "";
            }
        });
        $grid->column('introduction', __('Giới thiệu'));
        $grid->column('image', __('Hình ảnh'))->image();
        $grid->column('duration', __('Khoảng thời gian'))->filter('like');
        $grid->column('staff_number', __('Số nhân viên'));
        $grid->column('price', __('Giá'))->number()->filter('like');
        $grid->column('company_amount', __('Số tiền tính vào chi phí công ty'))->number();
        $grid->column('id', __('Số tiền tính vào chi phí hộ kinh doanh'))->display(function(){  //Calculate
            return number_format($this->price - $this->company_amount);
        });
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('Status', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();
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
        $show = new Show(Service::findOrFail($id));

        $show->field('branches', __('Chi nhánh'))->as(function ($branches) {
            $branches = explode(',', $branches);
            if (is_array($branches) && count($branches) > 0) {
                $branchName = "";
                foreach ($branches as $i => $branch) {
                    $branchModel = Branch::find($branch);
                    $branchName .= $branchModel ? $branchModel->name . " , " : "";
                }
                return $branchName;
            } else {
                return "";
            }
        });
        $show->field('customer_types', "Loại khách hàng")->as(function ($customerTypes) {
            $customerTypes = explode(',', $customerTypes);
            if (is_array($customerTypes) && count($customerTypes) > 0) {
                $customerTypeName = "";
                foreach ($customerTypes as $i => $customerType) {
                    $customerTypeModel = CustomerType::find($customerType);
                    $customerTypeName .= $customerTypeModel ? $customerTypeModel->name . " , " : "";
                }
                return $customerTypeName;
            } else {
                return "";
            }
        });        
        $show->field('name', __('Tên'));
        $show->field('code', __('Mã'));
        $show->field('tags', "Thẻ")->as(function ($tags) {
            $tags = explode(',', $tags);
            if (is_array($tags) && count($tags) > 0) {
                $tagName = "";
                foreach ($tags as $i => $tag) {
                    $tagName .= $tag . " , ";
                }
                return $tagName;
            } else {
                return "";
            }
        });
        $show->field('introduction', __('Giới thiệu'));
        $show->field('image', __('Hình ảnh'));
        $show->field('duration', __('Khoảng thời gian'));
        $show->field('staff_number', __('Số nhân viên'));
        $show->field('price', __('Giá'));
        $show->field('status', __('Trạng thái'))->as(function ($statusId) {
            $status = Utils::commonCodeFormat('Status', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
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
        $form = new Form(new Service());

        $form->multipleSelect('branches', "Chi nhánh")->options(Branch::all()->pluck('name', 'id'))->default(array(Admin::user()->active_branch_id));
        $form->multipleSelect('customer_types', "Loại khách hàng")->options(CustomerType::all()->pluck('name', 'id'));
        $form->text('name', __('Tên'));
        $form->text('code', __('Mã'));
        $form->multipleSelect('tags', "Thẻ")->options(CommonCode::where("type", "Service")->pluck('description_vi', 'value'));
        $form->text('introduction', __('Giới thiệu'));
        $form->image('image', __('Hình ảnh'));
        $form->number('duration', __('Khoảng thời gian'));
        $form->number('staff_number', __('Số nhân viên'));
        $form->currency('price', __('Giá'));
        $form->currency('company_amount', __('Số tiền tính vào chi phí công ty'));
        $form->select('status', __('Trạng thái'))->options(Constant::STATUS)->default(1);

        // callback before save
        $form->saving(function (Form $form) {
            $form->name = ucfirst($form->name);
        });
        return $form;
    }
}
