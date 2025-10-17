@extends('layouts.admin')

@section('title', 'Daily Work Hours | '.Session::get('site_name'))

@section('content')
<div class="main-panel">          
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">Daily Work Hours</h3>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Pages</a></li>
          <li class="breadcrumb-item active" aria-current="page">Daily Work Hours</li>
        </ol>
      </nav>
    </div>
    
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Employee Work Hours</h4>
            
            <form action="{{ route('work_hours') }}" method="GET" class="mb-4">
              <div class="form-group">
                <label for="date_from">From:</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}" onchange="this.form.submit()">
              </div>
              <div class="form-group">
                <label for="date_to">To:</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}" onchange="this.form.submit()">
              </div>
              <div class="form-group">
                <label for="user_id">Select Employee:</label>
                <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                  <option value="">All Employees</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                      {{ $user->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </form>
            
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr class="bg-primary text-white">
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Date</th>
                    <th>Entry Time</th>
                    <th>Exit Time</th>
                    <th>overtime minutes</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($workHours as $index => $workHour)
                  
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $workHour->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($workHour->check_in)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($workHour->check_in)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($workHour->check_out)->format('H:i') }}</td>
                    <td>{{ $workHour->overtime_minutes }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pagescript')
<script src="{{asset('js/file-upload.js')}}"></script>
<script src="{{asset('js/typeahead.js')}}"></script>
<script src="{{asset('js/select2.js')}}"></script>
<script src="{{asset('js/formpickers.js')}}"></script>
<script>
    document.getElementById('date_from').addEventListener('change', function() {
        this.form.submit();
    });
    document.getElementById('date_to').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@stop
