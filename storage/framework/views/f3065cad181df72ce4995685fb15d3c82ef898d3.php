<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Create New Service | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>

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
            <a class="nav-link" href="<?php echo e(route('employee_attendance')); ?>">
              <span class="btn btn-primary">+ Mark Attendance</span>
            </a>
          </li>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Table</h4>
                 
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                      <div class="mb-3">
  <label for="searchByDate" class="form-label">Search by Date</label>
  <input type="date" id="searchByDate" class="form-control" placeholder="Select a date">
</div>

<table class="table">
  <thead>
      <tr class="bg-primary text-white">
          <th>#</th>
          <th>Employee ID</th>
          <th>Name</th>
          <th>Status</th>
          <th>Leave Type</th>
          <th>Reason</th>
          <th>Entry Time</th>
          <th>Exit Time</th>
          <th>Edit Exit Time</th>
      </tr>
  </thead>
  <tbody>
      <?php
          use Carbon\Carbon;
          $today = Carbon::today()->toDateString(); // Get today's date in Y-m-d format
          $employees = DB::table('employee_attendances')
                          ->whereDate('created_at', $today)
                          ->get();
      ?>

      <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
          <td><?php echo e($loop->iteration); ?></td>
          <td><?php echo e($employee->user_id); ?></td>
          <td><?php echo e($employee->name); ?></td>
          <td><?php echo e($employee->status); ?></td>
          <td><?php echo e($employee->leave_type); ?></td>
          <td><?php echo e($employee->reason); ?></td>
          <td><?php echo e($employee->created_at); ?></td>
          <td id="updated_at_<?php echo e($employee->user_id); ?>"><?php echo e($employee->updated_at); ?></td>
          <td>
            <button 
            class="btn btn-primary btn-sm update-exit-time-btn" 
            data-id="<?php echo e($employee->user_id); ?>" 
            id="updateExitTimeBtn_<?php echo e($employee->user_id); ?>">
            Update
        </button>
          </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<div class="modal fade" id="updateExitTimeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Exit Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="exitTimeForm">
          <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
          <input type="hidden" id="employeeId">
          <div class="mb-3">
            <label for="employeeExitTime" class="form-label">Exit Time</label>
            <input type="datetime-local" class="form-control" id="employeeExitTime" name="updated_at">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="saveExitTimeBtn" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>


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
  
<!-- Bootstrap CSS in the <head> -->


<!-- Bootstrap JS and Popper.js just before closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="path/to/your/javascript.js"></script>
  
</body>
</html>


</body>


<!-- Mirrored from www.urbanui.com/melody/template/pages/samples/orders.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 15 Sep 2018 06:10:27 GMT -->
</html>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const updateButtons = document.querySelectorAll('.update-exit-time-btn');
    
    updateButtons.forEach(button => {
      button.addEventListener('click', function () {
        const userId = this.getAttribute('data-id');
        document.getElementById('employeeId').value = userId;
        const modal = new bootstrap.Modal(document.getElementById('updateExitTimeModal'));
        modal.show();
      });
    });
  
    document.getElementById('saveExitTimeBtn').addEventListener('click', function () {
      const userId = document.getElementById('employeeId').value;
      const updatedAt = document.getElementById('employeeExitTime').value;
  
      console.log('Sending request with:', { user_id: userId, updated_at: updatedAt });
  
      fetch('<?php echo e(route("update.exit.time")); ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          user_id: userId,
          updated_at: updatedAt,
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        if (data.success) {
          document.getElementById(`updated_at_${userId}`).innerText = data.updated_at;
          const modalElement = document.getElementById('updateExitTimeModal');
          const modal = bootstrap.Modal.getInstance(modalElement);
          modal.hide();
          document.getElementById('exitTimeForm').reset();
          alert('Exit time updated successfully!');
        } else {
          alert(data.message || 'Failed to update exit time.');
        }
      })
      .catch(error => {
        console.error('Error updating exit time:', error);
        alert('Error updating exit time. Please check the console for details.');
      });
    });
  });
  </script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/attendance.blade.php ENDPATH**/ ?>