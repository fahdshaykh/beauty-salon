







<?php $__env->startSection('title', 'Roles | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>







<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Roles



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(url('/roles')); ?>">Roles</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Role Listing</li>



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



                    <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title"></h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center">



                                <?php if(in_array("7,2", session()->get('all_rp')['0'])): ?>



                                <a href="<?php echo e(url('role/role-create')); ?>" class="btn btn-primary btn-sm">Add New Role</a>



                                <?php endif; ?>



                            </div>



                        </div>



                    </div>



                <div class="row">



                    <div class="col-12 mt-3">



                        <div class="table-responsive">



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>



                                        <th>Role Name</th>



                                        <th>Created at</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    <?php $__currentLoopData = $role_permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                                    <?php if($role->role_id !== 1): ?>



                                    <tr>



                                        <td><?php echo e($num); ?></td>



                                        <td><?php echo e($role->role->role); ?></td>



                                        <td><?php echo e(date('m-d-Y', strtotime($role->role->created_at))); ?></td>



                                        <td>



                                        <?php if(in_array("7,3", session()->get('all_rp')['0'])): ?>



                                            <a href="<?php echo e(url('role/edit',$role->role_id)); ?>">



                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>



                                            </a>



                                        <?php endif; ?>



                                        </td>



                                    </tr>



                                    <?php $num++; ?>



                                    <?php endif; ?>



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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/roles/index.blade.php ENDPATH**/ ?>