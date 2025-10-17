@extends('layouts.admin')



@section('title', 'Send PostCard | '.Session::get('site_name'))



@section('content')
<style>
  .hide
{
 display:none;
}
</style>
<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Send PostCard

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('postcard')}}">Post Cards</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Send PostCard</li>

                </ol>

            </nav>

        </div>

        @if(Session::has('success'))

            <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                {{Session::get('success')}}

            </div>

        @endif

        @if(Session::has('error'))

            <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                {!!Session::get('error')!!}

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
            <form class="forms-sample" action="{{url('postcard-send')}}" method="post" enctype="multipart/form-data">
            @csrf
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">PostCard Form</h4>
                        @if($settings->clicksend_api_key=="" || $settings->clicksend_username=="" || $settings->clicksend_password=="")
                        <p class="card-description" style="color:red;">You don't have account on clicksend<a target="_blank" href="https://clicksend.com/?u=267536"> Create my account</a></p>
                        @endif
                        <div class="form-group">
                            <label>Select multiple customer</label>
                            <select class="js-example-basic-multiple w-100" name="customers[]" multiple="multiple" required>
                            <option value="" disabled>Select Customer</option>

                                @foreach($customers as $customer)

                                    <option value="{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}} ({{$customer->primary_phone}}) </option>

                                @endforeach
                            </select>
                            
                        </div>
                        <div class="form-group">
                            <div class="form-check form-check-flat form-check-primary">
                                                            
                                <label class="form-check-label"> Click if you have PostCard URLs
                                    
                                <input id="check_file_url" name="urls" type="checkbox" class="form-check-input custom-check" value="1">
                                    <i class="input-helper"></i></label>
                            </div>
                        </div>
                        <div class="hide" id="file_url">
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" id="file_url1" name="file_url1" class="form-control" placeholder="Enter url for pdf file" >
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="file_url2" class="form-control" placeholder="Enter second Url if you have fornt & back are different" >
                                </div>
                            </div>
                        </div>
                    
                        <div class="card" id="upload_pdf">
                            <div class="card-body">
                            <h4 class="card-title d-flex">Upload PostCard</h4>
                        
                            <input type="file" name="post_card" id="post_card" class="dropify" required />
                            </div>
                            
                        </div>
                        <p class="card-description">
                                Instruction for PostCard Template <code><a href="{{url('postcard-info')}}"><i class="fas fa-info-circle"></i>Click here </a></code>
                            </p>
                            <button type="submit" style="float:right;" class="btn btn-primary mr-2">Submit</button>
                    </div>
                    
                </div>
              
                </div>
              
            </form>
        </div>

    </div>

</div>
<script>
$(document).ready(function() {
  $("#check_file_url").click(function() {
      if ($('#check_file_url').is(":checked"))
      {
        $('#file_url').removeClass( "hide");
        $('#file_url1').prop('required',true);
        $('#post_card').prop('required',false);
        $('#upload_pdf').addClass("hide");
      }
      else{
        $('#file_url').addClass("hide");
        $('#upload_pdf').removeClass( "hide");
        $('#file_url1').prop('required',false);
        $('#post_card').prop('required',true);
    //   $('#num_days').prop('required',true);
      }
  });

});
</script>
@endsection