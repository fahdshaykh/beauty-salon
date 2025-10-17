@section('pagescript')
    <script src="{{asset('js/file-upload.js')}}"></script>
    <script src="{{asset('js/typeahead.js')}}"></script>
    <script src="{{asset('js/select2.js')}}"></script>
    <script src="{{asset('js/formpickers.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Edit Message Settings | '.Session::get('site_name'))

@section('content')


 <!-- partial -->
 <div class="main-panel">        
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
                Update API
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('message-settings')}}">Site Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit API</li>
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
                  <h4 class="card-title">Edit Api Key</h4>
                  <form class="forms-sample" action="{{url('message/update',$message_settting['0']->id)}}" method="post">
                  @csrf
                    <div class="form-group">
                      <label for="exampleInputUsername1">API Key:</label>
                      <input type="text" class="form-control" value="{{ $message_settting['0']->api_key }}" name="api_key" id="exampleInputUsername1" placeholder="Enter Service Name" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Description</label>
                        <textarea class="form-control" id="exampleTextarea1" name="description" rows="4">{{ $message_settting['0']->description }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <a class="btn btn-light" href="{{url('message')}}">Cancel</a>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      
@endsection

