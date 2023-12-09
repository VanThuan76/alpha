<?php

namespace App\Admin\Controllers;

use App\Admin\Grid\CustomEditAction;
use App\Admin\Helpers\DatabaseHelper;
use App\Models\Core\CustomerType;
use App\Models\Facility\Bed;
use App\Models\Facility\Room;
use App\Models\Facility\Zone;
use App\Models\Hrm\Employee;
use App\Models\Operation\WorkShift;
use App\Models\Product\Service;
use App\Models\Sales\Sales;
use App\Models\Sales\SalesDetail;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\View;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Route;

class Sales_SalesDetailController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Đơn mua vé chi tiết';

    /**
     * Make a grid builder.
     *
     * @param int||array $sales_id
     * @return Grid
     */
    protected function grid($sales_id = [])
    {
        $grid = new Grid(new SalesDetail());
        $grid->column('sales.user_code', __('Mã khách hàng'));
        $grid->column('sales.user_type', __('Loại khách hàng'))->display(function ($typeId) {
            return CustomerType::where('id', $typeId)->first()->name;
        });
        $grid->column('sales.customer_name', __('Tên khách hàng'));
        $grid->column('service.code', __('Mã dịch vụ'));
        $grid->column('service.name', __('Tên dịch vụ'));
        $grid->column('service.price', __('Đơn giá'));
        $grid->column('service.actual_price', __('Giá thực tính'));
        $grid->column('status', __('Trạng thái'))->display(function ($statusId) {
            $status = Utils::commonCodeFormat('Sales', 'description_vi', $statusId);
            if ($status) {
                return $status;
            } else {
                return "";
            }
        });
        $grid->column('created_at', __('Ngày tạo'))->vndate();
        $grid->column('updated_at', __('Ngày cập nhật'))->vndate();

        $grid->fixColumns(0, 0);
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->append(new CustomEditAction($actions->getKey()));
        });
        if (!empty($sales_id)) {
            $grid->model()->whereIn('sales_id', $sales_id);
        }

        // $grid->tools(function ($tools) {
        //     $tools->append('<a href="javascript:void(0);" class="btn btn-sm btn-info" id="export-word-btn">Xuất File Word</a>');
        // });
        // $urlExportWordData = 'https://business.metaverse-solution.vn/api/export-word-data';
        // $urlExportWord = 'https://business.metaverse-solution.vn/api/export-word';
        // $id = Route::current()->parameter('report_student');
        // $script = <<<EOT
        // $(document).ready(function() {
        //     var idStudentReport = $id;
        //     if (!idStudentReport) return;
            
        //     document.getElementById("export-word-btn").addEventListener("click", function() {
        //         const params = new URLSearchParams();
        //         params.append('q', idStudentReport);
        //         fetch("$urlExportWordData", {
        //             method: "POST",
        //             headers: {
        //                 "Content-Type": "application/x-www-form-urlencoded",
        //             },
        //             body: params.toString(),
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             const params = new URLSearchParams();
        //             params.append('data', JSON.stringify(data));
        
        //             fetch("$urlExportWord", {
        //                 method: "POST",
        //                 headers: {
        //                     "Content-Type": "application/x-www-form-urlencoded",
        //                 },
        //                 body: params.toString(),
        //             })
        //             .then(response => response.blob())
        //             .then(blob => {
        //                 const url = window.URL.createObjectURL(blob);
        //                 const a = document.createElement("a");
        //                 a.style.display = "none";
        //                 a.href = url;
        //                 a.download = "baocaokqhoctap.docx";
        //                 document.body.appendChild(a);
        //                 a.click();
        //                 window.URL.revokeObjectURL(url);
        //             })
        //             .catch(error => console.error("Error:", error));
        //         })
        //         .catch(error => console.error("Error:", error));
        //     });
        // });
        // EOT;
        // Admin::script($script);
        return $grid;
    }

    /**
     * Show the detail of a specific student report.
     *
     * @param int $id
     * @return Show
     */
    public function detail($id)
    {
        if (request()->is('admin/sales/report-sales-detail/*')) {
            $code = Sales::where('id', $id)->first()->code;
            $serviceQuantity = Sales::where('id', $id)->first()->service_quantity;
            $reportDetails = SalesDetail::where("sales_id", $id)->get();
            $reportDetailIds = $reportDetails->pluck('id')->toArray();
            $filteredGrid = $this->grid($reportDetailIds);
            return View::make('admin.sales_report_detail', compact('code', 'serviceQuantity', 'filteredGrid'));
        } else {

            $show = new Show(SalesDetail::findOrFail($id));
           
            $show->field('sales.user_code', __('Mã khách hàng'));
            $show->field('sales.user_type', __('Loại khách hàng'));
            $show->field('sales.customer_name', __('Tên khách hàng'));
            $show->field('service.code', __('Mã dịch vụ'));
            $show->field('service.name', __('Tên dịch vụ'));
            $show->field('service.price', __('Đơn giá'));
            $show->field('service.actual_price', __('Giá thực tính'));
            $show->field('status', __('Trạng thái'));
            $show->field('created_at', __('Ngày tạo'))->vndate();
            $show->field('updated_at', __('Ngày cập nhật'))->vndate();
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableList();
                    $tools->disableDelete();
                });;
            return $show;
        }
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $services = DatabaseHelper::getOptionsForSelect(Service::class, "name", "id", []);
        $filteredServices = [];
        foreach ($services as $serviceId => $serviceName) {
            $service = Service::find($serviceId);
            if ($service && is_array($service->branches)) {
                if (in_array(Admin::user()->active_branch_id, $service->branches)) {
                    $filteredServices[$serviceId] = $serviceName;
                }
            }
        }
        $workShifts = DatabaseHelper::getOptionsForSelect(WorkShift::class, "bed_id", "id", []);
        $bedNames = [];
        foreach ($workShifts as $workShiftId => $bedId) {
            $bed = Bed::find($bedId);
            if ($bed) {
                if ($bed->branch_id == Admin::user()->active_branch_id) {
                    $employeeId = WorkShift::where('bed_id', $bed->id)->first()->employee_id;
                    $employeeName = Employee::where('id', $employeeId)->first()->name;
                    $fromAt = WorkShift::where('bed_id', $bed->id)->first()->from_at;
                    $toAt = WorkShift::where('bed_id', $bed->id)->first()->to_at;
                    $zoneName = Zone::where('id', $bed->zone_id)->first()->name;
                    $roomName = Room::where('id', $bed->room_id)->first()->name;
                    $bedNames[$workShiftId] = $employeeName . " - " . $zoneName . " - " . $roomName . " - " . $bed->name . " (Làm từ {$fromAt} đến {$toAt})";
                } else {
                    $bedNames[$workShiftId] = null;
                }
            } else {
                $bedNames[$workShiftId] = null;
            }
        }
        $uniqueBedNames = array_unique($bedNames);
        $statuses = Utils::commonCodeOptionsForSelect('Sales', 'description_vi', 'value');

        $form = new Form(new SalesDetail());
        $form->select('service_id', __('Tên dịch vụ'))->options($filteredServices)->required();
        $form->select('work_shift_id', __('Ca làm việc'))->options($uniqueBedNames)->required();
        $form->text('total_price', __('Đơn giá'))->disable();
        $form->text('total_discount', __('Tổng tiền giảm giá'));
        $form->text('vat', __('Thuế VAT'));
        $form->text('actual_price', __('Tiền thực tính'));
        $form->select('status', __('Trạng thái'))->options($statuses)->default(4)->required();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
        });
        $form->saved(function (Form $form) {
            admin_toastr('Lưu thành công!');
            $id = request()->route()->parameter('report_sales_detail');
            $salesReportId = $form->model()->find($id)->getOriginal("sales_id");
            return redirect("/admin/sales/report-sales-detail/{$salesReportId}");
        });
        return $form;
    }
}
