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
    <label for="exampleInputUsername1">Category Name</label>
    <input type="text" name="name" value="<?php if(isset($category->name)): ?><?php echo e($category->name); ?><?php else: ?><?php echo e(old('name')); ?><?php endif; ?>" class="form-control" placeholder="Enter Category Name" required/>
</div>
<div class="form-group">
    <label for="exampleTextarea1">Discription</label>
    <textarea class="form-control" id="exampleTextarea1" name="description" rows="4"><?php if(isset($category->description)): ?><?php echo e($category->description); ?><?php else: ?><?php echo e(old('description')); ?><?php endif; ?></textarea>
</div>
<button type="submit" class="btn btn-primary mr-2">Submit</button>
<a class="btn btn-light" href="<?php echo e(route('categories.index')); ?>">Cancel</a><?php /**PATH E:\projects\bsaloon\resources\views/categories/category_form.blade.php ENDPATH**/ ?>