@extends('layouts.admin')
@section('title', 'Work Schedule | '.Session::get('site_name'))

@section('pagescript')
<script src="{{asset('js/file-upload.js')}}"></script>
<script src="{{asset('js/typeahead.js')}}"></script>
<script src="{{asset('js/select2.js')}}"></script>
<script src="{{asset('js/formpickers.js')}}"></script>

<script>
$(function () {
    // Open modal and fill data
    $(document).on('click', '.edit-schedule-btn', function () {
        const id    = $(this).data('id');
        const day   = $(this).data('day');
        const start = $(this).data('start').substring(0, 5); // HH:MM
        const end   = $(this).data('end').substring(0, 5);

        $('#schedule_id').val(id);
        $('#day_name').val(ucfirst(day));
        $('#start_time').val(start);
        $('#end_time').val(end);

        $('#editScheduleModal').modal('show');
    });

    // Submit form via AJAX
    $('#editScheduleForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const id   = $('#schedule_id').val();
        const url  = '{{ route("work_schedule.update", ":id") }}'.replace(':id', id);
        const data = form.serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function (res) {
                if (res.success) {
                    $('#editScheduleModal').modal('hide');
                    location.reload(); // or update table row dynamically
                } else {
                    $('#modalError')
                        .removeClass('d-none')
                        .text(res.message || 'Validation failed');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Server error. Please try again.';
                $('#modalError')
                    .removeClass('d-none')
                    .text(msg);
            }
        });
    });
});

// Helper: ucfirst for JS
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}
</script>
@stop

@section('content')
<div class="main-panel">
    <div class="content-wrapper">

    <div class="alert alert-success" style="display:none;" id="status-success"></div>

        <!-- Page Header -->
        <div class="page-header">
            <h3 class="page-title">Work Schedule</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('work_schedule.index') }}">Schedules</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Work Schedule</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Work Schedule Table</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>#</th>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <!-- <th>Status</th> -->
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $schedule)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ ucfirst($schedule->day) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                            <!-- <td>

                                                <label class="switch">

                                                    <input type="checkbox" id="user-{{$schedule->id}}"

                                                        onclick="changeScheduleStatus(event.target, {{ $schedule->id }});"{{($schedule->is_active) ? 'checked' : ''}}>



                                                    <span class="slider round"></span>



                                                </label>



                                            </td> -->
                                            <td>
                                                @can('edit work schedule')
                                                <button class="btn btn-sm btn-primary edit-schedule-btn"
                                                        data-id="{{ $schedule->id }}"
                                                        data-day="{{ $schedule->day }}"
                                                        data-start="{{ $schedule->start_time }}"
                                                        data-end="{{ $schedule->end_time }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No schedules found</td>
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
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editScheduleForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Work Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalError" class="alert alert-danger d-none"></div>

                    <input type="hidden" name="schedule_id" id="schedule_id">

                    <div class="form-group mb-3">
                        <label class="form-label">Day</label>
                        <input type="text" id="day_name" class="form-control" disabled>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<script>



function changeScheduleStatus(_this, id) {



 



    var status = $(_this).prop('checked') == true ? 1 : 0;



    $.ajax({



        url: "{{url('schedule-status')}}",



        type: 'GET',



        data: {



            id: id,



            status: status 



        },



        success: function (result) {



          $("#status-success").show();



          $("#status-success").html(result.success);



          setTimeout(function() {



            $("#status-success").hide()



          }, 3000);



        }



    });



}

</script>