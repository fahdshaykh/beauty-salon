



 <!-- plugins:css -->



 <link rel="stylesheet" href="../../vendors/iconfonts/font-awesome/css/all.min.css">



  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">



  <!-- endinject -->



  <link rel="shortcut icon" href="../../images/favicon.png" />



<?php $__env->startSection('content'); ?>



<div class="container-scroller">



    <div class="container-fluid page-body-wrapper full-page-wrapper">



      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">



        <div class="row flex-grow">



          <div class="col-lg-6 d-flex align-items-center justify-content-center">



            <div class="auth-form-transparent text-left p-3">



              <div class="brand-logo">



                <img src="../../images/power_diary.png" alt="logo">



              </div>



              <h4><?php echo e(__('Login')); ?></h4>



                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>



                    <div class="alert alert-danger" role="alert">



                        <?php echo e($message); ?>




                        <!-- <h6 class="font-weight-light">Happy to see you again!</h6> -->



                    </div>



                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>



                <?php if(Session::has('inactive')): ?>



                  <div class="alert alert-danger alert-dismissible">



                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                      <?php echo e(Session::get('inactive')); ?>




                  </div>



                <?php endif; ?>



              



              <form class="pt-3" method="POST" action="<?php echo e(route('login')); ?>">



              <?php echo csrf_field(); ?>



                <div class="form-group">



                  <label for="email"><?php echo e(__('E-Mail Address')); ?></label>



                  <div class="input-group">



                    <div class="input-group-prepend bg-transparent">



                      <span class="input-group-text bg-transparent border-right-0">



                        <i class="fa fa-user text-primary"></i>



                      </span>



                    </div>



                    <input type="email"  class="form-control form-control-lg border-left-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="Enter Email" required autocomplete="email" autofocus>



                    



                  </div>



                </div>



                <div class="form-group">



                  <label for="password"><?php echo e(__('Password')); ?></label>



                  <div class="input-group">



                    <div class="input-group-prepend bg-transparent">



                      <span class="input-group-text bg-transparent border-right-0">



                        <i class="fa fa-lock text-primary"></i>



                      </span>



                    </div>



                    <input type="password" class="form-control form-control-lg border-left-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" name="password" required autocomplete="current-password" placeholder="Password">



                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>



                    <div class="alert alert-danger" role="alert">



                        <strong><?php echo e($message); ?></strong>



                    </div>



                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>                        



                  </div>



                </div>



                







                <div class="row">



                  <?php if(Route::has('password.request')): ?>



                      <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">



                          <?php echo e(__('Forgot Your Password?')); ?>




                      </a>



                  <?php endif; ?>



                </div>











                <div class="my-3">



                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">



                LOGIN</button>



                </div>



              </form>



            </div>



          </div>



          <div class="col-lg-6 login-half-bg d-flex flex-row">



            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2022  All rights reserved.</p>



          </div>



        </div>



      </div>



    </div>







</div>



<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/auth/login.blade.php ENDPATH**/ ?>