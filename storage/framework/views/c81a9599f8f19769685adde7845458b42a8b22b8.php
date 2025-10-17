<!DOCTYPE html>



<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">



<head>



    <meta charset="utf-8">



    <meta name="viewport" content="width=device-width, initial-scale=1">





    <!-- CSRF Token -->



    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">







    <title>Login | DiaryMaster</title>







    <!-- Scripts -->



    <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>







    <!-- Fonts -->



    <link rel="dns-prefetch" href="//fonts.gstatic.com">



    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">







    <!-- Styles -->



    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">



</head>



<body>



    <div id="app">



        <main>



            <?php echo $__env->yieldContent('content'); ?>



        </main>



        



    </div>



</body>



</html>



<?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/layouts/app.blade.php ENDPATH**/ ?>