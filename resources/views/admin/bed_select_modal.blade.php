<?php 
use App\Models\Operation\BedOrder;
use App\Models\Facility\Room;
use App\Models\Facility\Branch;
use App\Models\Facility\Zone;
use App\Models\AdminUser;
?>

<form class="selectForm" id="select-form"  >
  <div class="form-group">
    <label for="bed-name" class="control-label">Chọn giường: {{$bed->name}}</label>
    <input type="hidden" class="form-control bed-id" name="bed-id" id="bed-id" value="{{$bed->id}}"/>
    <div class="form-group">
      <label>Chọn khách hàng</label>
      <select class="form-control" name="customer-id" id="customer-id">
        @foreach ($customers as $id=>$customer)
          <option value={{$customer->id}}>{{$customer->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn dịch vụ</label>
      <select class="form-control" name="order-id" id="order-id">
        @foreach ($orders as $id=>$order)
          <option value={{$order->id}}>{{$order->service->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 1:</label>
      <select class="form-control" name="staff_1">
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 2:</label>
      <select class="form-control" name="staff_2">
        <option value=""></option>
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn người xông sục:</label>
      <select class="form-control" name="staff_3">
        <option value=""></option>
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
</form>