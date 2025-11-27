@extends('layouts.admin')

@section('title', 'Add Employee Attendance | '. (auth()->user()->companySetting->site_name ?? ''))

@section('pagescript')
    <script src="{{ asset('js/file-upload.js') }}"></script>
    <script src="{{ asset('js/typeahead.js') }}"></script>
    <script src="{{ asset('js/select2.js') }}"></script>
    <script src="{{ asset('js/formpickers.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#status').change(function() {
                if ($(this).val() === 'A') {
                    $('#reasonField').show();
                } else {
                    $('#reasonField').hide();
                    $('#reason').val('');
                }
            });
        });
    </script>
@stop

@section('content')

<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title">Add Employee Attendance</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('attendance') }}">Attendance</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Attendance</li>
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

        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" action="{{ url('attendance/store') }}" method="post">
                            @csrf

                            
                            <!-- User Dropdown -->
                             
                             <div class="form-group">
                                <label>Select Employee</label>
                                <select id="user_id" name="user_id" class="js-example-basic-single w-100" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            

                            <!-- Attendance Status Dropdown -->
                            <div class="form-group">
                                <label>Status</label>
                                <select id="status" name="status" class="js-example-basic-single w-100" required>
                                    <option value="P">Present</option>
                                    <option value="A">Absent</option>
                                </select>
                            </div>

                            <!-- Conditional Reason Field (for Absent only) -->
                            <div class="form-group" id="reasonField" style="display: none;">
                                <label>Reason (Required if Absent)</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="160"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a class="btn btn-light" href="{{ url('attendance') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
