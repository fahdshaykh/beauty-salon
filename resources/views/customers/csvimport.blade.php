@section('pagescript')
    <script src="{{asset('js/select2.js')}}"></script>
@stop
@extends('layouts.admin')



@section('title', 'Import Customer | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Import Customer

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Customers</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Import Customer</li>

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
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Import Customers</h4>
                  <form class="forms-sample" action="{{ url('import-customer') }}" method="POST" name="importform" enctype="multipart/form-data">
                  @csrf
                  <div class="card" id="upload_pdf">
                            <div class="card-body">
                            <h4 class="card-title d-flex">Upload CSV File</h4>
                        
                            <input type="file" name="import_file" id="post_card" class="dropify" required />
                            </div>
                            
                        </div>
                        <p class="card-description">
                                Download Example file and arrange data in that file <code><a href="{{asset('Customers.csv')}}" target="_blank"><i class="fas fa-info-circle"></i>Download Exampel Import File </a></code>
                            </p>
                            <button type="submit" style="float:right;" class="btn btn-primary mr-2">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

    </div>

</div>

@endsection