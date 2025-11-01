







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







        <div class="card">



            <div class="card-body">



                <h4 class="card-title"></h4>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="<?php echo e(route('services.index')); ?>">
                                <?php $__currentLoopData = request()->except(['page', 'per_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="form-control">
                                    <?php $__currentLoopData = $paginationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($option); ?>" <?php echo e(request('per_page', auth()->user()->getPerPagePreference()) == $option ? 'selected' : ''); ?>><?php echo e($option); ?> per page</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>



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

                                        <td><i class="fas fa-dollar-sign"></i><?php echo e($service->price); ?></td>

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

                                        <div class="btn-group align-top">

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit services')): ?>
                                            <a href="<?php echo e(route('services.edit',$service->id)); ?>" class="btn btn-default">
                                                    <i class="fas fa-pen" style="font-size:15px;"></i>
                                            </a>
                                            <?php endif; ?>


                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete services')): ?>
                                            <form method="post" action="<?php echo e(route('services.destroy', $service->id)); ?>" onsubmit="return confirm('Are You Sure Want To Delete?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-trash-alt" style="font-size:15px; color:red;"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>


                                        </div>
                                        </td>

                                    </tr>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>



                            </table>



                        </div>



                    </div>



                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing <?php echo e($services->firstItem()); ?> to <?php echo e($services->lastItem()); ?> of <?php echo e($services->total()); ?> entries</p>
                        <?php echo e($services->links()); ?>

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/services/index.blade.php ENDPATH**/ ?>