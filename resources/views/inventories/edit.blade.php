@section('pagescript')



<script src="{{asset('js/file-upload.js')}}"></script>



<script src="{{asset('js/typeahead.js')}}"></script>



<script src="{{asset('js/select2.js')}}"></script>



<script src="{{asset('js/formpickers.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Edit inventory item | '. (auth()->user()->companySetting->site_name ?? ''))







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

                    <li class="breadcrumb-item active" aria-current="page">Edit inventory</li>



                </ol>



            </nav>



        </div>



        <div class="row">



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Inventory</h4>



                    <form class="forms-sample" action="{{route('inventories.update', $inventory->id)}}" method="post">



                    @csrf
                    @method('put')



                      <div class="form-group">



                        <label for="name">Name</label>



                        <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name', $inventory->name)}}" name="name" id="name" placeholder="Name">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror


                      </div>


                      <div class="form-group">



                        <label for="name">Low Alert</label>



                        <input type="text" class="form-control @error('low_alert') is-invalid @enderror" value="{{old('low_alert', $inventory->low_alert)}}" name="low_alert" id="low_alert" placeholder="Low Alert">

                        @error('low_alert')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                      </div>      

                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160">{{ $inventory->description }}</textarea>



                      </div>



                      <button type="submit" class="btn btn-dark mr-2">Update Inventory</button>



                      <a class="btn btn-light" href="{{route('inventories.index')}}">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    @endsection