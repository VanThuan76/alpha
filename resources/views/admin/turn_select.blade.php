<?php 
use App\Models\Operation\WorkSchedule;
use App\Models\Facility\Room;
use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use App\Models\AdminUser;
?>
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
        <div class="box-body timeline">
        @foreach($room->beds as $bed)
            <?php 
                $schedule = WorkSchedule::where('bed_id', $bed->id)->orderBy('date', 'DESC')->first();
            ?>
            @if(!is_null($schedule))
            {{$bed->name}} : {{$schedule->date}}
            <a href="{{env('APP_URL')}}/admin/work-schedules/{{$schedule->id}}/edit">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="hori-timeline" dir="ltr">
                                    <ul class="list-inline events">
                                        <?php
                                            $staff1 = is_null($schedule->shift1) ? null : AdminUser::find($schedule->shift1)->name;
                                        ?>
                                        <li class="list-inline-item event-list {{isset($staff1) ? '' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff1) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->start_1}}</div>
                                                <h5 class="font-size-16">{{isset($staff1) ? $staff1 : ""}}</h5>
                                            </div>
                                        </li>
                                        <li class="list-inline-item event-list {{isset($staff1) ? 't' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff1) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->end_1}}</div>
                                                <h5 class="font-size-16">{{isset($staff1) ? $staff1 : ""}}</h5>
                                            </div>
                                        </li>          
                                        <?php
                                            $staff2 = is_null($schedule->shift2) ? null : AdminUser::find($schedule->shift2)->name;
                                        ?>
                                        <li class="list-inline-item event-list {{isset($staff2) ? 't' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff2) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->start_2}}</div>
                                                <h5 class="font-size-16">{{isset($staff2) ? $staff2 : ""}}</h5>
                                            </div>
                                        </li>
                                        <li class="list-inline-item event-list {{isset($staff2) ? 't' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff2) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->end_2}}</div>
                                                <h5 class="font-size-16">{{isset($staff2) ? $staff2 : ""}}</h5>
                                            </div>
                                        </li>    
                                        <?php
                                            $staff3 = is_null($schedule->shift3) ? null : AdminUser::find($schedule->shift3)->name;
                                        ?>
                                        <li class="list-inline-item event-list {{isset($staff3) ? 't' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff3) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->start_3}}</div>
                                                <h5 class="font-size-16">{{isset($staff3) ? $staff3 : ""}}</h5>
                                            </div>
                                        </li>
                                        <li class="list-inline-item event-list {{isset($staff3) ? 't' : 'no-border-color'}}">
                                            <div class="px-4">
                                                <div class="event-date {{isset($staff3) ? 'bg-soft-success' : 'bg-soft-danger'}} text-primary">{{$schedule->end_3}}</div>
                                                <h5 class="font-size-16">{{isset($staff3) ? $staff3 : ""}}</h5>
                                            </div>
                                        </li>                           
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                </div>
                </div>

            </a>
            @endif
        @endforeach
        </div>
    </div>
    
@endforeach