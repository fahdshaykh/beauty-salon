







<?php $__env->startSection('title', 'Users | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>



<style>



.switch {



    position: relative;



    display: inline-block;



    width: 60px;



    height: 34px;



}







.switch input {



    opacity: 0;



    width: 0;



    height: 0;



}







.slider {



    position: absolute;



    cursor: pointer;



    top: 0;



    left: 0;



    right: 0;



    bottom: 0;



    background-color: #ccc;



    -webkit-transition: .4s;



    transition: .4s;



}







.slider:before {



    position: absolute;



    content: "";



    height: 26px;



    width: 26px;



    left: 4px;



    bottom: 4px;



    background-color: white;



    -webkit-transition: .4s;



    transition: .4s;



}







input:checked+.slider {



    background-color: #392C70;



}







input:focus+.slider {



    box-shadow: 0 0 1px #2196F3;



}







input:checked+.slider:before {



    -webkit-transform: translateX(26px);



    -ms-transform: translateX(26px);



    transform: translateX(26px);



}







/* Rounded sliders */



.slider.round {



    border-radius: 34px;



}







.slider.round:before {



    border-radius: 50%;



}



</style>



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Users



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(route('users.index')); ?>">Users</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Users Listing</li>



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



        <?php if(session('error')): ?>

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(session('error')); ?>


        </div>

        <?php endif; ?>















        <div class="card">



            <div class="card-body">



                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">User Listing</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center" style="float: center;">






                                <a href="<?php echo e(url('user/user-create')); ?>" class="btn btn-primary btn-sm">Add New User</a>






                            </div>



                        </div>



                        </div>


                    <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="<?php echo e(route('users.index')); ?>">
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



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>



                                        <th>User Name</th>

                                        <th>User Email</th>

                                        <th>User phone</th>

                                        <th>User address</th>

                                        <th>Status</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                                    <?php if($user->role_id !== 1): ?>



                                    <tr>



                                        <td><?php echo e($num); ?></td>






                                        <td> <?php echo e($user->name); ?> </td>

                                        <td><?php echo e($user->email); ?></td>

                                        <td><?php echo e($user->phone); ?></td>

                                        <td><?php echo e($user->address); ?></td>

                                        <td>







                                            <label class="switch">



                                                <input type="checkbox" id="user-<?php echo e($user->id); ?>"



                                                    onclick="changeUserStatus(event.target, <?php echo e($user->id); ?>);"



                                                    <?php echo e(($user->is_active) ? 'checked' : ''); ?>>



                                                <span class="slider round"></span>



                                            </label>



                                        </td>



                                        <td>







                                            <a href="<?php echo e(url('user/edit',$user->id)); ?>">



                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>



                                            </a>



                                            |<a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('user/destroy',$user->id)); ?>">



                                                <i class="fas fa-trash text-danger"></i>



                                            </a>




                                        |<a href="<?php echo e(url('user/payslip',$user->id)); ?>">



                                        <i class='fas fa-file-pdf ml-2' style='font-size:14px'></i>



                                        </a>



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

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing <?php echo e($users->firstItem()); ?> to <?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> entries</p>
                        <?php echo e($users->links()); ?>

                    </div>
                </div>


            </div>



        </div>



    </div>



    <?php $__env->stopSection(); ?>







    <script>



    function changeUserStatus(_this, id) {







        var status = $(_this).prop('checked') == true ? 1 : 0;



        $.ajax({



            url: "<?php echo e(url('user-status')); ?>",



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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/users/index.blade.php ENDPATH**/ ?>