<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Tổng quan</h3>
    </div>
    <div class="box-body">
        <div>
            <strong>Mã mua vé:</strong>
            <span>{{ $code }}</span>
        </div>
        <div>
            <strong>Số lượng dịch vụ:</strong>
            <span>{{ $serviceQuantity }}</span>
        </div>
        {{-- <div>
            <strong>Tên chi nhánh:</strong> 
            <span>{{ $branch->branch_name }}</span>
        </div>
        <div>
            <strong>Tên lịch học:</strong> 
            <span>{{ $schedule->name }}</span>
        </div>
        <div>
            <strong>Tên bài giảng:</strong>
            <span>{{ $report['lesson_name'] }}</span>
        </div>
        <div>
            <strong>Tên bài tập:</strong>
            <span>{{ $report['home_work'] }}</span>
        </div>
        <div>
            <strong>Bình luận chung:</strong>
            <span>{{ $report['general_comment'] }}</span>
        </div> --}}
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