



        



        <?php $expense_report_array = array(5,6); ?>

        <?php $expense_report_link = implode(',', $expense_report_array); ?>

        <?php $total_amount = 0; ?>

        <?php $__currentLoopData = $expense_report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php $total_amount = $total_amount + $expense->amount; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>





        <div class="card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <h4 class="card-title">Expense Report</h4>

                    </div>

                    <div class="col-md-6"></div>


                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div class="alert alert-warning mt-3" role="alert" style="text-align:center">

                          Start Date: <strong><?php if($start_date == 'no_start_date'): ?> No Date Selected  <?php else: ?> <?php echo e($start_date); ?> <?php endif; ?></strong>, End Date: <strong><?php if($end_date == 'no_end_date'): ?> No Date Selected <?php else: ?> <?php echo e($end_date); ?> <?php endif; ?></strong>

                      </div>

                      <div style="text-align:center">

                          <p>Grand Total: <i class="fas fa-dollar-sign"></i><?php echo e($total_amount); ?></p>

                      </div>

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Date </th>

                                        <th>Expense Type</th>

                                        <th>Company Name</th>

                                        <th>Amount</th>

                                        <th>Payment Method</th>

                                        <th>Payable</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    <?php $__currentLoopData = $expense_report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <tr>

                                        <td><?php echo e($num); ?></td>

                                        <td><?php echo e($expense->type); ?></td>

                                        <td><?php echo e($expense->company_name); ?></td>

                                        <td><i class="fas fa-dollar-sign"></i><?php echo e($expense->amount); ?></td>

                                        <td><?php echo e($expense->method); ?></td>

                                        <td><?php echo e($expense->payable); ?></td>

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



<script>

function exportReports(_this) {

    let _url = $(_this).data('href');

    var path = <?php echo json_encode(url('/')); ?>


    window.location.href = path+_url;

}

</script><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/company_reports/expense_reports.blade.php ENDPATH**/ ?>