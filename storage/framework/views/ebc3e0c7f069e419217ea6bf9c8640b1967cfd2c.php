<?php $__env->startSection('pagescript'); ?>



<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>



<script src="<?php echo e(asset('js/typeahead.js')); ?>"></script>



<script src="<?php echo e(asset('js/select2.js')); ?>"></script>



<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<script>
$(document).ready(function() {
    // Function to update amount_pending
    function updatePendingAmount() {
        let totalPrice = parseFloat($('#price').val()) || 0;
        let amountPaid = parseFloat($('input[name="amount_paid"]').val()) || 0;
        let amountPending = totalPrice - amountPaid;

        $('input[name="amount_pending"]').val(amountPending >= 0 ? amountPending : 0);
    }

    // Bind event listeners
    $('#price, input[name="amount_paid"]').on('input', function() {
        updatePendingAmount();
    });
});
</script>


<?php $__env->stopSection(); ?>















<?php $__env->startSection('title', 'Create User | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>











<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



               Create New Inventory



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(url('/users')); ?>">Inventories</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Create New Inventory</li>



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



        <div class="row">



            <div class="col-12 grid-margin stretch-card">



                <div class="card">



                  <div class="card-body">



                    <h4 class="card-title">Create Inventory</h4>



                    <form class="forms-sample" action="<?php echo e(route('inventories_purchases.store')); ?>" method="post" enctype="multipart/form-data">



                    <?php echo csrf_field(); ?>



                      <div class="form-group">

                        <label for="name">Select Item</label>

                        <select class="form-control" id="name" name="inventory_id">
                           <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <option value="<?php echo e($inventory->id); ?>"><?php echo e($inventory->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         </select>

                      </div>



                      <div class="form-group">



                        <label for="quantity">Quantity</label>



                        <input type="number" class="form-control" name="quantity" value="<?php echo e(old('quantity')); ?>" id="quantity" placeholder="quantity">



                      </div>

                      



                      <div class="form-group">



                        <label for="price">Total Price</label>



                        <input type="number" class="form-control" name="price" id="price" placeholder="Enter price">



                      </div>


                      <div class="form-group">

                        <label for="name">Select Supplier</label>

                        <select class="form-control" id="name" name="supplier_id">
                           <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         </select>

                      </div>



                      <!-- <div class="form-group">



                        <label>File upload</label>



                        <input type="file" name="image" class="file-upload-default">



                        <div class="input-group col-xs-12">



                          <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">



                          <span class="input-group-append">



                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>



                          </span>



                        </div>



                      </div> -->

                      <div class="form-group">

                        <label for="name">Select Supplier</label>

                        <select class="form-control" id="name" name="paypemnt_type">
                           
                             <option value="cash">Cash</option>

                             <option value="check">Check</option>
                            
                         </select>

                      </div>

                      <div class="form-group">



                      <label for="price">Amount Paid</label>



                      <input type="number" class="form-control" name="amount_paid" id="price" placeholder="Enter Amount Paid">



                      </div>

                      <div class="form-group">



                      <label for="price">Amount Pending</label>



                      <input type="number" class="form-control" name="amount_pending" id="price" disabled>



                      </div>

                      <div class="form-group">



                            <label for="exampleTextarea1">Description</label>



                            <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"></textarea>



                      </div>



                      <button type="submit" class="btn btn-primary mr-2">Submit</button>



                      <a class="btn btn-light" href="<?php echo e(route('inventories.index')); ?>">Cancel</a>



                    </form>



                  </div>



                </div>



              </div>



        </div>



    </div>







    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/inventories/inventories_purchase/create.blade.php ENDPATH**/ ?>