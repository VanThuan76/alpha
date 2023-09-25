<?php 
use App\Models\BedOrder;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Zone;
use App\Models\AdminUser;
?>

<form class="tagForm" id="tag-form"  >
  <div class="form-group">
    <label for="bed-name" class="control-label">Chọn giường: {{$bed->name}}</label>
    <input type="hidden" class="form-control bed-id" id="bed-id"/>
    <div class="form-group">
      <label>Chọn khách hàng</label>
      <select class="form-control">
        @foreach ($customers as $id=>$customer)
          <option value={{$customer->id}}>{{$customer->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn dịch vụ</label>
      <select class="form-control">
        @foreach ($services as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 1:</label>
      <select class="form-control">
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 2:</label>
      <select class="form-control">
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Chọn người xông sục:</label>
      <select class="form-control">
        @foreach ($staffs as $id=>$name)
          <option value={{$id}}>{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
</form>