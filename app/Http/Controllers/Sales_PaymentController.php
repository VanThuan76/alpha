<?php

namespace App\Http\Controllers;

use App\Models\Operation\TicketOrder;
use Illuminate\Http\Request;

class Sales_PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $requestData = $request->all();

        $ticketOrders = [];
        foreach ($requestData as $data) {
            $ticketOrder = new TicketOrder([
                'sales_code' => $data['sales_code'],
                'branch_id' => $data['branch_id'],
                'customer_code' => $data['customer_code'],
                'customer_name' => $data['customer_name'],
                'service_code' => $data['service_code'],
                'date' => $data['date'],
                'start_at' => $data['start_at'],
                'to_at' => $data['to_at'],
                'status' => $data['status'],
            ]);
            $ticketOrders[] = $ticketOrder;
        }

        // Lưu các bản ghi vào cơ sở dữ liệu
        foreach ($ticketOrders as $ticketOrder) {
            $ticketOrder->save();
        }

        // Redirect hoặc trả về view cần thiết sau khi thực hiện hành động thanh toán
    }
}

