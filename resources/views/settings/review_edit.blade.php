@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Edit Review URL | '.Session::get('site_name'))



@section('content')





 <!-- partial -->

 <div class="main-panel">        

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

                Update Review URL

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('review-settings')}}">Review Settings</a></li>

                <li class="breadcrumb-item active" aria-current="page">Edit URL</li>

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

            <div class="col-md-12 grid-margin stretch-card">

              <div class="card">

                <div class="card-body">

                  <h4 class="card-title">Edit Review URL</h4>

                  <form class="forms-sample" action="{{url('review-settings/update')}}" method="post">

                  @csrf

                    <div class="form-group">

                        <label for="exampleTextarea1">Review URL</label>

                        <textarea class="form-control" id="exampleTextarea1" name="url" rows="4">{{ $review_settings['0']->url }}</textarea>

                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="{{url('review')}}">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>

      

@endsection



