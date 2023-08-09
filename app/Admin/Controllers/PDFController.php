<?php
namespace App\Admin\Controllers;

use App\Models\Bill;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;
 
class PDFController extends Controller
{
   private $fpdf;
 
    public function __construct()
    {
         
    }
 
    public function createPDF(Request $request)
    {
        $id = $request->input('id');
        $bill = Bill::find($id);
        $this->fpdf = new \tFPDF();
        $this->fpdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $this->fpdf->SetFont('DejaVu','',10);
        $this->fpdf->AddPage("P", ['200', '130']);
        $this->fpdf->Text(10, 10, "Hoá đơn" ); 
        $this->fpdf->Text(10, 15, "Khách hàng: Dương Mạnh Cường" );   
        $count = 0;
        foreach( $bill->service_id as $id=>$amount){
            if ($amount > 0){
                $count ++;
                $text = "Dịch vụ: " . Service::find($id)->name. " Số lượng: " . $amount . " Giá tiền: " . number_format(Service::find($id)->price);
                $this->fpdf->Text(10, 15 + $count * 5, $text); 
            }
        }
        $this->fpdf->Text(10, 20 + $count * 5, "Tổng số tiền: " . number_format($bill->total_amount)); 
        $this->fpdf->Output();
        exit;
    }
}