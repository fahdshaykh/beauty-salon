

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Invoice Settings | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Invoice Settings

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="#">Settings</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Invoice Settings</li>

                </ol>

            </nav>

        </div>

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{Session::get('success')}}
        </div>

        @elseif(Session::has('destroy'))
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{Session::get('destroy')}}
        </div>
        @endif

        <div class="row">

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Invoices setup</h4>

                <p class="card-description">

                  Select <code>Starting date</code> & <code>Ending Date.</code> to set invoices to zero!

                </p>

                <form class="form-inline" action="{{url('invoice-setting')}}" method="get">
                
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

                  

                   

                   

                    <button type="submit" value="submit" class="btn btn-primary mb-2">Update</button>


                </form>

              </div>

            </div>

          </div>

        </div>
       
    </div>

</div>

@endsection