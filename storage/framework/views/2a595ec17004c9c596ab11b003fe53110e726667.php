
        <div class="card">
            <div class="card-body">
                <div class="row">
               
                    <div class="col-12">
                        <div class="table-responsive">
                            <div   class="form-check form-check-flat form-check-primary mt-2 mr-5" style="float: left;">
                                <label class="form-check-label">
                                <input id="select-all" type="checkbox" class="form-check-input" name="customer-check">
                                    Select All
                                <i class="input-helper"></i></label>
                             </div>
                            <form method="POST" action="<?php echo e(url('send-message')); ?>"  id="send-mesage">
                                <?php echo csrf_field(); ?>
                            <table id="customer-listing" class="table table-bordered  table-hover">
                                        <?php if(in_array("16,2", session()->get('all_rp')['0'])): ?>
                                             <button style="float: right;" type="button" class="btn btn-primary mb-5 mr-5" id="message">Send Message</button>
                                             <?php endif; ?>
                                          
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th><i class="fa fa-check"></i></th>
                                        <th>Name</th>
                                        <?php if(!empty($all_services_by_customer)): ?>
                                        <th>Services</th>
                                        <th>Date</th>
                                        <?php endif; ?>
                                        <th>Phone#</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Post Code </th>
                                        <th>Referred By:</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    <input type="hidden" name="type" value="" id="hid_type">
                                    <input type="hidden" name="title" value="" id="hid_title">
                                    <input type="hidden" name="schedule" value="0" id="schedule">
                                    <input type="hidden" name="schedule_time" value="" id="schedule_time">
                                    <input type="hidden" name="schedule_date" value="" id="schedule_date">
                                    <?php if($customers=="no"): ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="alert alert-danger">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                    No data Found
                                             
                                             </tr>
                                        </div>
                                        </div>
                                  
                                    <?php elseif($customers=="no-date"): ?>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="alert alert-danger">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                    Please Select Dates!
                                             
                                             </tr>
                                        </div>
                                        </div>
                                    <?php elseif($customers!="no"): ?>
                                 
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      
                                    <tr>    
                                        <td><div class="form-check form-check-flat form-check-primary">
                                               
                                            <label class="form-check-label">
                                                <input value="<?php echo e($data->id); ?>" id="custom-check" type="checkbox" class="form-check-input custom-check"  name="phoneumber[]">
                                                <i class="input-helper"></i></label>
                                             </div>
                                           
                                            </td>
                                              
                                        <td><?php echo e($data->first_name); ?> <?php echo e($data->last_name); ?></td>
                                        <?php if(!empty($all_services_by_customer)): ?>
                                        <td>
                                        <?php echo e($all_services_by_customer[$data->id]); ?>
                                        </td>
                                        <td><?php echo e($data->created_at); ?></td>
                                        <?php endif; ?>

                                   
                                        <td><?php echo e($data->primary_phone); ?></td>
                                        <td><?php echo e($data->email); ?></td>
                                        <td><?php echo e($data->address); ?></td>
                                        <td><?php echo e($data->zip); ?></td>
                                        <td><?php echo e($data->referred_by); ?></td>
                                   
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
<script>
    $(document).ready(function() {
        $("#select-all").click(function() {
            var checkBoxes = $('.custom-check');
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        });

    });
    $(document).ready(function() {
        $("#message").click(function() {
            if ($('.custom-check').is(":checked"))
                {
                    $('#message-modal').modal('toggle');
                }
                else{
                    alert("Please Select A Customer.");
                }
        });
        $("#postcard").click(function() {
            if ($('.custom-check').is(":checked"))
                {
                    $('#postcard-modal').modal('toggle');
                }
                else{
                    alert("Please Select A Customer.");
                }
        });
    });
</script><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/marketing/response.blade.php ENDPATH**/ ?>