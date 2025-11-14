



@section('pagescript')



<script src="{{asset('js/select2.js')}}"></script>



<script src="{{asset('js/formpickers.js')}}"></script>



@stop



@extends('admin.layouts.admin')







@section('title', 'Create Expense Reports | '.Session::get('site_name'))







@section('content')







<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Create Reports



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('expense-reports')}}">Reports</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Expense Report</li>



                </ol>



            </nav>



        </div>



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



                <h4 class="card-title">Expense Report</h4>



                <p class="card-description">



                  In order to get report please select between dates <code>From Date</code> & <code>To Date.</code> You can also specifies with your Expense Type!



                </p>



                <form class="form-inline" action="{{url('record/expense')}}" method="get" id="inventory-filters">



                  <div class="input-group">



                    <div id="datepicker-popup-new" class="input-group date datepicker">



                      <input type="text" name="start_date" class="form-control">



                      <span class="input-group-addon input-group-append border-left">



                        <span class="far fa-calendar input-group-text"></span>



                      </span>



                    </div>



                  </div>



                  <div class="input-group">



                    <div id="datepicker-popup-new2" class="input-group date datepicker">



                      <input type="text" name="end_date" class="form-control">



                      <span class="input-group-addon input-group-append border-left">



                        <span class="far fa-calendar input-group-text"></span>



                      </span>



                    </div>



                  </div>



                  



                    <div class="form-group">



                      <select id="expense_type" name="company_id" class="form-control js-example-basic-single w-100" style="width:100%" required>



                        <option value="no" selected>Select Company</option>



                        @foreach($companies as $company)



                            <option value="{{$company->id}}">{{$company->company_name}}</option>



                        @endforeach



                      </select>



                    </div>



                    <button type="submit" value="submit" class="btn btn-primary ml-3 mb-2">Submit</button>





                </form>



              </div>



            </div>



          </div>



        </div>

        <div class="card" id="report">

            

          </div>

    </div>



</div>







<script>

  $(document).ready(function(){

    $("#inventory-filters").submit(function(event){

      event.preventDefault();

      $.ajax({

          url:"{{url('admin/company/record/inventory')}}",

          type:"GET",

          data: $('form#inventory-filters').serialize(),

          success:function(response){

            //console.log(response.view);

            $("#report").empty();

            $("#report").append(response.view);        

          }

        });  

          

    });

});



  



</script>



@endsection