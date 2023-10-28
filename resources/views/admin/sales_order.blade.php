<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div style="background-color: white;">
                @include('components.search')
                <table class="table table-striped">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tên đơn hàng</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Thành tiền</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                      </tr>
                      <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                      </tr>
                      <tr>
                        <th scope="row">3</th>
                        <td colspan="2">Larry the Bird</td>
                        <td>@twitter</td>
                      </tr>
                    </tbody>
                  </table>
            </div>
            <div class="row">
                @foreach($services as $service)
                    <div class="col-md-3 mb-3">
                        <div class="card" style="width: 18rem;">
                            <img src="{{ asset('image/logo-senbachdiep.jpg') }}"
                            class="card-img-top" alt="Image Product" width="150" height="100"/>
                            <div class="card-body">
                                <h5 class="card-title">{{$service->name}}</h5>
                                <p class="card-text">{{$service->description}}</p>
                                <p class="card-text font-weight-bold text-primary">{{number_format($service->price, 2, ',', ' ' . 'VNĐ')}}</p>
                                <button class="btn btn-primary add-to-cart" 
                                data-product-id="{{ $service->id }}"
                                data-product-name="{{ $service->name }}"
                                data-product-price="{{ $service->price }}"
                                >Thêm</button>                            
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-4" style="background-color: white; height: 100vh;">
            @include('components.search')
            @include('components.search_result')
            <div style="margin-top: 10px">
                <ul class="nav nav-tabs" id="myTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1">Hoá đơn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2">Đơn hàng</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane active">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Tổng tiền hàng</p>
                            </div>
                            <div class="col-md-6">
                                <p>100000</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Giảm giá</p>
                            </div>
                            <div class="col-md-6">
                                <p>100000</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Phương thức thanh toán</p>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <div id="tab2" class="tab-pane">
                        <p>Nội dung của tab 2</p>
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
<script>
    $(document).ready(function () {
        $(".add-to-cart").on("click", function (e) {
            e.preventDefault();
            var productId = $(this).data("product-id");
            var productName = $(this).data("product-name");
            var productPrice = $(this).data("product-price");
            $.ajax({
                url: "/api/add-to-cart",
                method: "POST",
                data: {
                    product_id: productId,
                    product_name: productName,
                    product_price: productPrice
                },
                success: function (response) {
                    // Xử lý phản hồi từ API nếu cần thiết
                    alert("Sản phẩm đã được thêm vào giỏ hàng.");
                },
                error: function (error) {
                    // Xử lý lỗi nếu có
                    alert("Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.");
                }
            });
        });
    });
</script>
