@section('pagescript')



    <script src="{{asset('js/file-upload.js')}}"></script>



    <script src="{{asset('js/typeahead.js')}}"></script>



    <script src="{{asset('js/select2.js')}}"></script>



    <script src="{{asset('js/formpickers.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Add New Expense | '.Session::get('site_name'))







@section('content')











 <!-- partial -->



 <div class="main-panel">        



        <div class="content-wrapper">



          <div class="page-header">



            <h3 class="page-title">



                Add Expense



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                <li class="breadcrumb-item"><a href="{{url('expenses')}}">Expenses</a></li>



                <li class="breadcrumb-item active" aria-current="page">Add Expense</li>



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







          @if(Session::has('error'))



            <div class="alert alert-danger alert-dismissible">



                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                {{Session::get('error')}}



            </div>



          @endif



          



          <div class="row">



            <div class="col-md-6 grid-margin stretch-card">



              <div class="card">



                <div class="card-body">



                  <form class="forms-sample" action="{{url('expenses/store')}}" method="post">



                  @csrf



                    <div class="form-group">



                      <label>Select Expense Type</label>



                      <select id="expense_type" name="expense_type" class="js-example-basic-single w-100" required>



                      @foreach($expense_types as $expense_type)



                            <option value="{{$expense_type->id}}">{{$expense_type->type}}</option>



                        @endforeach



                      </select>



                    </div>



                    <div class="form-group">



                      <label>Select Payment Method</label>



                      <select id="payment_method" name="payment_method" class="js-example-basic-single w-100" required>



                      @foreach($payment_methods as $payment_method)



                            <option value="{{$payment_method->id}}">{{$payment_method->method}}</option>



                        @endforeach



                      </select>



                    </div>



                    <div class="form-group">



                      <div id="datepicker-popup-new" class="input-group date datepicker">



                        <input type="text" name="payable" value="" class="form-control" required>



                        <span class="input-group-addon input-group-append border-left">



                          <span class="far fa-calendar input-group-text"></span>



                        </span>



                      </div>



                    </div>











                    <div class="form-group">



                      <div class="input-group">



                        <div class="input-group-prepend">



                          <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>



                        </div>



                        <input type="text" name="amount" class="form-control" aria-label="Amount (to the nearest dollar)" required>



                      </div>



                    </div>



                    <div class="form-group">



                        <label for="exampleTextarea1">Expense Description</label>



                        <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                    </div>



                    <button type="submit" class="btn btn-primary mr-2">Submit</button>



                    <a class="btn btn-light" href="{{url('expenses')}}">Cancel</a>



                  </form>



                </div>



              </div>



            </div>



          </div>



        </div>



      



@endsection











