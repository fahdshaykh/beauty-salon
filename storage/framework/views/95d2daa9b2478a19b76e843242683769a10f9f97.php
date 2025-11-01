

<?php $__env->startSection('title', 'Create Customer | ' . Session::get('site_name')); ?>

<?php $__env->startSection('pagescript'); ?>
    <script src="<?php echo e(asset('js/select2.js')); ?>"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Customer</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('customers.index')); ?>">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Customer</li>
                </ol>
            </nav>
        </div>

        <?php if(Session::has('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <?php echo e(Session::get('error')); ?>

            </div>
        <?php endif; ?>

        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            Upload Excel File
        </button> -->

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Customer Form</h4>
                        <form class="forms-sample" id="signupForm" action="<?php echo e(route('customers.store')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <?php echo $__env->make('customers.customer_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Upload Customers Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                    <div class="modal-body">
                        <form id="importForm" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="file" class="form-label">Select Excel File (XLS/XLSX)</label>
                                <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                            </div>
                            <div id="uploadStatus" class="text-center mb-3" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Uploading...</p>
                            </div>
                            <div id="resultMessage" class="alert alert-success" style="display: none;"></div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Placeholder -->
        <div id="alertPlaceholder"></div>
    </div>
</div>

<!-- Inline JavaScript for modal functionality -->
<script>
    (function($) {
        $(document).ready(function() {
            // Handle form submission
            $('#importForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = $('#submitBtn');
                const uploadStatus = $('#uploadStatus');
                const resultMessage = $('#resultMessage');

                // Hide button and show loading status
                submitBtn.hide();
                uploadStatus.show();
                resultMessage.hide();

                $.ajax({
                    url: '<?php echo e(route("customers.import")); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            resultMessage.text(response.message).show();
                            setTimeout(function() {
                                $('#importModal').modal('hide');
                                resultMessage.hide();
                            }, 2000); // Display message for 2 seconds before closing
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'An error occurred.';
                        resultMessage.removeClass('alert-success').addClass('alert-danger').text(message).show();
                    },
                    complete: function() {
                        // Reset UI
                        uploadStatus.hide();
                        if (!resultMessage.is(':visible')) {
                            submitBtn.show();
                        }
                    }
                });
            });

            // Function to show alert
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alertPlaceholder').html(alertHtml).find('.alert').alert();
                setTimeout(() => $('#alertPlaceholder .alert').alert('close'), 5000);
            }
        });
    })(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/customers/create.blade.php ENDPATH**/ ?>