@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')

@section('title', 'Attendance Listing | '.Session::get('site_name'))

@section('content')

      <!-- partial -->
      <div class="main-panel">          
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              Attendance
            </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendance</li>
              </ol>
            </nav>
          </div>
          <li class="nav-item d-none d-lg-flex">
            @can('create attendance')
            <a class="nav-link" href="{{ route('employee_attendance') }}">
              <span class="btn btn-primary">+ Mark Attendance</span>
            </a>
            @endcan
          </li>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Table</h4>
                 
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">

                        <table class="table">
                          <thead>
                              <tr class="bg-primary text-white">
                                  <th>#</th>
                                  <th>Name</th>
                                  <th>Status</th>
                                  <th>Late Arrival</th>
                                  <th>Entry Time</th>
                                  <th>Entry Ip</th>
                                  <th>Exit Time</th>
                                  <th>Exit Ip</th>
                                  <th>Edit Exit Time</th>
                              </tr>
                          </thead>
                          <tbody>

                              @foreach($employees as $employee)

                              
                              <tr>
                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $employee->user->name }}</td>
                                  <td>{{ $employee->status }}</td>
                                  <td>{{ $employee->is_late ? 'Yes' : 'No' }}</td>
                                  <td>{{ \Carbon\Carbon::parse($employee->check_in)->format('H:i') }}</td>
                                  <td>{{ $employee->check_in_location }}</td>
                                  <td id="updated_at_{{ $employee->user_id }}">
                                  @if($employee->check_out)
                                      {{ \Carbon\Carbon::parse($employee->check_out)->format('H:i') }}
                                  @else
                                      --
                                  @endif
                                  </td>
                                  <td>{{ $employee->check_out_location }}</td>
                                  <td>
                                    @can('edit attendance')
                                    @if(!$employee->check_out)

                                      <button 
                                        class="btn btn-primary btn-sm update-exit-time-btn" 
                                        data-id="{{ $employee->user_id }}" 
                                        id="updateExitTimeBtn_{{ $employee->user_id }}"
                                        type="button">
                                        Update
                                      </button>

                                    @endif
                                    @endcan
                                  </td>
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
        </div>

        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2018 <a href="https://www.urbanui.com/" target="_blank">Urbanui</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="far fa-heart text-danger"></i></span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="updateExitTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Exit Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
            <div id="exitTimeError" class="alert alert-danger d-none"></div>
            <!-- <div class="form-check">
              <label class="form-check-label">
                <input type="checkbox" id="transportaion_assistance" class="form-check-input">
                Transportation assistance
              </label>
            </div> -->
                <input type="hidden" id="attendanceId">
                <input type="time" id="exitTime" class="form-control mt-2">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveExitTime">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
  
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
$(document).ready(function() {

    // Open modal & set user ID
    $(document).on('click', '.update-exit-time-btn', function() {
        var userId = $(this).data('id');
        $('#attendanceId').val(userId);
        $('#exitTime').val(''); // Clear previous value
        $('#updateExitTimeModal').modal('show');
    });

    // Save exit time (AJAX request)
    $('#saveExitTime').click(function() {
        var userId = $('#attendanceId').val();
        var exitTime = $('#exitTime').val();
        var transportAssistance = $('#transportaion_assistance').is(':checked') ? 1 : 0;

        $.ajax({
            url: '{{ route("update.exit.time") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId,
                check_out_time: exitTime,
                transport_assistance: transportAssistance
            },
            success: function(response) {
                if(response.success) {
                    $('#updateExitTimeModal').modal('hide');

                    // Update displayed time in the table
                    $('#updated_at_' + userId).text(exitTime);
                    location.reload();
                } else {
                    // console.log('Error:', response.message);
                    $('#exitTimeError')
                        .removeClass('d-none')
                        .text(response.message);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });

});
</script>
