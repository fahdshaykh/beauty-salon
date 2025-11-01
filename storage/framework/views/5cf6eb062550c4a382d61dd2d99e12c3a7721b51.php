<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Create New Award | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>
      <!-- partial -->
      <div class="main-panel">          
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              Give an Award
            </h3>
           
          </div>
          
                  <div class="row">
                    <div class="col-12">
                     
                      <form action="<?php echo e(route('awards.store')); ?>" method="POST" >
                      <?php echo csrf_field(); ?>
                     

         <!-- Attendance Date -->
                      
         <div class="form-group">
    <label>Awardee Name</label>
    <select class="form-control" id="name" name="name">
        <option value="" disabled selected>Please Select Awardee Name</option>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
                     <div class="form-group">
                      <label for="award">Award Name:</label>
                     <input type="text" id="award" name="award"  class="form-control" required >
                     </div>
                     <div class="form-group">
                      <label for="gift">Gift:</label>
                     <input type="text" id="gift" name="gift"  class="form-control" required >
                     </div>
                     <div class="form-group">
                      <label for="date">Date:</label>
                     <input type="date" id="date" name="date"  class="form-control" required >
                     </div>

                        <div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </div>
                       
                        </form>
                      
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
  


</body>


<!-- Mirrored from www.urbanui.com/melody/template/pages/samples/orders.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 15 Sep 2018 06:10:27 GMT -->
</html>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/awards/create.blade.php ENDPATH**/ ?>