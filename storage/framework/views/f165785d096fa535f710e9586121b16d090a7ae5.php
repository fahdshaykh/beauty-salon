<?php $__env->startSection('pagescript'); ?>



<script src="<?php echo e(asset('js/calendar.js')); ?>"></script>



<script src="<?php echo e(asset('js/select2.js')); ?>"></script>

<script src="<?php echo e(asset('js/js-grid.js')); ?>"></script>

<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>



<?php $__env->stopSection(); ?>





<?php $__env->startSection('title', 'Dashboard | bSaloon'); ?>







<?php $__env->startSection('content'); ?>



<link rel="stylesheet" href="<?php echo asset('css/jobmodal.css')?>" type="text/css"> 

<style>

.popper,

.tooltipevent {

  position: absolute;

  z-index: 9999;

  background: black;

  color: white;

  width: 250px;

  border-radius: 3px;

  box-shadow: 0 0 2px rgba(0,0,0,0.5);

  padding: 10px;

  text-align: center;

}

.style5 .tooltipevent {

  background: #1E252B;

  color: #FFFFFF;

  max-width: 200px;

  width: auto;

  font-size: .8rem;

  padding: .5em 1em;

}

.fc-event{

    cursor: pointer;

}

.holiday {
    background: lightgray;
    pointer-events: none;
}

      

</style>

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Dashboard

            </h3>

              

          <?php if(Session::has('success')): ?>



            <div class="alert alert-success alert-dismissible">



                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                <?php echo e(Session::get('success')); ?>




            </div>

        <?php endif; ?>

     

        </div>


        <div class="row">



            <div class="col-md-12">



            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                <div class="form-group col-md-3">



                    <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100"



                        style="width:100%">



                        <option value="<?php echo e(auth()->user()->company_id); ?>">All Bookings</option>



                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                        <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>



                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                    </select>



                </div>
            <?php endif; ?>



                <div class="card">


                    <?php 
                        $permvalue = 'no'; 
                    ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('calendar create booking')): ?>
                    <?php 
                        $permvalue = 'yes'; 
                    ?>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view calendar')): ?>
                    <div class="card-body">

                    

                    <input type="hidden" id="creat-booking-perm" value="<?php echo e($permvalue); ?>">

                        <h4 class="card-title">Booking Calender</h4>



                        <div id="calendar" class="full-calendar"></div>



                    </div>
                    <?php endif; ?>



                </div>



            </div>



        </div>



    </div>



    <!-- content-wrapper ends -->



</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                    

</div>

<!-- main-panel ends -->



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/home.blade.php ENDPATH**/ ?>