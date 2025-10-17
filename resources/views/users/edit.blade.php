@section('pagescript')



<script src="{{asset('js/file-upload.js')}}"></script>



<script src="{{asset('js/typeahead.js')}}"></script>



<script src="{{asset('js/select2.js')}}"></script>



<script src="{{asset('js/formpickers.js')}}"></script>


<script>
  $(document).ready(function () {
    function toggleTransportAllowanceInput() {
      if ($('#exampleSelectGender').val() === 'yes') {
        $('#transportAllowanceInput').show();
      } else {
        $('#transportAllowanceInput').hide();
      }
    }

    // Run on page load
    toggleTransportAllowanceInput();

    // Run on dropdown change
    $('#exampleSelectGender').on('change', function () {
      toggleTransportAllowanceInput();
    });
  });
</script>

@stop







@extends('layouts.admin')







@section('title', 'Edit User | '.Session::get('site_name'))







@section('content')











<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Update User



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('/users')}}">Users</a></li>



                    <li class="breadcrumb-item active" aria-current="page">New User</li>



                </ol>



            </nav>



        </div>



        @if ($errors->any())



        <div class="alert alert-danger">



            @foreach ($errors->all() as $error)



            {{ $error }}



            @endforeach



        </div>



        @endif



        <div class="row">



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Update User</h4>



                    <form class="forms-sample" action="{{route('user.update', $user->id)}}" method="post" enctype="multipart/form-data">



                    @csrf



                      <div class="form-group">



                        <label for="name">Name</label>



                        <input type="text" class="form-control" name="name" value="{{$user->name}}" id="name" placeholder="Name">



                      </div>



                      <div class="form-group">



                        <label for="email">Email</label>



                        <input type="email" class="form-control" name="email" value="{{$user->email}}" id="email" placeholder="Email">



                      </div>



                      <div class="form-group">



                        <label for="password">Password</label>



                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">



                      </div>

                      <div class="form-group">



                        <label for="userphone">User Phone</label>



                        <input type="text" class="form-control" name="phone" value="{{$user->phone}}" id="userphone" placeholder="Enter Phone Number" maxLength="12">



                      </div>



                      <div class="form-group">



                      <label for="userphone">Base Salary</label>



                      <input type="number" class="form-control" name="base_salary" id="userphone" value="{{$user->base_salary}}" placeholder="Enter Base Salary" maxLength="12">



                      </div>


                      <div class="form-group">



                      <label for="userphone">Commision rate</label>



                      <input type="number" class="form-control" name="commission_rate" id="userphone" value="{{$user->commission_rate}}" placeholder="Enter Commision rate" maxLength="12">



                      </div>


                      <div class="form-group">
                        <label for="exampleSelectGender">Transport Allowance</label>
                        <select class="form-control" name="is_transport" id="exampleSelectGender" required>
                          <option value="">Select Transport</option>
                          <option value="no" {{ $user->is_transport == 'no' ? 'selected' : '' }}>No</option>
                          <option value="yes" {{ $user->is_transport == 'yes' ? 'selected' : '' }}>Yes</option>
                        </select>
                      </div>

                      <div class="form-group" id="transportAllowanceInput" style="display: none;">
                        <label for="transportAllowance">Transport Allowance Amount</label>
                        <input type="number" class="form-control" name="transport_allowance" id="transportAllowance" placeholder="Enter allowance amount" value="{{ $user->transport_allowance }}">
                      </div>



                      <div class="form-group">



                        <label for="Address">User Address</label>



                        <input type="text" class="form-control" name="address" value="{{$user->address}}" id="address" placeholder="Enter Address">



                      </div>



                      <div class="form-group">



                        <label for="exampleSelectGender">Role</label>



                        <select class="form-control" name="role_id" id="exampleSelectGender">



                        <option value="">Select Role</option>



                        @foreach($roles as $role)



                          <option value="{{$role->id}}" @if($role->id == $user->role_id) selected @endif>{{$role->role}}</option>



                        @endforeach



                        </select>



                      </div>



                      <div class="form-group">



                        <label>File upload</label>



                        <input type="file" name="image" class="file-upload-default">



                        <div class="input-group col-xs-12">



                          <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">



                          <span class="input-group-append">



                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>



                          </span>



                        </div>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Submit</button>



                      <a class="btn btn-light" href="{{route('users.index')}}">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    @endsection