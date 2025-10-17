@section('pagescript')



<script src="{{asset('js/file-upload.js')}}"></script>



<script src="{{asset('js/typeahead.js')}}"></script>



<script src="{{asset('js/select2.js')}}"></script>



<script src="{{asset('js/formpickers.js')}}"></script>


<script>
$(document).ready(function() {
    // Function to update amount_pending
    function updatePendingAmount() {
        let totalPrice = parseFloat($('#price').val()) || 0;
        let amountPaid = parseFloat($('input[name="amount_paid"]').val()) || 0;
        let amountPending = totalPrice - amountPaid;

        $('input[name="amount_pending"]').val(amountPending >= 0 ? amountPending : 0);
    }

    // Bind event listeners
    $('#price, input[name="amount_paid"]').on('input', function() {
        updatePendingAmount();
    });
});
</script>


@stop







@extends('layouts.admin')







@section('title', 'Create User | '.Session::get('site_name'))







@section('content')











<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



               Create New Inventory



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('/users')}}">Inventories</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Create New Inventory</li>



                </ol>



            </nav>



        </div>



          @if ($errors->any())



              <div class="alert alert-danger alert-dismissible">



                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                  <ul>



                      @foreach ($errors->all() as $error)



                          <li>{{ $error }}</li>



                      @endforeach



                  </ul>



              </div>



          @endif



        <div class="row">



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Create Inventory</h4>



                    <form class="forms-sample" action="{{route('inventories_purchases.update', $purchase->id)}}" method="post" enctype="multipart/form-data">



                    @csrf
                    @method('put')



                      <div class="form-group">



                        <label for="name">Select Product</label>



                        <select class="form-control" id="name" name="inventory_id">
                            @foreach($inventories as $inventory)
                                <option value="{{ $inventory->id }}" {{ $inventory->id == old('inventory_id', $purchase->inventory_id) ? 'selected' : '' }}>
                                    {{ $inventory->name }}
                                </option>
                            @endforeach
                        </select>



                      </div>



                      <div class="form-group">



                        <label for="quantity">Quantity</label>



                        <input type="number" class="form-control" name="quantity" value="{{old('quantity', $purchase->quantity)}}" id="quantity" placeholder="quantity">



                      </div>

                      



                      <div class="form-group">



                        <label for="price">Price</label>



                        <input type="number" class="form-control" name="price" id="price" value="{{ $purchase->price }}" placeholder="Enter price">



                      </div>


                      <div class="form-group">



                        <label for="name">Select Supplier</label>



                        <select class="form-control" id="name" name="supplier_id">
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $supplier->id == old('supplier_id', $purchase->supplier_id) ? 'selected' : '' }}>
                                    {{ $inventory->name }}
                                </option>
                            @endforeach
                        </select>



                      </div>



                      <!-- <div class="form-group">



                        <label>File upload</label>



                        <input type="file" name="image" class="file-upload-default">



                        <div class="input-group col-xs-12">



                          <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">



                          <span class="input-group-append">



                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>



                          </span>



                        </div>



                      </div> -->

                      <div class="form-group">

                        <label for="name">Select Supplier</label>

                        <select class="form-control" id="name" name="paypemnt_type">
                           
                             <option value="cash">Cash</option>

                             <option value="check">Check</option>
                            
                         </select>

                      </div>

                      <div class="form-group">



                      <label for="price">Amount Paid</label>



                      <input type="number" class="form-control" name="amount_paid" value="{{ $purchase->amount_paid }}" id="price" placeholder="Enter Amount Paid">



                      </div>

                      <div class="form-group">



                      <label for="price">Amount Pending</label>



                      <input type="number" class="form-control" name="amount_pending" value="{{ $purchase->amount_pending }}" id="price" disabled>



                      </div>


                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160">{{ $purchase->description }}</textarea>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Submit</button>



                      <a class="btn btn-light" href="{{route('inventories.index')}}">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    @endsection