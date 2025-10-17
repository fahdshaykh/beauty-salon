

<?php $__env->startSection('title', 'Customers | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>

<style>
    .pagination-dropdown {
        max-width: 200px;
        margin-bottom: 20px;
    }

    .pagination-dropdown label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .pagination-dropdown select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 10px 40px 10px 15px;
        font-size: 0.95rem;
        color: #495057;
        width: 100%;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23495057' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
    }

    .pagination-dropdown select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
    }

    .pagination-dropdown select:hover {
        border-color: #007bff;
    }

    .pagination-dropdown option {
        padding: 10px;
    }

    @media (max-width: 576px) {
        .pagination-dropdown {
            max-width: 100%;
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Customers</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('customers.index')); ?>">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Customers Listing</li>
                </ol>
            </nav>
        </div>

        <div class="alert alert-success" style="display:none;" id="status-success"></div>

        <?php if(Session::has('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <?php echo e(Session::get('success')); ?>

        </div>
        <?php elseif(Session::has('destroy')): ?>
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <?php echo e(Session::get('destroy')); ?>

        </div>
        <?php endif; ?>

        <?php if(Session::has('update')): ?>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <?php echo e(Session::get('update')); ?>

        </div>
        <?php endif; ?>

        <?php $customer_edit_array = array(3, 3); ?>
        <?php $customer_edit = implode(',', $customer_edit_array); ?>
        <?php $customer_delete_array = array(3, 4); ?>
        <?php $customer_delete = implode(',', $customer_delete_array); ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h4 class="card-title">Customers Listing</h4>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3">
                        <div class="text-center" style="float: center;">
                            <span data-href="/all-customer-export" id="export" class="btn btn-primary" onclick="exportTasks(event.target);">Export Customers</span>
                        </div>
                    </div>
                </div>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="<?php echo e(route('customers.index')); ?>">
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
                    <div class="col-12 mt-2">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Country Code</th>
                                        <th>Primary Phone</th>
                                        <th>Date Of Birth</th>
                                        <th>E-Mail</th>
                                        <th>Address</th>
                                        <th>Referred By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><a href="<?php echo e(url('bookings-history', $customer->id)); ?>"> <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?> </a></td>
                                        <td><?php echo e($customer->country_code); ?></td>
                                        <td><?php echo e($customer->primary_phone); ?></td>
                                        <td><?php echo e($customer->date_of_birth); ?></td>
                                        <td><?php echo e($customer->email); ?></td>
                                        <td><?php echo e($customer->address); ?></td>
                                        <td><?php echo e($customer->referred_by); ?></td>
                                        <td>
                                            <?php if(in_array($customer_edit, session()->get('all_rp')['0'])): ?>
                                            <a href="<?php echo e(route('customers.edit', $customer->id)); ?>">
                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if(in_array($customer_delete, session()->get('all_rp')['0'])): ?>
                                            | <a onclick="return confirm('Are you sure to delete');" href="<?php echo e(url('customers/destroy', $customer->id)); ?>">
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
                        <p>Showing <?php echo e($customers->firstItem()); ?> to <?php echo e($customers->lastItem()); ?> of <?php echo e($customers->total()); ?> entries</p>
                        <?php echo e($customers->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<script>
function changeCustomerStatus(_this, id) {
    var status = $(_this).prop('checked') == true ? 1 : 0;
    $.ajax({
        url: "<?php echo e(url('customerstatus')); ?>",
        type: 'GET',
        data: {
            id: id,
            status: status
        },
        success: function(result) {
            $("#status-success").show();
            $("#status-success").html(result.success);
            setTimeout(function() {
                $("#status-success").hide();
            }, 3000);
        }
    });
}

function exportTasks(_this) {
    let _url = $(_this).data('href');
    window.location.href = _url;
}
</script>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/customers/index.blade.php ENDPATH**/ ?>