<?php $__env->startSection('pagescript'); ?>

<link rel="stylesheet" href="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.css')); ?>">

<script src="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.min.js')); ?>"></script>

<script src="<?php echo e(asset('js/editorDemo.js')); ?>"></script>

<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<script src="<?php echo e(asset('js/form-addons.js')); ?>"></script>

<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'E-Mail History | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>

<style>

  .hide

{

 display:none;

}

.loader{

    left:0;

    right:0;

    z-index: 999;

    position: fixed;

    border:none;

    width:100%;

    top:200px;

    

}



</style>

<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Email Marketing History

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item active" aria-current="page">Email Marketing History</li>

                </ol>

            </nav>

        </div>

        <?php if($errors->any()): ?>

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <ul>

                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <li><?php echo e($error); ?></li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </ul>

        </div>

        <?php endif; ?>



        <?php if(Session::has('success')): ?>

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('success')); ?>


        </div>

        <?php endif; ?>



        <div class="row">

            <div class="col-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Email Marketing History </h4>

                        <div class="row">

                        <form class="form-inline" method="post" action="<?php echo e(url('email-history-filter')); ?>">

                        <?php echo csrf_field(); ?>

                            <div class="col-lg-3">

                            <div class="input-group mb-2 mr-sm-2">

                                    <div id="datepicker-popup-new" class="input-group date datepicker">

                                        <input type="text" name="start_date" value="<?php if($start_date!=''): ?> <?php echo e($start_date); ?> <?php endif; ?>" placeholder=" Start Date" class="form-control"/>

                                        <span class="input-group-addon input-group-append border-left">

                                            <span class="far fa-calendar input-group-text"></span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-3">

                            <div class="input-group mb-2 mr-sm-2">

                                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                                        <input type="text" name="end_date" value="<?php if($start_date!=''): ?> <?php echo e($end_date); ?> <?php endif; ?>"placeholder="End Date" class="form-control"/>

                                        <span class="input-group-addon input-group-append border-left">

                                            <span class="far fa-calendar input-group-text"></span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                         

                            <div class="col-lg-2">

                                <div class="form-group">

                                    <select name="type" class="form-control js-example-basic-multiple w-100">

                                    <option value="all" <?php if($type=='all'): ?> selected <?php endif; ?>>All</option>

                                        <option value="sent" <?php if($type=='sent'): ?> selected <?php endif; ?>>Sent</option>

                                        <!-- <option value="schedule" <?php if($type=='schedule'): ?> selected <?php endif; ?>>Scheduled</option> -->

                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-4">

                                <div class="form-group">

                                    <button type="submit" value="submit" class="btn btn-primary mb-2">Search</button>

                                </div>

                            </div>

                        </form>  

                        </div>

                        <br>

                        <!-- <div   class="form-check form-check-flat form-check-primary mt-2 mr-5" style="float: left;">

                                <label class="form-check-label">

                                <input id="select-all" type="checkbox" class="form-check-input" name="customer-check">

                                    Scheduled

                                <i class="input-helper"></i></label>

                             </div> -->

                        <table id="customer-listing" class="table table-bordered  table-hover">             

                                <thead>

                                    <tr class="bg-primary text-white">

                                      

                                        <th>Email#</th>

                                        <th>Template</th>

                                        <th>Date</th>

                                        <th>Time </th>

                                        <th>Type</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if($history): ?>

                                        <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        

                                        <tr>    

                                            <td><?php echo e($data->email); ?></td>

                                            <td><?php if($data->template==0): ?> Created <?php else: ?> <?php echo e($data->template); ?> <?php endif; ?></td>

                                            <td><?php echo e($data->date); ?></td>

                                            <td><?php echo e($data->time); ?></td>

                                            <td><?php echo e($data->type); ?></td>

                                    

                                        </tr>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php else: ?>

                                    <tr><td>No data found</td></tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<script>

$(document).ready(function() {

    $("#message").click(function() {

        if ($('.custom-check').is(":checked"))

        {

            var customers = [];

            $('#message-modal').modal('toggle');

            $("input:checkbox[name=customer-checkbox]:checked").each(function(){

                customers.push($(this).val());

            });

            $("#customers_emails").val(customers);

        }

        else{

            alert("Please Select Customer.");

        }

    });



    $("#select-all").click(function() {

        var checkBoxes = $('.custom-check');

        checkBoxes.prop("checked", !checkBoxes.prop("checked"));

    });



    $("#check_schedule").click(function() {

      if ($('#check_schedule').is(":checked"))

      {

        $('#schedule_time_div').removeClass("hide");

        $('#schedule_t').prop('required',true);

        $('#schedule_d').prop('required',true);

      }

      else{

        $('#schedule_time_div').addClass("hide");

        $('#schedule_t').prop('required',false);

        $('#schedule_d').prop('required',false);

      }

  });

});

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/power_mailing/history.blade.php ENDPATH**/ ?>