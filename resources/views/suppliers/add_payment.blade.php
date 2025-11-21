@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Create New Service | '.Session::get('site_name'))



@section('content')





 <!-- partial -->

 <div class="main-panel">        

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

                Add Payment

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{route('suppliers.index')}}">Supplers</a></li>

                <li class="breadcrumb-item active" aria-current="page">Add Payment</li>

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

            <div class="col-md-12 grid-margin stretch-card">

              <div class="card">

                <div class="card-body">

                  <h4 class="card-title">Add Pending Payment</h4>

                  <form class="forms-sample" action="{{ route('supplier_payments.store') }}" method="post">

                  @csrf

                    <input type="hidden" class="form-control" name="supplier_id" value="{{ $supplier->id }}" required>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Amount</label>

                      <input type="number" class="form-control" name="amount" id="exampleInputUsername1" placeholder="Enter Amount" required>

                    </div>


                    <div class="form-group">

                      <div class="form-group mt-2">

                        <label for="exampleInputUsername1">Note</label>

                        <textarea class="form-control" id="exampleTextarea1" name="note" rows="4" maxlength="160"></textarea>

                      </div>

                    </div>


                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="{{ route('suppliers.balance_summary', $supplier->id) }}">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>

</div>

      

@endsection





