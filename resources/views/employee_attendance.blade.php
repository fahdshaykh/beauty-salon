@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Mark Attendance | '.Session::get('site_name'))



@section('content')
      <!-- partial -->
      <div class="main-panel">          
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              Mark Attendance
            </h3>
           
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Today Attendance</h4>
                  <div class="row grid-margin">
                    
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
            <form id="attendanceForm" method="POST" action="{{ route('save-attendance') }}">
                     @csrf

         <!-- Attendance Date -->
                      <!-- <div class="form-group">
                        <label for="attendanceDate">Attendance Date:</label>
                        <input type="date" id="attendanceDate" name="attendance_date"  class="form-control" required >
                      </div> -->

                        <table class="table">
                          <thead>
                            <tr class="bg-primary text-white">
                                <th>#</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Types of Leave</th>
                                <th>Reason</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="hidden" name="data[{{ $loop->index }}][user_id]" value="{{ $user->id }}">
                                    {{ $user->id }}
                                </td>
                                <td>
                                    <input type="hidden" name="data[{{ $loop->index }}][name]" value="{{ $user->name }}">
                                    {{ $user->name }}
                                </td>
                                <td>
                                    <button 
                                        class="badge badge-info present-absent-btn" 
                                        data-status="P"
                                        onclick="toggleStatus(this)" 
                                        type="button">
                                        P
                                    </button>
                                    <input type="hidden" name="data[{{ $loop->index }}][status]" value="P" class="status-input">
                                </td>
                                <td class="leave-type-cell"></td>
                                <td class="reason-cell"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        


                        </table>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                          </form>
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
  @endsection
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="../../Admin/support/js/vendor.bundle.base.js"></script>
  <script src="../../Admin/support/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="../../Admin/js/off-canvas.js"></script>
  <script src="../../Admin/js/hoverable-collapse.js"></script>
  <script src="../../Admin/js/misc.js"></script>
  <script src="../../Admin/js/settings.js"></script>
  <script src="../../Admin/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="../../Admin/js/data-table.js"></script>
  <!-- End custom js for this page-->
  <script>
   function toggleStatus(button) {
    event.preventDefault();

    const currentStatus = button.getAttribute('data-status');
    const row = button.closest('tr'); // Get the row of the clicked button
    const leaveTypeCell = row.querySelector('.leave-type-cell');
    const reasonCell = row.querySelector('.reason-cell');
    const statusInput = row.querySelector('.status-input'); // Find the hidden input for the status

    // Toggle between "P" (Present) and "A" (Absent)
    if (currentStatus === "P") {
        // Switch to Absent
        button.setAttribute('data-status', "A");
        button.textContent = "A";
        button.classList.remove('badge-info');
        button.classList.add('badge-danger');

        // Update the hidden input for status
        statusInput.value = "A";

        // Insert dropdown and input field
        leaveTypeCell.innerHTML = `
            <select class="form-control" name="data[${row.rowIndex - 1}][leave_type]">
                <option value="">Select Leave Type</option>
                <option value="sick">Sick</option>
                <option value="casual">Casual</option>
                <option value="half_day">Half Day</option>
                <option value="maternity">Maternity</option>
                <option value="other">Other</option>
            </select>
        `;

        reasonCell.innerHTML = `
            <input type="text" class="form-control" name="data[${row.rowIndex - 1}][reason]" placeholder="Enter reason">
        `;
    } else {
        // Switch to Present
        button.setAttribute('data-status', "P");
        button.textContent = "P";
        button.classList.remove('badge-danger');
        button.classList.add('badge-info');

        // Update the hidden input for status
        statusInput.value = "P";

        // Clear dropdown and input field
        leaveTypeCell.innerHTML = "";
        reasonCell.innerHTML = "";
    }
}

</script>


</body>


<!-- Mirrored from www.urbanui.com/melody/template/pages/samples/orders.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 15 Sep 2018 06:10:27 GMT -->
</html>
