@extends('layouts.admin')
@section('title', 'Daily Work Hours | '.Session::get('site_name'))

@section('pagescript')
<script src="{{asset('js/file-upload.js')}}"></script>
<script src="{{asset('js/typeahead.js')}}"></script>
<script src="{{asset('js/select2.js')}}"></script>
<script src="{{asset('js/formpickers.js')}}"></script>

<script>
    $(function () {
        // Initialise Select2 (same class you used in Email-History)
        $('.select2-user').select2({
          placeholder: "All Employees",
          allowClear: true,
          width: '100%',
          dropdownParent: $('.input-group') // Keeps arrow inside
      });
    });
</script>
@stop

@section('content')
<style>
    .loader{
        left:0;right:0;z-index:999;position:fixed;border:none;width:100%;top:200px;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <!-- Page Header -->
        <div class="page-header">
            <h3 class="page-title">Daily Work Hours</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Daily Work Hours</li>
                </ol>
            </nav>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Employee Work Hours</h4>

                        <!-- FILTER FORM -->
                        <div class="row">
                            <form class="form-inline w-100" method="GET" action="{{ route('work_hours') }}">

                                <!-- FROM DATE -->
                                <div class="col-lg-3">

                                    <div class="input-group mb-2 mr-sm-2">

                                        <div id="datepicker-popup-new" class="input-group date datepicker">

                                            <input type="text" name="date_from" value="{{ request('date_from') }}" placeholder=" Start Date" class="form-control"/>

                                            <span class="input-group-addon input-group-append border-left">

                                                <span class="far fa-calendar input-group-text"></span>

                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <!-- TO DATE -->

                                <div class="col-lg-3">

                                    <div class="input-group mb-2 mr-sm-2">

                                        <div id="datepicker-popup-new2" class="input-group date datepicker">

                                            <input type="text" name="date_to" value="{{ request('date_to') }}" placeholder="End Date" class="form-control"/>

                                            <span class="input-group-addon input-group-append border-left">

                                                <span class="far fa-calendar input-group-text"></span>

                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <!-- EMPLOYEE SELECT (Select2) -->
                                <div class="col-lg-4">
                                    <div class="form-group mb-2 mr-sm-2 w-100">
                                        <select name="user_id"
                                                class="form-control js-example-basic-multiple w-100">
                                            <option value="">All Employees</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}"
                                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- SEARCH + RESET -->
                                <div class="col-lg-2">
                                    <div class="form-group mb-2">
                                        <button type="submit" class="btn btn-primary mr-2">Search</button>
                                        <a href="{{ route('work_hours') }}"
                                           class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <br>

                        <!-- Pagination Dropdown -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="pagination-dropdown">
                                    <form class="form-inline">
                                        <div class="form-group">
                                            <label for="rows">Records:&nbsp;</label>
                                            <select class="form-control" id="rows" name="rows">
                                                <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                                <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                                <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                                <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                                <option value="100" @if($rows == 100) selected @endif > 100 </option>
                            <option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TABLE -->
                        <table id="customer-listing" class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Entry Time</th>
                                    <th>Exit Time</th>
                                    <th>Overtime (mins)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($workHours as $index => $workHour)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $workHour->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($workHour->check_in)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($workHour->check_in)->format('H:i') }}</td>
                                        <td>
                                            {{ $workHour->check_out
                                                ? \Carbon\Carbon::parse($workHour->check_out)->format('H:i')
                                                : '<span class="text-muted">—</span>' }}
                                        </td>
                                        <td>{{ $workHour->overtime_minutes ?? '0' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/work-hours') }}?rows=" + this.value;
        });
    }
});
</script>