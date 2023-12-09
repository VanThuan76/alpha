<?php
use App\Models\Hrm\Employee;
?>

<style>
    .flex-form {
        display: flex;
        flex-wrap: wrap;
    }

    .flex-form label,
    .flex-form input {
        flex: 1 1 calc(50% - 20px);
        margin: 5px;
    }

    @media (max-width: 768px) {

        .flex-form label,
        .flex-form input {
            flex: 1 1 calc(100% - 20px);
        }
    }

    .col-md-3 {
        flex-basis: calc(25% - 20px);
        margin-right: 20px;
        margin-bottom: 20px;
    }

    .col-md-3:last-child {
        margin-right: 0;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div style="background-color: white;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tên đơn hàng</th>
                            <th scope="col">Nhân viên phục vụ</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Giá thành</th>
                            <th scope="col">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <?php
            $employees = Employee::where('position_id', 2)
                ->orderBy('id', 'desc')
                ->get();
            ?>
            <div class="row" style="width: 80vw;">
                @foreach ($services as $service)
                    <div class="col-md-3 mb-3" style="flex: 1;">
                        <div class="card" style="width: 20rem;">
                            <img src="{{ $service->image }}" class="card-img-top" alt="Image Product" width="200"
                                height="150" style="border-radius: 25px;" />
                            <div class="card-body">
                                <h4 class="card-title">Dịch vụ: {{ $service->name }}</h4>
                                <p class="card-text">{{ $service->description }}</p>
                                <p class="card-text font-weight-bold text-primary">
                                    Giá: {{ number_format($service->price, 0, ',', '.') . ' VNĐ' }}</p>
                                <label>Nhân viên phục vụ:</label>
                                <select data-placeholder="Vui lòng chọn" multiple name="employees[]"
                                    class="chosen-select employees-select form-select" style="width: 18rem;">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee }}" data-product-id="{{ $service->id }}">
                                            {{ $employee->name }}</option>
                                    @endforeach
                                </select>

                                <button style="margin-top: 10px;" class="btn btn-primary add-to-cart"
                                    data-product-id="{{ $service->id }}" data-product-name="{{ $service->name }}"
                                    data-product-price="{{ $service->price }}">Thêm</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        <div class="col-md-4" style="background-color: white; height: 100vh;">
            <div style="margin: 10px 0;">
                <ul class="nav nav-tabs" id="myTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1">Hoá đơn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2">Đơn hàng</a>
                    </li>
                </ul>
                <div class="tab-content" style="margin: 20px 0;">
                    <div id="tab1" class="tab-pane active">
                        <p>
                            <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button"
                                aria-expanded="false" aria-controls="collapseExample">
                                Khách hàng chưa đặt lịch
                            </a>
                            <button class="btn btn-primary" type="button" data-toggle="collapse"
                                data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                Khách hàng đã đặt lịch
                            </button>
                        </p>
                        <form class="flex-form">
                            <div class="collapse" id="collapseExample">
                                <label>Mã lịch hẹn: </label>
                                <input name="schedule_code" />
                            </div>
                            <label>Loại khách hàng: </label>
                            <input name="customer_type" />
                            <label>Mã khách hàng: </label>
                            <input name="customer_code" />
                            <label>Tên khách hàng: </label>
                            <input name="customer_name" />
                            <label>Số lượng dịch vụ: </label>
                            <input name="services_count" />
                            <label>Số lượng sản phẩm: </label>
                            <input name="products_count" />
                            <label>Sản phẩm: </label>
                            <label>Nhân viên phục vụ: </label>
                        </form>
                    </div>
                    <div id="tab2" class="tab-pane">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Tổng tiền hàng:</p>
                            </div>
                            <div class="col-md-6">
                                <p class="actual-price"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Giảm giá:</p>
                            </div>
                            <div class="col-md-6">
                                <p class="discount-price"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>VAT:</p>
                            </div>
                            <div class="col-md-6">
                                <p class="vat"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Phương thức thanh toán</p>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" aria-label="Default select example">
                                    <option value="1">Tiền mặt</option>
                                    <option value="2">Chuyển khoản</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <button class="col-md-6">In</button>
                            <button class="col-md-6">Thanh toán</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            var re = /(-?\d+)(\d{3})/;
            while (re.test(s[0])) {
                s[0] = s[0].replace(re, '$1' + sep + '$2');
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
        $(".chosen-select").chosen({
            no_results_text: "Oops, nothing found!"
        })
        $(".add-to-cart").on("click", function(e) {
            e.preventDefault();
            var productId = $(this).data("product-id");
            var productName = $(this).data("product-name");
            var productPrice = $(this).data("product-price");

            var selectedEmployees = [];
            $(this).closest('.card-body').find('select[name="employees[]"] option:selected').each(
                function() {
                    selectedEmployees.push($(this).text());
                });
            var existingRow = $(`table tbody tr[data-product-id="${productId}"]`);
            if (existingRow.length > 0) {
                var quantityCell = existingRow.find('.quantity');
                var currentQuantity = parseInt(quantityCell.text());
                quantityCell.text(currentQuantity + 1);
                var totalPriceCell = existingRow.find('.total-price');
                var currentTotalPrice = parseFloat(totalPriceCell.text());
                currentTotalPrice += parseFloat(productPrice);
                totalPriceCell.text(currentTotalPrice);
            } else {
                var employeeNames = selectedEmployees.join(', ');
                var newRow = `
            <tr data-product-id="${productId}">
                <th scope="row">${productId}</th>
                <td>${productName}</td>
                <td>${employeeNames}</td>
                <td class="quantity">1</td>
                <td class="price">${number_format(productPrice, 0, ',', '.') + ' VNĐ'}</td>
                <td class="total-price">${productPrice}</td>
            </tr>
        `;
                $("table tbody").append(newRow);
            }
            var total = 0;
            $('.total-price').each(function() {
                var price = parseFloat($(this).text().replace(/[^\d.-]/g, ''));
                total += price;
            });
            $('.actual-price').text(number_format(total, 0, ',', '.') + ' VNĐ');
        });
    });
</script>
