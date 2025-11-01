<?php $__env->startSection('pagescript'); ?>
<script src="<?php echo e(asset('js/form-addons.js')); ?>"></script>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('title', 'E-Mail Compaign | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<style>
  .hide
{
 display:none;
}
.loader{
    left:0;
    right:0;
    z-index: 999;
    position: fixed;
    border:none;
    width:100%;
    top:200px;
    
}

</style>
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Multiple Email Send
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Send Mails</li>
                </ol>
            </nav>
        </div>
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

        <?php if(Session::has('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo e(Session::get('success')); ?>

        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">E-Mails Send Form <?php echo date("Y-m-d h:i A"); ?></h4>
                        <div class="row">
                        <form class="form-inline" method="post" action="<?php echo e(url('customer-email-filter')); ?>">
                        <?php echo csrf_field(); ?>
                            <!-- <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Select Post Code</label>
                                    <select id="service_id" name="zip[]" class="form-control js-example-basic-multiple w-100" multiple="multiple">
                                        
                                        <?php $__currentLoopData = $zipcode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->zip); ?>"><?php echo e($data->zip); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div> -->
                            <!-- <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Select Referred by</label>

                                    <select id="id" name="referred_by[]" class="form-control js-example-basic-multiple w-100" multiple="multiple">
                                    <option value="Google">Google</option>
                                    <option value="Leaflet">Leaflet</option>
                                    <option value="Recommendation">Recommendation</option>
                                    <option value="Repeat client">Repeat client</option>
                                    <option value="Seen van">Seen van</option>
                                    <option value="Paper advert">Paper advert</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Instagram">Instagram</option>
                                    <option value="Postcard">Postcard</option>
                                    <option value="Email">Email</option>
                                    <option value="SMS">SMS</option>
                                    <option value="AdWords">AdWords</option>
                                    <option value="Magazine">Magazine</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Select Services</label>

                                    <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple">
                                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group mb-2 mr-sm-2">
                                    <label>Start Date</label>
                                    <div id="datepicker-popup-new" class="input-group date datepicker">
                                        <input type="text" name="start_date" placeholder=" Start Date" class="form-control"/>
                                        <span class="input-group-addon input-group-append border-left">
                                            <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group mb-2 mr-sm-2">
                                    <label>End Date</label>
                                    <div id="datepicker-popup-new2" class="input-group date datepicker">
                                        <input type="text" name="end_date" placeholder="Start Date" class="form-control"/>
                                        <span class="input-group-addon input-group-append border-left">
                                            <span class="far fa-calendar input-group-text"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <button type="submit" value="submit" class="btn btn-primary mb-2">Search</button>
                                </div>
                            </div>
                        </form>  
                        </div>
                        <br>
                        <!-- <div   class="form-check form-check-flat form-check-primary mt-2 mr-5" style="float: left;">
                                <label class="form-check-label">
                                <input id="select-all" type="checkbox" class="form-check-input" name="customer-check">
                                    Select All
                                <i class="input-helper"></i></label>
                             </div> -->
                        <table id="customer-listing" class="table table-bordered  table-hover">
                                     
                             <button style="float: right;" type="button" class="btn btn-primary mb-5 mr-5" id="message">Send Emails</button>                
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th><i class="fa fa-check"></i></th>
                                        <th>Name</th>
                                        <th>Email#</th>
                                        <th>Phone</th>
                                        <th>Date of Birth</th>
                                        <th>Referred By:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($customer_detail): ?>
                                        <?php $__currentLoopData = $customer_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <tr>    
                                            <td>
                                                <div class="form-check form-check-flat form-check-primary">
                                                
                                                <label class="form-check-label">
                                                    <input value="<?php echo e($data->email); ?>" data-customer="<?php echo e($data->id); ?>" name="customer-checkbox" id="custom-check" type="checkbox" class="form-check-input custom-check">
                                                    <i class="input-helper"></i></label>
                                                </div>
                                            
                                                </td>
                                                
                                            <td><?php echo e($data->first_name); ?> <?php echo e($data->last_name); ?></td>
                                            <td><?php echo e($data->email); ?></td>
                                            <td><?php echo e($data->primary_phone); ?></td>
                                            <td><?php echo e($data->date_of_birth); ?></td>
                                            <td><?php echo e($data->referred_by); ?></td>
                                    
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                    <tr><td>No data found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document" style="max-width:60%;">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Email Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="forms-sample" action="<?php echo e(url('send-mailing')); ?>" method="post" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="emails" id="customers_emails" value="">
                        <input type="hidden" name="customerids" id="customerids" value="">
                        <div class="row">

                            <!-- <div class="form-group col-md-4">
                                <div class="form-check form-check-flat form-check-primary">                              
                                    <label class="form-check-label"> Click If you want to schedule Email
                                    <input id="check_schedule" name="schedule" type="checkbox" class="form-check-input">
                                        <i class="input-helper"></i></label>
                                </div>
                            </div> -->
                            
                            <!-- <div class="form-group col-md-4">
                            <div class="form-check form-check-flat form-check-primary">                              
                                <label class="form-check-label"> Insert Opt Out Link
                                <input name="optout" type="checkbox" id="optout_link" class="form-check-input">
                                    <i class="input-helper"></i></label>
                            </div>
                        </div> -->
                        </div>
                        
                        <div class="hide" id="schedule_time_div">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Schedule Date</label>
                                    <div id="datepicker-popup-booking" class="input-group date datepicker">
                                        <input type="text" id="schedule_d" name="schedule_date" value="" class="form-control">
                                        <div class="input-group-addon input-group-append"><i class="far fa-calendar input-group-text"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Schedule Time:</label>
                                    <div class="input-group date" id="timepicker-schedule" data-target-input="nearest">
                                        <div class="input-group" data-target="#timepicker-schedule" data-toggle="datetimepicker">
                                            <input type="text" name="schedule_time" id="schedule_t" value="" class="form-control form-control-sm datetimepicker-input" data-target="#timepicker-schedule"/>
                                            <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dynamic-template">
                            <div class="form-group">
                                <label>Select Template Title</label>
                                <select id="template" name="template_id" id="templates" onchange="templatepreview(this.value);" class="form-control">
                                    <option value="">Select Template Type</option>
                                    <?php $__currentLoopData = $templates_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data['id']); ?>">
                                    <?php echo e($data['title']); ?>

                                    </option>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="card" id="upload_pdf">
                                    <div class="card-body">
                                        <h4 class="card-title d-flex">Preivew</h4>
                                        <div id="preview-template" style="width:50% heigt:50%;">
                                        </div>
                                
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2 mt-3">Send Mails</button>
                    </div>
                </div>
                <!-- <a class="btn btn-light" href="#">Cancel</a> id="summernoteExample" -->
            </form>  
        </div>
    </div>
</div>
<script>
function templatepreview(templateid){
    if(templateid!=""){
        $.ajax({
        url: "<?php echo e(url('get-template-preview')); ?>/"+templateid,
        type: 'GET',
        success: function(result) {
            $("#preview-template").html(result);
        }
        });
    }
    else{
        showDangerToast('Please Select Template');
    }
  
}
$(document).ready(function() {
    $("#message").click(function() {
        if ($('.custom-check').is(":checked"))
        {
            var emails = [];
            var ids = [];
            $('#message-modal').modal('toggle');
            $("input:checkbox[name=customer-checkbox]:checked").each(function(){
                
                emails.push($(this).val());
                ids.push($(this).attr('data-customer'));
            });
            $("#customers_emails").val(emails);
            $("#customerids").val(ids);
        }
        else{
            showDangerToast('Please Select Customer');
            // alert("Please Select Customer.");
        }
      
    });

    $("#select-all").click(function() {
        var checkBoxes = $('.custom-check');
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });

    $("#check_schedule").click(function() {
      if ($('#check_schedule').is(":checked"))
      {
        $('#schedule_time_div').removeClass("hide");
        $('#schedule_t').prop('required',true);
        $('#schedule_d').prop('required',true);
      }
      else{
        $('#schedule_time_div').addClass("hide");
        $('#schedule_t').prop('required',false);
        $('#schedule_d').prop('required',false);
      }
  });
  
});

</script>

<script>
$(document).ready(function() {
    const maxAllowed = 20;

    $(document).on('change', '.custom-check', function() {
        const checkedCount = $('.custom-check:checked').length;

        if (checkedCount > maxAllowed) {
            // Uncheck the current one
            $(this).prop('checked', false);
            alert('You can select a maximum of ' + maxAllowed + ' customers only.');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/power_mailing/create.blade.php ENDPATH**/ ?>