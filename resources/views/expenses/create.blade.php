@section('pagescript')



    <script src="{{asset('js/file-upload.js')}}"></script>



    <script src="{{asset('js/typeahead.js')}}"></script>



    <script src="{{asset('js/select2.js')}}"></script>



    <script src="{{asset('js/formpickers.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Add New Expense | '. (auth()->user()->companySetting->site_name ?? ''))







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



          <div class="row">



            <div class="col-md-12 grid-margin stretch-card">



              <div class="card">



                <div class="card-body">



                  <form class="forms-sample" action="{{url('expenses/store')}}" method="post">



                  @csrf



                    <div class="form-group">



                      <label>Select Expense Type</label>



                      <select id="expense_type" name="expense_type" class="form-control @error('expense_type') is-invalid @enderror js-example-basic-single w-100" required>



                      @foreach($expense_types as $expense_type)



                            <option value="{{$expense_type->id}}">{{$expense_type->type}}</option>



                        @endforeach



                      </select>

                      @error('expense_type')
                          <div class="invalid-feedback d-block">
                              {{ $message }}
                          </div>
                      @enderror


                    </div>



                    <div class="form-group">



                      <label>Select Payment Method</label>



                      <select id="payment_method" name="payment_method" class="form-control @error('user_id') is-invalid @enderror js-example-basic-single w-100" required>



                      @foreach($payment_methods as $payment_method)



                            <option value="{{$payment_method->id}}">{{$payment_method->method}}</option>



                        @endforeach



                      </select>

                      @error('user_id')
                          <div class="invalid-feedback d-block">
                              {{ $message }}
                          </div>
                      @enderror


                    </div>



                    <div class="form-group">



                      <div id="datepicker-popup-new" class="input-group date datepicker">



                        <input type="text" name="payable" value="" class="form-control @error('payable') is-invalid @enderror" required>



                        <span class="input-group-addon input-group-append border-left">



                          <span class="far fa-calendar input-group-text"></span>



                        </span>

                        @error('payable')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror



                      </div>



                    </div>











                    <div class="form-group">



                      <div class="input-group">



                        <div class="input-group-prepend">



                          <span class="input-group-text bg-primary text-white">{{ currency_symbol() }}</span>



                        </div>



                        <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" aria-label="Amount (to the nearest dollar)" required>

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                      </div>



                    </div>



                    <div class="form-group">



                        <label for="exampleTextarea1">Expense Description</label>



                        <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                    </div>



                    <button type="submit" class="btn btn-primary mr-2">Create</button>



                    <a class="btn btn-light" href="{{url('expenses')}}">Cancel</a>



                  </form>



                </div>



              </div>



            </div>



          </div>



        </div>



      



@endsection











