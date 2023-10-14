<?php

namespace App\Admin\Actions;

use App\Models\Facility\Branch;
use App\Models\Operation\WorkSchedule;
use Carbon\Carbon;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Encore\Admin\Actions\RowAction;
use App\Admin\Controllers\Constant;

class UpdateThirdSchedule extends RowAction
{
    public function handle(WorkSchedule $workSchedule, Request $request)
    {
        // Switch the value of the `star` field and save
        $workSchedule->shift3 = $request->get("technician");
        $workSchedule->save();

        // return a new html to the front end after saving
        $html = $this->display($workSchedule->shift3);
        return $this->response()->html($html);
    }

    public function form()
    {
        $branch = Branch::find($this->row->branch_id);
        
        $this->select('branch', 'Chi nhánh')->options(Branch::where('status', 1)->where('id', $branch->id)->pluck('name', 'id'))->default($this->row->id)->readOnly();
        $this->text('date', 'Ngày')->default($this->row->date)->readOnly();
        if (!is_null($this->column)) {
            $this->select('shift', 'Ca')->options(Constant::SHIFT)->default($this->column->getName())->readOnly();
            $date = Carbon::parse($this->row->date);
            if($date >= Carbon::now()->startOfDay()){
                $this->multipleSelect('technician', 'Kỹ thuật viên')->options(AdminUser::where('status', 1)->where('active_branch_id', $branch->id)->pluck('name', 'id'))
                ->default($this->row[$this->column->getName()]);
            } else {
                $this->multipleSelect('technician', 'Kỹ thuật viên')->options(AdminUser::where('status', 1)->where('active_branch_id', $branch->id)->pluck('name', 'id'))
                ->default($this->row[$this->column->getName()])->readOnly();
            }
        } else {
            $this->multipleSelect('technician', 'Kỹ thuật viên')->options(AdminUser::where('status', 1)->where('active_branch_id', $branch->id)->pluck('name', 'id'));
        }

    }

    // This method displays different icons in this column based on the value of the `star` field.
    public function display($technicans)
    {
        if (!$technicans){
            return "<i class=\"fa fa-pencil\"></i>";
        }
        $html = "";
        foreach($technicans as $i => $technican){
            $html .= AdminUser::find($technican)->name . "<br/>";
        }
        return $html;
    }
}