<?php $__env->startSection('pagescript'); ?>



<script src="<?php echo e(asset('js/select2.js')); ?>"></script>



<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>

<?php $__env->stopSection(); ?>  <?php $__env->startSection('title', 'Marketing Section | '.Session::get('site_name')); ?> <?php $__env->startSection('content'); ?>



<!-- partial -->

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

<center id="loader-modal" class="loader hide" >                        

    <div class="dot-opacity-loader">

        <span></span>

        <span></span>

        <span></span>

    </div>

</center>

<div class="main-panel" id="loader-container">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

               SMS Marketing

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?php echo e(url('marketing')); ?>">Marketing</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Send Message</li>

                </ol>

            </nav>

        </div>

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

        <?php endif; ?> <?php if(Session::has('error')): ?>

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            <?php echo e(Session::get('error')); ?>


        </div>

        <?php endif; ?>

        <div class="row">

            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Customers</h4>

                        <form class="form-inline" method="get" id="expense-filters">

                            <div class="row">

                                <div class="col-lg-2">

                                    <div class="form-group">

                                        <label>Select Post Code</label>

                                        <select id="service_id" name="zip[]" class="form-control js-example-basic-multiple w-100" multiple="multiple">

                                            <?php $__currentLoopData = $zipcode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <option value="<?php echo e($data->zip); ?>"><?php echo e($data->zip); ?></option>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </select>

                                    </div>

                                </div>



                                <div class="col-lg-2">

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

                                </div>

                                <div class="col-lg-2">

                                    <div class="form-group">

                                        <label>Select Services</label>



                                        <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple">

                                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </select>

                                    </div>

                                </div>

                                <div class="col-lg-2">

                                    <div class="input-group mb-2 mr-sm-2">

                                        <div id="datepicker-popup-new" class="input-group date datepicker">

                                            <input type="text" name="start_date" placeholder=" Start Date" class="form-control"/>

                                            <span class="input-group-addon input-group-append border-left">

                                                <span class="far fa-calendar input-group-text"></span>

                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-lg-2">

                                    <div class="input-group mb-2 mr-sm-2">

                                        <div id="datepicker-popup-new2" class="input-group date datepicker">

                                            <input type="text" name="end_date" placeholder="Start Date" class="form-control"/>

                                            <span class="input-group-addon input-group-append border-left">

                                                <span class="far fa-calendar input-group-text"></span>

                                            </span>

                                        </div>

                                    </div>

                                </div>

                                

                                <button type="submit" value="submit" class="btn btn-primary mb-2" width="100" height="50">Submit</button>

                               

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <div class="card" id="report"></div>

    </div>

</div>



<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">

    <div class="modal-dialog modal-md" role="document" >

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel">Send Message</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">×</span>

                </button>

            </div>

            <div class="modal-body" id="mar_for_id">

                <div class="form-group">

                    <label>Select Template Type</label>

                    <select id="template_type" name="template_type" class="form-control" required>

                        <option value="">Select Template Type</option>

                        <?php $__currentLoopData = $templates_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <option value="<?php echo e($data->type); ?>">

                            <?php if($data->type ==1): ?> <?php echo e("SMS"); ?> <?php elseif($data->type ==2): ?> <?php echo e("WhatsApp"); ?> <?php elseif($data->type ==3): ?> <?php echo e("E-Mail"); ?>


                        </option>

                        <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div>

                <div class="form-group" id="template_title"></div>

                <div class="form-group">

                    <div class="form-check form-check-flat form-check-primary">                              

                        <label class="form-check-label"> Click If you want to schedule SMS

                        <input id="check_schedule" name="schedule" type="checkbox" class="form-check-input">

                            <i class="input-helper"></i></label>

                    </div>

                </div>

                <div class="hide" id="schedule_time_div">

                    <div class="form-group row">

                        <div class="col">

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

                <div class="modal-footer">

                    <button type="submit" id="sub-btn" class="btn btn-success" disabled>Submit</button>

                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="postcard-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-md" role="document" style="max-width:65%;">

        <div class="modal-content" >

            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel">PostCard Form</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">×</span>

                </button>

            </div>

                <form class="forms-sample" id="postcard-form" action="<?php echo e(url('postcard-send')); ?>" method="post" enctype="multipart/form-data">

                <?php echo csrf_field(); ?>

                    <div class="card">

                        <div class="card-body">

                            <!-- <h4 class="card-title">PostCard Form</h4> -->

                            <?php if($settings->clicksend_api_key=="" || $settings->clicksend_username=="" || $settings->clicksend_password==""): ?>

                            <p class="card-description" style="color:red;">You don't have account on clicksend<a target="_blank" href="https://clicksend.com/?u=267536"> Create my account</a></p>

                            <?php endif; ?>

                            <p class="card-description"><a target="_blank" href="<?php echo e(url('postcard-sample')); ?>">Check Postcard Samples</a></p>

                            <div class="form-group">

                            <label class="form-check-label">Schedule Postcard (optional)</label>

                                <div class="input-group">

                                <div id="datepicker-popup" class="input-group date datepicker">

                                    <input type="text" name="scheduledate" id="scheduledate" class="form-control" placeholder="Don't Enter Previous Date, It will consider now">

                                    <span class="input-group-addon input-group-append border-left">

                                    <span class="far fa-calendar input-group-text"></span>

                                    </span>

                                </div>

                                </div>

                            </div>

                            <div class="form-group">

                            <label class="form-check-label">Custom message (optional)</label>

                                <div class="input-group">

                                    <input type="text" name="custom_message" class="form-control" placeholder="Write Message For Customer" >

                                </div>

                            </div>

                            <div class="form-group">

                                <div class="form-check form-check-flat form-check-primary">

                                                                

                                    <label class="form-check-label"> Click if you have PostCard URLs

                                        

                                    <input id="check_file_url" name="urls" type="checkbox" class="form-check-input" value="1">

                                        <i class="input-helper"></i></label>

                                </div>

                            </div>

                            <div class="hide" id="file_url">

                                <div class="form-group">

                                    <div class="input-group">

                                    <input type="text" id="file_url1" name="file_url1" class="form-control" placeholder="Enter url for pdf file" >

                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="input-group">

                                        <input type="text" name="file_url2" class="form-control" placeholder="Enter second Url if you have fornt & back are different" >

                                    </div>

                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="card col" id="upload_pdf">

                                    <div class="card-body">

                                    <h4 class="card-title d-flex">Upload PostCard (Only Pdf file is accepted)</h4>

                                

                                    <input type="file" name="post_card" id="post_card" class="dropify" required />

                                    </div>

                                    

                                </div>



                                <div class="card col" id="upload_pdf">

                                    <div class="card-body">

                                        <h4 class="card-title d-flex">Preivew</h4>

                                        <iframe id="viewer" frameborder="0" scrolling="no" width="300" height="200"></iframe>

                                

                                    </div>

                                    

                                </div>

                            </div>

                            

                            <input type="hidden" name="customer_ids" id="customer_ids">

                            <p class="card-description">

                                    Instruction for PostCard Template <code><a target="_blank" href="<?php echo e(url('postcard-info')); ?>"><i class="fas fa-info-circle"></i>Click here </a></code>

                                </p>

                                <button type="submit" style="float:right;" class="btn btn-primary mr-2">Submit</button>

                        </div>

                        

                    </div>

                    

                    </div>

                    

                </form>

        </div>

    </div>

</div>

<script>

    $(document).ready(function(){

//         $('.datepicker').datetime({  

//  minDate:new Date(),

//  disabledDates: [new Date()]

// });

        $( document ).ajaxStart(function() {

            $('#loader-modal').removeClass("hide");

            $('#loader-container').addClass("container");

        });

        $( document ).ajaxStop(function() {

            $('#loader-modal').addClass("hide");

            $('#loader-container').removeClass( "container");

        });

    });

    $(document).ready(function () {

        $("#expense-filters").submit(function (event) {

            event.preventDefault();

            $.ajax({

                url: "<?php echo e(url('marketing-search')); ?>",

                type: "GET",

                data: $("form#expense-filters").serialize(),

                success: function (response) {

                    //console.log(response.view);

                    $("#report").empty();

                    $("#report").append(response);

                },

            });

        });

        $("#template_type").change(function () {

            event.preventDefault();

            var selectedType = $(this).children("option:selected").val();

            if (selectedType == "") {

                alert("Please Choose an option");

            } else {

                $.ajax({

                    url: "<?php echo e(url('template-search')); ?>",

                    type: "GET",

                    data: $("select#template_type").serialize(),

                    success: function (response) {

                        $("#template_title").empty();

                        if (response) {

                            $("#sub-btn").removeAttr("disabled")

                            $("#template_title").append('<label>Select Template </label><select id="template-tit" name="template_title" class="form-control"></select>');

                            $("#template-tit").append('<option value="" disabled>Select Title</option>');

                            $.each(response, function (index, value) {

                                $("#template-tit").append('<option value="' + value["id"] + '">' + value["title"] + "</option>");

                            });

                        }

                    },

                });

            }

        });

    });



    $(document).ready(function () {

        $("#message-modal").on("hidden.bs.modal", function () {

            $("select#template_type").val("");

            $("#template_title").empty();

            $("#sub-btn").attr("disabled", true)

        });

    });

    $(document).ready(function () {

        $("#sub-btn").click(function () {

            var selected = $("#template_type option:selected");



            var selected_t = $("#template_title option:selected");



            $("#hid_type").val(selected.val());

            $("#hid_title").val(selected_t.val());

            if ($('#check_schedule').is(":checked"))

            {

                $("#schedule").val(1);

                $("#schedule_time").val($("#schedule_t").val());

                $("#schedule_date").val($("#schedule_d").val());

            } 

            if (!confirm("Please confirm to send message?")) {

                return false;

            }

            $("#send-mesage").submit();

        });

    });



    $(document).ready(function() {

    $("#check_file_url").click(function() {

      if ($('#check_file_url').is(":checked"))

      {

        $('#file_url').removeClass( "hide");

        $('#file_url1').prop('required',true);

        $('#post_card').prop('required',false);

        $('#upload_pdf').addClass("hide");

      }

      else{

        $('#file_url').addClass("hide");

        $('#upload_pdf').removeClass( "hide");

        $('#file_url1').prop('required',false);

        $('#post_card').prop('required',true);

        //   $('#num_days').prop('required',true);

      }

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



  $("#postcard-form").submit(function (event) {

            event.preventDefault();

            var customers = [];

            $("input:checkbox[id=custom-check]:checked").each(function(){

                customers.push($(this).val());

            });

            $("#customer_ids").val(customers);

            var formData = new FormData($(this)[0]);

            $.ajax({

                url: "<?php echo e(url('postcard-send')); ?>",

                type: "POST",

                data: formData,

                processData: false,

                contentType: false,

                async: false,

                success: function (response) {

                    if(response.success){

                    showSuccessToast(response.success);

                        $("#postcard-modal").modal('hide');

                    }

                    else{

                        showDangerToast(response.error);

                    }

                   

                },

            });

        });



}); 



$("#post_card").on("change", function(e){

    var file = e.target.files[0]

	//pdffile=document.getElementById("uploadPDF").files[0];

    var pdffile_url=URL.createObjectURL(file);

    $('#viewer').attr('src',pdffile_url);

});

    

</script>



<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/marketing/index.blade.php ENDPATH**/ ?>