
<div class="row">

    <div class="col-lg-12">

        <div class="card px-2">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4" style="margin-bottom: 0rem;">

                        <a class="navbar-brand brand-logo" href="#"><img src="<?php if($logo): ?><?php echo e(asset('email_logo/'.$logo)); ?> <?php else: ?> <?php echo e(asset('profiles/power_diary.png')); ?> <?php endif; ?>" width="250" height="50" alt="logo"/></a>

                    </div>

                    <div class="col-md-4"></div>

                    <div class="col-md-4">

                        

                    </div>

                </div>

                <?php echo $header; ?>

                <hr>

            </div>

            <div class="container-fluid mt-5 d-flex justify-content-center w-100">

                <?php echo $quote; ?>

            </div>

            <hr>

            <div class="col-md-4"></div>

            <?php echo $footer; ?>

        </div>

    </div>

</div><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/emails/quote.blade.php ENDPATH**/ ?>