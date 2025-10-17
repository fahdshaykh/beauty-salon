<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

    <script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Award Listing | '.Session::get('site_name')); ?>



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
                <li class="breadcrumb-item active" aria-current="page">Award</li>
              </ol>
            </nav>
          </div>
          <li class="nav-item d-none d-lg-flex">
            <a class="nav-link" href="<?php echo e(url('award/create')); ?>">
              <span class="btn btn-primary">+ Add New Award</span>
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
                        <table class="table">
                          <thead>
                            <tr class="bg-primary text-white">
                                <th>#</th>
                                <th>Awardee Name</th>
                                <th>Award</th>
                                <th>Gift</th>
                                <th>Date</th>
                                
                            </tr>
                          </thead>
                          <tbody>
                          
                          <?php $__currentLoopData = $awards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $award): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e(App\User::where('id', $award->name)->value('name')); ?></td>


                                <td><?php echo e($award->award); ?></td>
                                <td><?php echo e($award->gift); ?></td>
                                <td><?php echo e($award->date); ?></td>
                               
                                
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/award/index.blade.php ENDPATH**/ ?>