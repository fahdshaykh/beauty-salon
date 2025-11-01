<?php $__env->startSection('pagescript'); ?>

<link rel="stylesheet" href="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.css')); ?>">

<script src="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.min.js')); ?>"></script>

<script src="<?php echo e(asset('js/editorDemo.js')); ?>"></script>

<?php $__env->stopSection(); ?>







<?php $__env->startSection('title', 'Quotations | '.Session::get('site_name')); ?>



<?php $__env->startSection('content'); ?>



<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All quotations

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('services/index')); ?>">Quotations</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Quotation Listing</li>

                </ol>

            </nav>

        </div>



        <div class="card">

            <div class="card-body">

                <h4 class="card-title"></h4>

                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr>

                                        <th>Sr.No #</th>

                                        <th>E-mail</th>

                                        <th>Send Date</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($num); ?></td>

                                        <td><?php echo e($quote->email); ?></td>

                                        <td><?php echo e(date('m-d-Y', strtotime($quote->created_at))); ?></td>

                                        <td>

                                            <a type="button" data-toggle="modal" onclick="quote_view('<?php echo e($quote->id); ?>')" data-target="#quote-view" data-quoteid="<?php echo e($quote->id); ?>">

                                                <i class="fa fa-eye text-primary"></i>

                                            </a>

                                        </td>

                                    </tr>

                                    <?php $num++; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

<div class="modal fade" tabindex="-1" id="quote-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="exampleModalLabel">Quote Detail</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form>

          <div class="form-group">

            <label for="recipient-name" class="col-form-label">Recipient:</label>

            <input type="text" class="form-control" id="quote-email">

          </div>

          <div class="form-group">

            <textarea id="summernoteExample" name="message"></textarea>

          </div>

        </form>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<?php $__env->stopSection(); ?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>

function quote_view(id)

{

    console.log(id);

    $.ajax({

        url: "<?php echo e(url('quote-view')); ?>/"+id,

        type: 'GET',

        data: {

            id: id

        },

        success: function(result) {

            $("#quote-modal").modal('show');

            $("#quote-email").val(result['0'].email);

            $("#summernoteExample").summernote("code", result['0'].message);

        }

    });



}



</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/quotes/index.blade.php ENDPATH**/ ?>