@section('pagescript')



<script src="{{asset('js/file-upload.js')}}"></script>



<script src="{{asset('js/typeahead.js')}}"></script>



<script src="{{asset('js/select2.js')}}"></script>



<script src="{{asset('js/formpickers.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Create Inventory Item | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')











<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



               New Inventory Purchase



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('/inventories')}}">Inventories</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create inventory</li>



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



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Inventory</h4>



                    <form class="forms-sample" action="{{route('inventories.store')}}" method="post">



                    @csrf



                      <div class="form-group">



                        <label for="name">Name</label>



                        <input type="text" class="form-control" value="{{old('name')}}" name="name" id="name" placeholder="Name">



                      </div>



                      <div class="form-group">



                      <label for="name">Low Alert</label>



                      <input type="text" class="form-control" value="{{old('low_alert')}}" name="low_alert" id="low_alert" placeholder="Minimum Alert">



                      </div>

                      <div class="form-group">



                      <label for="name">Max Alert</label>



                      <input type="text" class="form-control" value="{{old('max_alert')}}" name="max_alert" id="max_alert" placeholder="Maximum Alert">



                      </div>
                      

                      

                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Submit</button>



                      <a class="btn btn-light" href="{{route('inventories.index')}}">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    @endsection