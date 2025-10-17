

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Add Holidays | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Add Holidays

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="#">Holidays</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Add Holidays</li>

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

                <h4 class="card-title">Holidays Setup</h4>

                <p class="card-description">

                  Select <code>Starting date</code> & <code>Ending Date.</code> to apply holidays in calendar!

                </p>

                <form class="form-inline" action="{{url('update-holidays')}}" method="post" id="expense-filters">
                @csrf
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
        <div class="card" id="report">
        <div class="card">
            <div class="card-body">
                <div class="row grid-margin">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>Sr </th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Date range</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $num = 1; ?>
                                    @foreach($holidays as $holiday)
                                    <tr>
                                    <td>{{$num}}</td>
                                    <td>{{$holiday->start_date}}</td>
                                    <td>{{$holiday->end_date}}</td>
                                    <td>{{$holiday->days}}</td>
                                    <td><a href="{{url('delete-holidays',$holiday->id)}}">
                                    <i class="fas fa-trash-alt text-danger" aria-hidden="true"></i>                          
                                </a></td>
</tr>
                                    <?php $num++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          </div>
    </div>

</div>

@endsection