









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



                All Awards



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(route('awards.index')); ?>"> Awards </a></li>



                    <li class="breadcrumb-item active" aria-current="page">Awards Listing</li>



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



                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="<?php echo e(route('awards.index')); ?>">
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



                    <div class="col-md-3">



                    <h4 class="card-title">Supplier Listing</h4>



                    </div>



                    <div class="col-md-6"></div>



                    <div class="col-md-3">



                        <div class="text-center" style="float: center;">



                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create awards')): ?>
                            <a href="<?php echo e(route('awards.create')); ?>" class="btn btn-primary btn-sm">Add Award fade</a>
                        <?php endif; ?>


                        </div>



                    </div>



                </div>



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
                                    <th>Actions</th>
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
                                    
                                    <td>
                                        <div class="btn-group align-top">

                                        

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit awards')): ?>
                                            <a href="<?php echo e(route('awards.edit',$award->id)); ?>" class="btn btn-default">
                                                    <i class="fas fa-pen" style="font-size:15px;"></i>
                                            </a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete awards')): ?>
                                            <form method="post" action="<?php echo e(route('awards.destroy', $award->id)); ?>" onsubmit="return confirm('Are You Sure Want To Delete?');">
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
                        <p>Showing <?php echo e($awards->firstItem()); ?> to <?php echo e($awards->lastItem()); ?> of <?php echo e($awards->total()); ?> entries</p>
                        <?php echo e($awards->links()); ?>

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/awards/index.blade.php ENDPATH**/ ?>