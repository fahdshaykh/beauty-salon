







<?php $__env->startSection('title', 'Create Booking | '.Session::get('site_name')); ?>







<?php $__env->startSection('content'); ?>





<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Create Booking



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="<?php echo e(route('service-bookings.index')); ?>">Service Booking</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Create Booking</li>



                </ol>



            </nav>



        </div>



        <?php if(Session::has('success')): ?>



            <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                <?php echo e(Session::get('success')); ?>




            </div>



        <?php endif; ?>



        <?php if(Session::has('error')): ?>



            <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



                <?php echo e(Session::get('error')); ?>




            </div>



        <?php endif; ?>



        <div class="row">



            <div class="col-12 grid-margin">



                <div class="card">



                    <div class="card-body">



                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">Bookings Form</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center" style="float: center;">



                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#customerModal">Add New Customer</button>



                            </div>



                        </div>



                        </div>







                        <form class="forms-sample" action="<?php echo e(route('service-bookings.store')); ?>" method="post">



                            <?php echo csrf_field(); ?>



                            <?php echo $__env->make('service_bookings.booking_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



                        </form>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>







<script>



    $(document).ready(function(){



        var date = '<?php echo $date; ?>';

        var url = window.location.pathname;

        var urlsplit = url.split("/");

        var lastpart = urlsplit[urlsplit.length-1];

        if (date) {

            if(lastpart == 'success'){

            }else {

                $("#customerModal").modal('show');

            }

        }



    });



</script>







<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">



    <div class="modal-dialog modal-lg" role="document">



      <div class="modal-content">



        <div class="modal-header">



          <h5 class="modal-title" id="customerModalLabel">Adding New Customer</h5>



          <button type="button" class="close" data-dismiss="modal" aria-label="Close">



            <span aria-hidden="true">&times;</span>



          </button>



        </div>



        <div class="modal-body">



            <form class="forms-sample" id="signupForm" action="<?php echo e(route('customers.store')); ?>" method="post">



            <div class="row">



            <input type="hidden" name="now_date" value="<?php echo e($date); ?>" class="form-control" />



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">First Name</label>



                        <div class="col-sm-9">



                            <input type="text" id="first_name" name="first_name" value="" class="form-control" />



                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Last Name</label>



                        <div class="col-sm-9">



                            <input type="text" id="last_name" name="last_name" value="" class="form-control" />



                        </div>



                    </div>



                </div>



            </div>



            <div class="row">



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Mobile</label>



                        <div class="col-sm-9">



                            <input type="text" id="primary_phone" name="primary_phone" value="" class="form-control" />



                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Telephone</label>



                        <div class="col-sm-9">



                            <input type="text" name="secondary_phone" value="" class="form-control"  />



                        </div>



                    </div>



                </div>



            </div>

            <input type="hidden" id="latitude" value="" name="latitude">

<input type="hidden" id="longitude" value="" name="longitude">

            <div class="row">



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Email</label>



                        <div class="col-sm-9">



                            <input type="email" name="email" value="" class="form-control" />



                        </div>



                    </div>



                </div>



                <div class="col-md-6">



                    <div class="form-group row">



                        <label class="col-sm-3 col-form-label">Postcode</label>



                        <div class="col-sm-9" id="lookup_field">



                            <!-- <input type="text" name="zip" value="" class="form-control" /> -->



                        </div>



                    </div>



                </div>



            </div>



            <div class="form-group">



                <label for="exampleTextarea1">Address</label>



                <textarea class="form-control" id="addresstextarea" name="address" rows="4"></textarea>



            </div>



            <!-- <div class="form-group">



                <label for="referred_by">Referred By</label>



                <input id="tags" name="referred_by" class="form-control" value="other" />



            </div> -->

            <div class="form-group">



                      <label>Referred By</label>

                      <div class="col-sm-9">

                      <select id="tags" name="referred_by" class="js-example-basic-single w-100">

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



            <div class="modal-footer">



                <input type="submit" class="btn btn-primary mr-2" value="Submit">



                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>



            </div>



            <?php echo csrf_field(); ?>



            </form>



        </div>



        </div>



      </div>



</div>



<script>

$('#lookup_field').setupPostcodeLookup({

    api_key: 'ak_kr1v46iv0aNNht6IWqYTiNYN2lxLb',

    output_fields: {

        line_1: '#first_line',

        line_2: '#second_line',

        line_3: '#third_line',

        post_town: '#post_town',

        postcode: '#postcode'

    }

});

function updateAddress(elem){

    var optionsText = elem.options[elem.selectedIndex].text;



    $("#addresstextarea").val(optionsText);

    const myArr = elem.value.split(",");

    $('#latitude').val(myArr[0]);

    $('#longitude').val(myArr[1]);

}



</script>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\projects\bsaloon\resources\views/service_bookings/create.blade.php ENDPATH**/ ?>