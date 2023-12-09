<?php

namespace App\Admin\Controllers;

use App\Models\Product\Service;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\View;
use Encore\Admin\Grid;

class Sales_OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Đặt hàng';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function index(Content $content)
    {
        $services = Service::all();
        $html = View::make('admin.sales_order', compact('services'));
        return $content
            ->title($this->title())
            ->description("Bán hàng")
            ->body($html->render());

    }
}
