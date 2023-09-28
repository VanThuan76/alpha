@foreach ($bedOrders as $id=>$order)
  <option value={{$order->id}}>{{$order->service->name}}</option>
@endforeach