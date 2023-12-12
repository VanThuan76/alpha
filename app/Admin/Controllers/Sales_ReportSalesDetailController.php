<?php

namespace App\Admin\Controllers;

class Sales_ReportSalesDetailController extends Sales_SalesDetailController
{
    protected $title = 'Danh sách đơn mua hàng chi tiết';

    public function index($id)
    {
        $filteredGrid = $this->grid($id);
        return $this->indexContent($this->title, $filteredGrid);
    }

}


