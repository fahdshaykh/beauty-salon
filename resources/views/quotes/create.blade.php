@section('pagescript')



<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">



<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>



<script src="{{asset('js/editorDemo.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Send Quotation | '.Session::get('site_name'))







@section('content')







<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Send Quote



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('quote-mail')}}">Quote</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Send Quote</li>



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



            <div class="col-12 grid-margin">



                <div class="card">



                    <div class="card-body">



                        <h4 class="card-title">Quote Form</h4>



                        <form class="forms-sample" action="{{url('quote-mail')}}" method="post">



                            @csrf



                            <div class="form-group">



                            <label for="exampleInputUsername1">Email</label>



                            <input type="email" name="email" value="" class="form-control" placeholder="Enter Email Address" required/>



                            </div>



                            <div class="form-group">



                                <textarea id="summernoteExample" name="message"></textarea>



                            </div>



                            <button type="submit" class="btn btn-primary mr-2 mt-3">Send Quote</button>



                            <!-- <a class="btn btn-light" href="#">Cancel</a> -->



                        </form>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>



@endsection