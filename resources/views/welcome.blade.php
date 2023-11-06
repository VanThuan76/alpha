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
                overflow-y: hidden;
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                min-height: 100vh;
                margin: 0;
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
                margin-top: 10px;
                background-image: url('{{ asset('image/logo-senbachdiep.jpg') }}');
                background-size: cover;
                background-position: center;
                height: 500px;
                color: white;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .img-introduction {
                background-image: url('{{ asset('image/logo-senbachdiep-2.jpg') }}');
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
            <div class="content">
                <div class="banner"></div>
                <div class="row d-flex align-items-center">
                    <div class="col-6">
                        <div class="img-introduction"></div>
                    </div>
                    <div class="col-6">
                        <h3>
                            Sống Khỏe Sống Trọn (Chuyên mục giới thiệu tổng quan dịch vụ)
                            <br/>
                            Trải nghiệm phong cách sống khỏe sống trọn
                        </h3>
                    </div>
                </div>
                <div>
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
                <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-2 px-2 px-xl-5">
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
</html>
