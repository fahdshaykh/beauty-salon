

<?php $__env->startSection('title', 'Template | B Saloon'); ?>



<?php $__env->startSection('content'); ?>

<div class="main-panel">

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

             Templates



            </h3>

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

                    <?php if(in_array("15,2", session()->get('all_rp')['0'])): ?>

                        <a style="float:right;" href="<?php echo e(url('/email-template-create')); ?>" class="btn btn-primary btn-add-template mb-2"> Add new</a>

                        <?php endif; ?>

                        <div class="table-responsive">

                                

                            <table id="order-listing" class="table table-striped table-bordered">



                                <thead>



                                    <tr>

                                    

                                        <th>Title</th>

                                        <th>Status</th>

                                        <th>Action</th>



                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $templates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>

                                            <td>

                                                <?php echo e($templates->title); ?>


                                            </td>

                                          

                                            <td>

                                            <label class="switch">

                                                <input type="checkbox" id="user--<?php echo e($templates->id); ?>"

                                                    onclick="changeTemplateStatus(event.target, <?php echo e($templates->id); ?>)" <?php echo e(($templates->status) ? 'checked' : ''); ?>>



                                                <span class="slider round"></span>



                                            </label>

  

                                        </td>

                                            <td>

                                            <?php if(in_array("15,3", session()->get('all_rp')['0'])): ?>

                                                <a href="<?php echo e(url('/email-template-edit',$templates->id)); ?>">

                                             <i class="fa fa-pencil-alt text-primary"></i></a>
                                             |
                                             <a target="_blank" href="<?php echo e(url('get-template-preview',$templates->id)); ?>">
                                             <i class="fa fa-eye text-primary"></i></a>

                                             <?php endif; ?>

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



<script>

function changeTemplateStatus(_this, id) {

    var status = $(_this).prop('checked') == true ? 1 : 0;

    $.ajax({



        url: "<?php echo e(url('template-status')); ?>",



        type: 'GET',



        data: {



            id: id,



            status: status 



        },



        success: function (result) {



          $("#status-success").show();



          $("#status-success").html(result.success);



          setTimeout(function() {



            $("#status-success").hide()



          }, 3000);



        }



    });



}

</script>

</div>

</div>

<?php $__env->stopSection(); ?>








<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/email_templates/index.blade.php ENDPATH**/ ?>