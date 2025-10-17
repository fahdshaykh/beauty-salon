



<?php $__env->startSection('title', 'Companies | Company Management'); ?>



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

                Companies

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(route('companies.index')); ?>">Companies</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Company Listing</li>

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

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive"> <!-- style="overflow: hidden !important;" -->

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr>

                                        <th>Company Name</th>

                                        <th>Owner Name</th>

                                        <th>Admin User</th>

                                        <th>Company Phone</th>

                                        <!-- <th>Plan Name</th> -->

                                        <th>Plan price</th>

                                        <!-- <th>SMS Count</th> -->

                                        <!-- <th>WhatsApp Count</th> -->

                                        <th>End Date</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($company->company_name); ?></td>

                                        <td><?php echo e($company->owner_name); ?></td>

                                        <td style="max-width:150px;"><?php echo e($company->email); ?></td>

                                        <td><?php echo e($company->company_phone); ?></td>

                                        <!-- <td><?php echo e($company->plan_name); ?></td> -->

                                        

                                        <td><i class="fas fa-pound-sign"></i><?php echo e($company->plan_price); ?></td>

                                        <!-- <td><?php echo e($company->sms_count); ?></td> -->

                                        <td><?php echo e(date('m-d-Y', strtotime($company->plan_end_date))); ?></td>

                                        <td>

                                            <label class="switch">

                                                <input type="checkbox" id="user-<?php echo e($company->id); ?>"

                                                    onclick="changeCompanyStatus(event.target, <?php echo e($company->id); ?>);"

                                                    <?php echo e(($company->is_active) ? 'checked' : ''); ?>>

                                                <span class="slider round"></span>

                                            </label>

                                        </td>

                                        <td>

                                            <a href="<?php echo e(route('companies.edit',$company->id)); ?>">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>

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

    function changeCompanyStatus(_this, id) {



        var status = $(_this).prop('checked') == true ? 1 : 0;

        $.ajax({

            url: "<?php echo e(url('company-status')); ?>",

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/companies/index.blade.php ENDPATH**/ ?>