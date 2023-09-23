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
      <option>option 1</option>
      <option>option 2</option>
      <option>option 3</option>
      <option>option 4</option>
      <option>option 5</option>
      </select>
    </div>
    <div class="form-group">
      <label>Chọn dịch vụ</label>
      <select class="form-control">
      <option>option 1</option>
      <option>option 2</option>
      <option>option 3</option>
      <option>option 4</option>
      <option>option 5</option>
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 1:</label>
      <select class="form-control">
      <option>option 1</option>
      <option>option 2</option>
      <option>option 3</option>
      <option>option 4</option>
      <option>option 5</option>
      </select>
    </div>
    <div class="form-group">
      <label>Chọn kỹ thuật viên 2:</label>
      <select class="form-control">
      <option>option 1</option>
      <option>option 2</option>
      <option>option 3</option>
      <option>option 4</option>
      <option>option 5</option>
      </select>
    </div>
    <div class="form-group">
      <label>Chọn người xông xục:</label>
      <select class="form-control">
      <option>option 1</option>
      <option>option 2</option>
      <option>option 3</option>
      <option>option 4</option>
      <option>option 5</option>
      </select>
    </div>
  </div>
</form>