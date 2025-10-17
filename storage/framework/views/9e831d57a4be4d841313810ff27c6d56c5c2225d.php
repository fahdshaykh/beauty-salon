







<?php $__env->startSection('title', 'Services | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Services



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(route('services.index')); ?>">Services</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Services Listing</li>



                </ol>



            </nav>



        </div>







        <div class="alert alert-success" style="display:none;" id="status-success"></div>







        <?php if(Session::has('success')): ?>



        <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            <?php echo e(Session::get('success')); ?>




        </div>







        <?php elseif(Session::has('destroy')): ?>



        <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            <?php echo e(Session::get('destroy')); ?>




        </div>



        <?php endif; ?>







        <?php if(Session::has('update')): ?>



        <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            <?php echo e(Session::get('update')); ?>




        </div>



        <?php endif; ?>







        <?php $service_edit_array = array(2,3); ?>



        <?php $service_edit = implode(',', $service_edit_array); ?>



        <?php $service_delete_array = array(2,4); ?>



        <?php $service_delete = implode(',', $service_delete_array); ?>







        <div class="card">



            <div class="card-body">



                <h4 class="card-title"></h4>



                <div class="row">



                    <div class="col-12">



                        <div class="table-responsive">



                            <table class="table table-striped">



                                <thead>



                                    <tr>

                                        <th>Category Name</th>

                                        <th>Service Name</th>

                                        <th>Service Price</th>

                                        <th>Created at</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($service->categories->name); ?></td>

                                        <td><?php echo e($service->name); ?></td>

                                        <td><i class="fas fa-pound-sign"></i><?php echo e($service->price); ?></td>

                                        <td><?php echo e(date('m-d-Y', strtotime($service->created_at))); ?></td>

                                        <td>

                                            <label class="switch">



                                                <input type="checkbox" id="user-<?php echo e($service->id); ?>"



                                                    onclick="changeServiceStatus(event.target, <?php echo e($service->id); ?>);"



                                                    <?php echo e(($service->is_active) ? 'checked' : ''); ?>>



                                                <span class="slider round"></span>



                                            </label>

                                        </td>

                                        <td>

                                        <?php if(in_array($service_edit, session()->get('all_rp')['0'])): ?>

                                            <a href="<?php echo e(route('services.edit',$service->id)); ?>">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>

                                        <?php endif; ?> 

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



    <?php $__env->stopSection(); ?>







    <script>

    function changeServiceStatus(_this, id) {







        var status = $(_this).prop('checked') == true ? 1 : 0;



        $.ajax({



            url: "<?php echo e(url('servicestatus')); ?>",



            type: 'GET',



            data: {



                id: id,



                status: status



            },



            success: function(result) {



                $("#status-success").show();



                $("#status-success").html(result.success);



                setTimeout(function() {



                    $("#status-success").hide()



                }, 3000);



            }



        });



    }

    </script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/services/index.blade.php ENDPATH**/ ?>