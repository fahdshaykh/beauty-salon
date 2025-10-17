<?php $__env->startSection('pagescript'); ?>

<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>

<script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>

<script src="<?php echo e(asset('js/select2.js')); ?>"></script>

<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Create Role | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Create Role

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('/roles')); ?>">Roles</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create Role</li>

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



	<?php if(Session::has('validate')): ?>



        <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            <?php echo e(Session::get('validate')); ?>




        </div>



	<?php endif; ?>



        <div class="row">

            <div class="col-lg-12">

                <div class="card px-3">

                    <div class="card-body">

                      <form class="forms-sample" action="<?php echo e(route('role.store')); ?>" method="post">

                        <h4 class="card-title">Add Role & Permissions</h4>

                        <div class="add-items d-flex">

                            <input type="text" name="role" class="form-control todo-list-input" placeholder="Enter Role Name">

                            <input type="submit" value="Add" class="add btn btn-primary">

                        </div>

                        <div class="col-lg-12 grid-margin stretch-card">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                      <div class="col-md-3">

                                        <h4 class="card-title">Set Permissions</h4>

                                      </div>

                                      <div class="col-md-6"></div>

                                        <div class="col-md-3">

                                              <div class="form-check form-check-primary">

                                                <label class="form-check-label">

                                                    <input type="checkbox" onclick="toggle(this);" value="1" class="form-check-input">

                                                    Check All

                                                </label>

                                              </div>

                                        </div>

                                    </div>

                                    

                                    <div class="table-responsive">

                                        <table class="table table-bordered">

                                            <thead>

                                                <tr>

                                                    <th>Modules</th>

                                                    <th>Read</th>

                                                    <th>Create</th>

                                                    <th>Edit</th>

                                                    <th>Delete</th>

                                                    <th colspan=3>Others</th>

                                                    <!-- <th>Export</th>

                                                    <th>Graph</th> -->

                                                </tr>

                                            </thead>

                                            <tbody>

                                            

                                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <tr>

                                                    <td class="py-1">

                                                        <div class="form-check form-check-primary">

                                                            <?php echo e($permission[0]->name); ?>


                                                        </div>

                                                    </td>

                                                    <?php $access_level = 1; ?>

                                                    <?php $__currentLoopData = $permission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                                                    <td>

                                                        <div class="form-check form-check-primary">

                                                            

                                                            <label class="form-check-label">

                                                                <input type="checkbox" name="access_id[<?php echo e($perm->permission_id); ?>][]" value="<?php echo e($access_level); ?>" class="form-check-input"> 

                                                            </label>

                                                           

                                                        </div>

                                                        <?php if($perm->permission_id==5 && $access_level==1)

                                                            { echo "Booking Report";  }

                                                            elseif($perm->permission_id==5 && $access_level==2) { echo "Export BR"; }

                                                            elseif($perm->permission_id==5 && $access_level==3){ echo "Customer report"; }

                                                            elseif($perm->permission_id==5 && $access_level==4){ echo "Export CR"; }

                                                            elseif($perm->permission_id==5 && $access_level==5){ echo "Expense Report"; }

                                                            elseif($perm->permission_id==5 && $access_level==6){ echo "Export ER"; }

                                                            elseif($perm->permission_id==5 && $access_level==7){ echo "Graph Report"; }

                                                            elseif($perm->permission_id==4 && $access_level==5){ echo "View Invoice"; }

                                                            elseif($perm->permission_id==3 && $access_level==5){ echo "Export Customer"; }

                                                            elseif($perm->permission_id==16 && $access_level==2){ echo "Send Message"; }

                                                            elseif($perm->permission_id==16 && $access_level==3){ echo "Send PostCard"; }

                                                            elseif($perm->permission_id==16 && $access_level==4){ echo "View PostCard History"; }

                                                            elseif($perm->permission_id==17 && $access_level==2){ echo "General Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==3){ echo "Review SMS"; }

                                                            elseif($perm->permission_id==17 && $access_level==4){ echo "Email Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==5){ echo "Tax Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==6){ echo "PostCard Settings"; }

                                                             ?>

                                                    </td>

                                                    <?php $access_level++ ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </tr>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                      <?php echo csrf_field(); ?>

                      </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<script>

function toggle(source) {

  var checkboxes = document.querySelectorAll('input[type="checkbox"]');

  for (var i = 0; i < checkboxes.length; i++) {

      if (checkboxes[i] != source)

      checkboxes[i].checked = source.checked;

  }

}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/roles/create.blade.php ENDPATH**/ ?>