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







@section('title', 'Create Inventory Purchase | '. (auth()->user()->companySetting->site_name ?? ''))







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



                    <li class="breadcrumb-item"><a href="{{ route('inventories_purchases.index') }}">Inventories</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Purchase inventory</li>



                </ol>



            </nav>



        </div>



        <div class="row">



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Create Inventory</h4>



                    <form class="forms-sample" action="{{route('inventories_purchases.store')}}" method="post" enctype="multipart/form-data">



                    @csrf



                      <div class="form-group">

                        <label for="name">Select Item</label>

                        <select class="form-control @error('inventory_id') is-invalid @enderror js-example-basic-single w-100" id="name" name="inventory_id">
                           @foreach($inventories as   $inventory)
                             <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                            @endforeach
                         </select>

                          @error('inventory_id')
                              <div class="invalid-feedback d-block">
                                  {{ $message }}
                              </div>
                          @enderror
                      </div>



                      <div class="form-group">



                        <label for="quantity">Quantity</label>



                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{old('quantity')}}" id="quantity" placeholder="quantity">
                        
                        @error('quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror


                      </div>

                      



                      <div class="form-group">



                        <label for="price">Total Price</label>



                        <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" id="price" placeholder="Enter price">

                        @error('price')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror


                      </div>


                      <div class="form-group">

                        <label for="name">Select Supplier</label>

                        <select class="form-control @error('supplier_id') is-invalid @enderror js-example-basic-single w-100" id="name" name="supplier_id">
                           @foreach($suppliers as   $supplier)
                             <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                         </select>

                          @error('supplier_id')
                              <div class="invalid-feedback d-block">
                                  {{ $message }}
                              </div>
                          @enderror
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

                        <label for="name">Select payment</label>

                        <select class="form-control @error('paypemnt_type') is-invalid @enderror" id="name" name="paypemnt_type">
                           
                             <option value="cash">Cash</option>

                             <option value="check">Check</option>
                            
                         </select>

                          @error('paypemnt_type')
                              <div class="invalid-feedback d-block">
                                  {{ $message }}
                              </div>
                          @enderror
                      </div>

                      <div class="form-group">



                      <label for="price">Amount Paid</label>



                      <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" name="amount_paid" id="price" placeholder="Enter Amount Paid">

                      @error('amount_paid')
                          <div class="invalid-feedback d-block">
                              {{ $message }}
                          </div>
                      @enderror

                      </div>

                      <div class="form-group">
                          <label for="date">Purchase Date</label>
                        <div id="datepicker-popup-new" class="input-group date datepicker ">
                          <input type="text" name="purchase_date" value="{{ old('purchase_date') }}" class="form-control @error('purchase_date') is-invalid @enderror" >
                          <span class="input-group-addon input-group-append border-left">
                            <span class="far fa-calendar input-group-text"></span>
                          </span>
                          @error('purchase_date')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror
                        </div>
                      </div>

                      <div class="form-group">



                      <label for="price">Amount Pending</label>



                      <input type="number" class="form-control @error('amount_pending') is-invalid @enderror" name="amount_pending" id="price" disabled>


                      @error('amount_pending')
                          <div class="invalid-feedback d-block">
                              {{ $message }}
                          </div>
                      @enderror

                      </div>

                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Add Purchase</button>



                      <a class="btn btn-light" href="{{route('inventories.index')}}">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    @endsection