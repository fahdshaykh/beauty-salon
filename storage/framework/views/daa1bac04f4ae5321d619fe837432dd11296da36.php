



<?php $__env->startSection('title', 'Expenses | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Expenses

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('expenses/index')); ?>">Expenses</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expenses Listing</li>

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



        <?php $expenses_array = array(9,3); ?>

        <?php $expenses_edit = implode(',', $expenses_array); ?>

        <?php $expenses_array = array(9,4); ?>

        <?php $expenses_delete = implode(',', $expenses_array); ?>



        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                      

                                        <th>Expense Type</th>

                                        <th>Date Paid</th>

                                        <th>Amount</th>

                                        <th>Payment Method</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                   

                                    <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                   

                                    <tr>

                                      

                                        <td><?php echo e($expense->type); ?></td>

                                        <td><?php echo e($expense->payable); ?></td>

                                        <td><i class="fas fa-pound-sign"></i><?php echo e($expense->amount); ?></td>

                                        <td><?php echo e($expense->method); ?></td>

                                        <td>

                                        

                                            <a href="<?php echo e(url('expenses/edit',$expense->id)); ?>">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>
                                            |<a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('expenses/destroy',$expense->id)); ?>">

<i class="fas fa-trash text-danger"></i>                          

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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/expenses/index.blade.php ENDPATH**/ ?>