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

<div class="form-group">

    <label for="exampleInputUsername1">Expense Type</label>

    <input type="text" name="type" value="<?php if(isset($expense_type->type)): ?><?php echo e($expense_type->type); ?><?php else: ?><?php echo e(old('type')); ?><?php endif; ?>" class="form-control" placeholder="Enter Expense Ttype" required/>

</div>

<div class="form-group">

    <label for="exampleTextarea1">Discription</label>

    <textarea class="form-control" id="exampleTextarea1" name="description" rows="4"><?php if(isset($expense_type->description)): ?><?php echo e($expense_type->description); ?><?php else: ?><?php echo e(old('description')); ?><?php endif; ?></textarea>

</div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>

<a class="btn btn-light" href="<?php echo e(url('expense-types')); ?>">Cancel</a><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/expense_types/expense_type_form.blade.php ENDPATH**/ ?>