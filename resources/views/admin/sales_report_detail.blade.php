<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Thanh toán</h3>
    </div>
    <div class="box-body">
        <div>
            <strong>Mã mua vé(đại diện):</strong>
            <span>{{ $code }}</span>
        </div>
        <div>
            <strong>Số lượng dịch vụ:</strong>
            <span>{{ $serviceQuantity }}</span>
        </div>
        <div>
            <strong>Tổng tiền:</strong>
            <span>{{ number_format($totalPrice, 0, ',', '.') }}</span>
        </div>
        <div>
            <strong>Tổng tiền giảm giá:</strong>
            <span>{{ number_format($totalDiscount, 0, ',', '.') }}</span>
        </div>
        <div>
            <strong>Thuế VAT:</strong>
            <span>{{ $vat }}</span>
        </div>
        <div>
            <strong>Số tiền thanh toán:</strong>
            <span>{{ number_format(((($totalPrice - $totalDiscount)) - ((($totalPrice - $totalDiscount) / 100)* $vat)), 0, ',', '.') }}</span>
        </div>
        <button onclick="submitPayment()">Thanh toán</button>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Đơn mua vé chi tiết</h3>
    </div>
    <div class="box-body">
        {!! $filteredGrid->render() !!}
    </div>
</div>

<script>
    function submitPayment() {
        var serviceQuantity = "{{ $serviceQuantity }}";
        var sales = JSON.parse(`{!! $sales !!}`);
        var salesDetails = JSON.parse(`{!! $salesDetails !!}`);
        var data = [];
        for (var i = 0; i < serviceQuantity; i++) {
            data.push({
                sales_code: sales.code,
                branch_id: sales.branch_id,
                customer_code: sales.customer_code,
                customer_name: sales.customer_name,
                service_code: salesDetails[i].service_code,
                date: "12/12/2023",
                start_at: "8:30",
                to_at: "10:00",
                status: 1
            });
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ route('process.payment') }}", true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Thanh toán thành công');
                } else {
                    console.error('Có lỗi xảy ra khi thanh toán');
                }
            }
        };
        xhr.send(JSON.stringify(data));
    }
</script>