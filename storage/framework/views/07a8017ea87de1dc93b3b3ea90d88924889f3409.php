



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



        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                
                    <div class="row">
                        <div class="col-md-3">
                            <div class="pagination-dropdown">
                                <label for="per_page">Items per page</label>
                                <form method="GET" action="<?php echo e(route('expenses.index')); ?>">
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

                                        <td><i class="fas fa-dollar-sign"></i><?php echo e($expense->amount); ?></td>

                                        <td><?php echo e($expense->method); ?></td>

                                        <td>

                                        
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit expenses')): ?>
                                            <a href="<?php echo e(url('expenses/edit',$expense->id)); ?>">

                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>

                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete expenses')): ?>
                                            |<a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('expenses/destroy',$expense->id)); ?>">

                                            <i class="fas fa-trash text-danger"></i>                          

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

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing <?php echo e($expenses->firstItem()); ?> to <?php echo e($expenses->lastItem()); ?> of <?php echo e($expenses->total()); ?> entries</p>
                        <?php echo e($expenses->links()); ?>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/expenses/index.blade.php ENDPATH**/ ?>