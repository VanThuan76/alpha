@foreach ($rooms as $room)
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Phòng: {{$room->name}}</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                <i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                <i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
        @foreach($room->beds as $bed)
            <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-12">
            <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Phòng: {{$room->name}}</span>
            <span class="info-box-number">41,410</span>
            <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
            70% Increase in 30 Days
            </span>
            </div>
            </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
            <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-calendar-check-o"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Likes</span>
            <span class="info-box-number">41,410</span>
            <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
            70% Increase in 30 Days
            </span>
            </div>
            </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
            <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-calendar-minus-o"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Events</span>
            <span class="info-box-number">41,410</span>
            <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
            70% Increase in 30 Days
            </span>
            </div>
            </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12">
            <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-comments-o"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Comments</span>
            <span class="info-box-number">41,410</span>
            <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
            70% Increase in 30 Days
            </span>
            </div>
            </div>
            </div>
            </div>
        @endforeach
        </div>
    </div>
    
@endforeach