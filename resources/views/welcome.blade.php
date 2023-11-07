<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trang chủ</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                min-height: 100vh;
                margin: 0;
                overflow-x: hidden
            }

            .full-height {
                min-height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .banner {
                background-color: blanchedalmond;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .img-introduction {
                background-image: url('{{ asset('image/logo-senbachdiep.jpg') }}');
                background-size: cover;
                background-position: center;
                height: 250px;
                color: white;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        </style>
    </head>
    <body>
        <div class="position-ref full-height">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                  <a class="navbar-brand" href="/">Sen Bách Diệp</a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                      <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Trang chủ</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Giới thiệu</a>
                      </li>
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Dịch vụ
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Bảng giá</a></li>
                          <li><a class="dropdown-item" href="#">Khuyến mại</a></li>
                        </ul>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Tin tức</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Liên hệ</a>
                      </li>
                    </ul>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Ngôn ngữ
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#english">Tiếng Anh</a></li>
                          <li><a class="dropdown-item" href="#vietnamese">Tiếng Việt</a></li>
                        </ul>
                    </div>
                  </div>
                </div>
            </nav>
            <div class="content">
                <div class="banner">
                    <img src="{{ asset('image/logo-senbachdiep-2.jpg') }}" class="img-fluid" alt="Banner" style="height: 50vh">
                </div>
                <div class="row d-flex align-items-center">
                    <div class="col-6">
                        <img src="{{ asset('image/logo-senbachdiep.jpg') }}" class="img-fluid" alt="Image Introduction" style="height: 50vh">
                    </div>
                    <div class="col-6">
                        <h3>
                            <strong>Sống Khỏe Sống Trọn</strong> 
                            <br/>
                            (Chuyên mục giới thiệu tổng quan dịch vụ)
                            <br/>
                            <br/>
                            Trải nghiệm phong cách sống khỏe sống trọn
                        </h3>
                    </div>
                </div>
                <div class="m-5">
                    <h2>DANH MỤC DỊCH VỤ NỔI BẬT</h2>
                    <div class="links">
                        <a href="#">Dịch vụ 1</a>
                        <a href="#">Dịch vụ 2</a>
                        <a href="#">Dịch vụ 3</a>
                        <a href="#">Dịch vụ 4</a>
                        <a href="#">Dịch vụ 5</a>
                        <a href="#">Dịch vụ 6</a>
                    </div>
                </div>
                <h2 class="m-5">VỀ CHÚNG TÔI</h2>
                <h2 class="m-5">TIN TỨC</h2>
                <h2 class="m-5">THÀNH TỰU</h2>
                <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-2 px-2 px-xl-5" style="background-color: blanchedalmond;">
                    <div class="mb-3 mb-md-0">
                      Copyright © 2023. {{ config('admin.name') }}
                    </div>
                    <div class="mb-3 mb-md-0">
                      Powered by <a href="#" target="_blank">Metaverse Vietnam</a>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</html>
