@section('pagescript')
<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">
<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>
<script src="{{asset('js/editorDemo.js')}}"></script>
<script src="{{asset('js/file-upload.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Edit Email Settings | '.Session::get('site_name'))

@section('content')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Update Email-Formate
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

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{Session::get('success')}}
        </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">E-mail Form</h4>
                        <form class="forms-sample" action="{{url('email-settings-update',$email['0']->company_id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputUsername1">Email</label>
                                <input type="email" name="email" value="{{$email['0']->email}}" class="form-control" placeholder="Enter Email Address" required/>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Title</label>
                                <input type="text" name="title" value="{{$email['0']->title}}" class="form-control" placeholder="Enter Email Address" required/>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Subject</label>
                                <input type="text" name="subject" value="{{$email['0']->subject}}" class="form-control" placeholder="Enter Email Address" required/>
                            </div>

                            <div class="form-group">
                                <label>Email Logo upload</label>
                                <input type="file" name="image" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                                </div>
                            </div>
                            <img src="@if($email['0']->image){{asset('email_logo/'.$email['0']->image)}} @else {{asset('profiles/power_diary.png')}} @endif" alt="profile" style="margin-left: 400px;" class="img-lg rounded-circle mb-3"/>

                            <div class="form-group">
                                <label for="exampleInputUsername1">E-Mail Header</label>
                                <textarea id="summernoteHeader" name="header">{{$email['0']->header}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">E-Mail Footer</label>
                                <textarea id="summernoteFooter" name="footer">{{$email['0']->footer}}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2 mt-3">Update Setting</button>
                            <!-- <a class="btn btn-light" href="#">Cancel</a> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection