<?php $__env->startSection('pagescript'); ?>

    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>

    <script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

    <script src="<?php echo e(asset('js/form-repeater.js')); ?>"></script>

@stopbSaloon







<?php $__env->startSection('title', 'Update Company | DiaryMaster '); ?>



<?php $__env->startSection('content'); ?>



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Edit Company

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('companies')); ?>">Companies</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Edit Company</li>

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

                        <h4 class="card-title">Update Company Information</h4>

                        <form class="forms-sample" action="<?php echo e(route('companies.update',$company_detail['0']->company_id)); ?>" method="post">

                            <?php echo csrf_field(); ?>

                            <?php echo method_field('PATCH'); ?>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Name</label>

                                <input type="text" name="company_name" value="<?php echo e($company_detail['0']->company_name); ?>" class="form-control" placeholder="Enter Company Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Smart Name(11 characters max)</label>

                                <input type="text" name="smart_name" value="<?php echo e($company_detail['0']->smart_name); ?>" class="form-control" placeholder="This name will be used in SMS. Max=11 & Min =2 characters allowed" minlength="2" maxlength="11" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Owner Name</label>

                                <input type="text" name="owner_name" value="<?php echo e($company_detail['0']->owner_name); ?>" class="form-control" placeholder="Enter Owner Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Phone</label>

                                <input type="text" name="company_phone" value="<?php echo e($company_detail['0']->company_phone); ?>" class="form-control" placeholder="Enter Company Phone" required/>

                            </div>



                            <div class="form-group">

                                <label for="exampleInputUsername1">Company E-mail</label>

                                

                                <input type="email" name="email" value="<?php echo e($company_detail['0']->useremail); ?>" class="form-control" placeholder="Enter Company Phone" required/>

                            </div>



                            <div class="form-group">

                                <label for="exampleInputUsername1">Company Password</label>

                                <input type="password" id="newPassword" name="password" value="" onkeyup="checkPasswordMatch()" class="form-control" placeholder="Enter Password" />

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Confirm Password</label>

                                <input type="password" id="confirmPassword" value="" onkeyup="checkPasswordMatch()" class="form-control" placeholder="Confirm Password" />

                                <span id="CheckPasswordMatch" class="text-warning"></span>

                            </div>





                            <!-- <div class="form-group">

                                <label for="exampleInputUsername1">Plan Name</label>

                                <input type="text" name="plan_name" value="<?php echo e($company_detail['0']->plan_name); ?>" class="form-control" placeholder="Enter Plan Name" required/>

                            </div>

                            <div class="form-group">

                                <label for="exampleInputUsername1">Plan Price</label>

                                <input type="text" name="plan_price" value="<?php echo e($company_detail['0']->plan_price); ?>" class="form-control" placeholder="Enter Plan Price" required/>

                            </div>





                            <div class="form-group">

                            <label>Plan End Date</label>

                            <div id="datepicker-popup-booking" class="input-group date datepicker">

                                <input type="text" name="plan_end_date" value="<?php echo e($company_detail['0']->plan_end_date); ?>" class="form-control" required>

                                <span class="input-group-addon input-group-append border-left">

                                <span class="far fa-calendar input-group-text"></span>

                                </span>

                            </div>

                            </div> -->



                            <div class="form-group">

                                <label for="exampleTextarea1">Company Address</label>

                                <textarea class="form-control" id="exampleTextarea1" name="company_address" rows="4" placeholder="Enter Company Address"><?php echo e($company_detail['0']->company_address); ?></textarea>

                            </div>



                            <!-- <div class="form-group row">

                                <label class="col-sm-3 col-form-label">Site Settings Permissions</label>

                                <div class="col-sm-4 mt-2">

                                    <div class="form-check">

                                        <label class="form-check-label">

                                            <input type="checkbox" name="message_permission" value="1" class="form-check-input" <?php if($company_detail[0]->message_permissions == 1): ?> checked <?php endif; ?>>

                                            Message Setting

                                        </label>

                                    </div>

                                </div>

                                <div class="col-sm-5 mt-2">

                                    <div class="form-check">

                                        <label class="form-check-label">

                                            <input type="checkbox" name="email_permission" value="1" class="form-check-input" <?php if($company_detail[0]->email_permissions == 1): ?> checked <?php endif; ?>>

                                            E-Mail Settings

                                        </label>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-md-4">

                                    <label>SMS Max Limit:</label>

                                    <input class="form-control" type="text" name="sms_count" value="<?php echo e($company_detail['0']->sms_count); ?>" min="0" placeholder="Enter number of SMS"/>

                                </div>

                                <div class="col-md-4">

                                    <label>Monthly Addition:</label>

                                    <input class="form-control" type="text" name="sms_limit" value="0" min="0" placeholder="Enter number of WhatsApp Notification" />

                                </div>

                            </div> -->

                            <button type="submit" id="add-company" class="btn btn-primary mr-2">Update Company</button>

                            <a class="btn btn-light" href="<?php echo e(route('companies.index')); ?>">Cancel</a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>



<script>

function checkPasswordMatch() 

{

    var password = $("#newPassword").val();

    var confirmPassword = $("#confirmPassword").val();

    if (password != confirmPassword){

        $('#add-company').attr('disabled', true);

        $("#CheckPasswordMatch").html("Passwords does not match!");

    }

    else {

        $('#add-company').attr('disabled', false);

        $("#CheckPasswordMatch").html("");

    }

    

}

</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/companies/edit.blade.php ENDPATH**/ ?>