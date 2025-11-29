<style>

    .hide

{

   display:none;

}

.loader{

    left:0;

    right:0;

    z-index: 10;

    position: fixed;

    border:none;

    width:100%;

    top:200px;

}

.popup{

    position:absolute;

    top:10%;

    left:30%;

    height:50%;

    width: 50%;

    background:color:#fff;

    padding:15px;

    padding-bottom:50px;

    /* box-shadow: 3px 3px 5px #aaa; */

    display: none;

    background-color: rgba(0, 0, 0, .8);

    z-index: 999;

    border-radius: 25px;

}



.popup img{

    height:100%;

    width:100%;

}

#payment-modal{

      

        z-index: 10;

        position: absolute;

        margin-left: auto;

        margin-right: auto;

        margin-top: auto;

        margin-bottom: auto;

        width: 40%;

        padding-top: 0 !important;



}

#payment-modal .modal-content{

    border:1px solid #392c70;

}

#payment-modal .modal-header{

    padding:0;

    background-color: #392c70;

    color: #fff;

}

#payment-modal .modal-header #modal-close{

    margin-right:10px !important;

    color:#FFF !important;

}

#modal-close span{

    color: #FFF;

}


/* css for cancel modal  */
#cancel-modal{

      

z-index: 10;

position: absolute;

margin-left: auto;

margin-right: auto;

margin-top: auto;

margin-bottom: auto;

width: 40%;

padding-top: 0 !important;



}

#cancel-modal .modal-content{

border:1px solid #392c70;

}

#cancel-modal .modal-header{

padding:0;

background-color: #392c70;

color: #fff;

}

#cancel-modal .modal-header #modal-close{

margin-right:10px !important;

color:#FFF !important;

}
</style>



<center id="loader-modal" class="loader" >

                           

                             <div class="dot-opacity-loader">

                               <span></span>

                               <span></span>

                               <span></span>

                             </div>

                          

                       </center>

<div class="modal-dialog modal-dialog-centered mt-2" tabindex="20" role="document" style="padding: 0;">

  

    <div class="modal-content">

        <div class="modal-header" style="background: #f9f9f9; padding: 0.5em;">

            <p class="our-left-p"></p>

            <p class="modal-title" id="exampleModalLongTitle">

                <?php echo date('d-m-Y', strtotime($booking_detail['due_date'])); $booking_detail['due_date']; ?>

                <span style="margin-left: 2em;"><?php echo $booking_detail['due_time']; ?></span><span style="margin-left: 2em;">Finish Time: <?php echo $booking_detail['finish_time']; ?> Minutes</span>

            </p>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: left; width: 34%; text-align: right;"><span aria-hidden="true">&times;</span></button>

        </div>

        <div class="modal-body modal-body-desktop modal-body-ipad modal-body-mobile our-modal">

            <div class="container">

                <div class="row">

                    <div class="col-md-6">

                        <div class="card our-card" style="background: #b1b1b112; padding: 0; height: 247px; margin: 0;">

                            <div class="card-body our-card-body">

                                <div class="row">

                                    <div class="col-6">

                                        <b>Name:</b> <span class="customer-data"><?php echo $booking_detail['customer_name']; ?></span>

                                    </div>

                                    <div class="col-6">

                                        <b>Mobile Number:</b> <span class="customer-data"><?php echo $booking_detail['customer_mobile']; ?></span>

                                    </div>

                                </div>

                                <div class="row mt-2">

                                    <div class="col-6">

                                        <b>E-Mail:</b> <span class="customer-data"><?php echo ($booking_detail['customer_email']!="")?$booking_detail['customer_email']:'Not Available'; ?></span>

                                    </div>

                                    <div class="col-6">

                                        <b>Referred By:</b> <span class="customer-data"><?php echo $booking_detail['referred_by']; ?></span>

                                    </div>

                                </div>

                                <div class="row mt-2">

                                    <div class="col-6">

                                        <b>Price:</b> <span class="customer-data">{{ currency_symbol() }}<?php echo $booking_detail['total_price']; ?> </span>

                                    </div>

                                    <div class="col-6">

                                        <b>Services:</b> <span class="customer-data"><?php echo $booking_detail['services']; ?></span>

                                    </div>


                                </div>

                                <div class="row mt-2">

                                    <div class="col-12">

                                        <b>Comments:</b> <span class="customer-data"><?php echo $booking_detail['comments']; ?></span>

                                    </div>

                                </div>

                                 



                            </div>

                        </div>

                    </div>

                    <div class="popup">

                    <button type="button" class="close" style="color:white;" onclick="hide_image();"><span aria-hidden="true">&times;</span></button>

                        <img src="" alt="" onclick="hide_image();"/>

                    </div>  

                    <div class="col-lg-6">

                        <div class="card">

                            <div class="card-body">

                                <h4 class="card-title">Job Notes</h4>
                                  

                                @can('calendar view job notes')
                                <div id="jsGrid"></div>
                                @endcan

                            </div>

                        </div>

                    </div>

                </div>

                <br />

               

                <div class="row">

                    <div class="col-lg-6 our-grid-margin our-map section">

                        <!-- <div class="card our-card">

                            <div class="card-body our-card-body">

                                <h6 class="card-title our-card-title">Map</h6>

                                <div class="job-map-container">

                                    <iframe

                                        src="https://maps.google.co.in/maps?q=<?php echo urlencode($booking_detail['customer_address']); ?>&output=embed"

                                        height="150"

                                        style="border: 0; width: 100%;"

                                        allowfullscreen=""

                                        loading="lazy"

                                    ></iframe>

                                </div>

                            </div>

                        </div> -->

                        <div class="card our-card" style="background: #b1b1b12e; height: 237px;">

                        <center><b style="margin-right: 20px;">Customer History</b></center>



                        <table class="table table-bordered table-responsive">

                            <tr>

                                <th>

                                    Employee Name

                                </th>

                                <th>

                                    Due Date

                                </th>

                                <th>

                                    services

                                </th>

                                <th>

                                    Comment

                                </th>

                                <th>

                                    Price

                                </th>

                            </tr>

                            <?php  foreach($alljobs_customer as $alljobs){ ?>

                                <tr>

                                    <td>

                                    <?php echo $alljobs->user->name; ?>

                                    </td>

                                    <td>

                                    <?php echo $alljobs->due_date ?>

                                    </td>

                                    <td>

                                    <?php foreach($customerjobs_services[$alljobs->id] as $jobs) { echo ' '.$jobs.' , '; } ?>

                                    </td>

                                    <td>

                                    <?php echo $alljobs->comment ?>

                                    </td>

                                    <td>

                                    <?php echo currency_symbol().$alljobs->total_price ?>

                                    </td>

                                </tr>

                                <?php } ?>



                        </table>

                        </div>

                    </div> 

                    <div class="col-md-6 our-grid-margin first-two-btns paid-cash-section-desktop paid-cash-section-ipad paid-cash-section-mobile">

                        <div class="card our-card" style="background: #b1b1b112;">

                            <div class="card-body our-card-body">

                                <div class="paid-cash-div-desktop paid-cash-div-ipad paid-cash-div-mobile">

                                    <p style="padding-top: 0;"><?php if($booking_detail['payment_instrument']!=""){ ?>Paid by <?php echo $booking_detail['payment_instrument']; } else{ echo "Payment is Pending"; } ?></p>

                                    <!-- <div style="text-align: center; margin-bottom: 10px;">

                                    <?php if($booking_detail['customer_email']!=""){ ?>

                                        <a style="color: #fff;text-decoration: none;" href="javascript:void(0);" onclick="sendInvoiceEmail('<?php echo $booking_detail['id']; ?>');">  <button  class="btn first-two-btns"><i class="fab fa-google"></i> Email this customer invoice</a>

                                   <?php } else { ?>

                                        <a style="color: #fff;text-decoration: none;" href="javascript:void(0);" onclick="sendInvoiceSMS('<?php echo $booking_detail['id']; ?>','<?php echo $booking_detail['customer_mobile']; ?>');">  <button  class="btn first-two-btns"><i class="fa fa-envelope"></i> SMS this customer invoice</a>

                                   <?php } ?>

                                    </div> -->

                                    <!-- <div style="text-align: center;">

                                     <button class="btn first-two-btns" onclick="sendReviewSms('<?php echo $booking_detail['customer_mobile']; ?>');"><i class="fa fa-envelope"></i> SMS customers Review request</button>

                                    </div> -->

                                </div>

                            </div>

                            <div class="row last-three-btns  last-three-btns-mobile" style="margin-top: 10px;">

                            
                                <div class="col-lg-12 col-md-12 col-sm-12 ipad-pro">

                                    <ul>

                                    @can('calendar update booking status')
                                        <li>

                                          

                                        <?php $status = array('1' => 'Booked', '2' => 'In-Progress', '3' => 'Completed', '4' => 'Cancel'); ?>

                                           <select style="background-color:#004cd6;color: #fff;width: 100px;

                                           height: 38px;" class="form-control form-control-sm" id="change-status" data-booking_id="{{$booking_detail['id']}}" onchange="changeBookingStatus(this);">

                                            @foreach($status as $statusKey => $check)

                                                <option value="{{$statusKey}}" @if($statusKey == $booking_detail['service_status']) selected @endif >{{$check}}</option>

                                            @endforeach

                                            </select>

                                       

                                        </li>
                                        @endcan

                                        @can('calendar view invoice')
                                        <li>

                                        

                                        <a target="_blank" href="{{url('view-booking',$booking_detail['id'])}}"><button class="btn"><i class="fas fa-eye btn-icon-prepend"></i> View Invoice</button></a>

                                        </li>
                                        @endcan

                                        @can('calendar update booking')
                                        <li>

                                        <a href="{{url('service-bookings/edit',$booking_detail['id'])}}"><button class="btn"><i class="fas fa-redo btn-icon-prepend"></i> Reschedule booking</button></a>

                                        </li>
                                        @endcan

                                    </ul>

                                </div>



                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="CancelModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CancelModalLabel">Cancel Booking</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <form id="cancel-form" method="POST" action="{{ route('service-bookings.cancel') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cancel-reason">Reason for Cancellation</label>
                            <textarea class="form-control" id="cancel-reason" name="reason" required></textarea>
                        </div>
                        <input type="hidden" id="cancel-booking-id" name="booking_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" id="cancel-modal-close">Close</button>
                        <input type="submit" class="btn btn-danger" value="Submit Cancellation">
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade " id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="ModalLabel">Payment Method</h5>

                <button type="button" class="close" id="modal-close" >

                <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <form method="post" id="payment-form">

            <div class="modal-body">

                

                <div class="form-group">

                    <select class="form-control form-control-lg" name="payment_method" id="payment_method" required>

                        <option value="">Select Payment Method</option>

                        <option value="cash">Cash</option>

                        <option value="paypal">PayPal</option>

                        <!-- <option value="card">Card</option>

                        <option value="invoice">Invoice</option>

                        <option value="cheque">Cheque</option>

                        <option value="bacs">BACS</option> -->

                    </select>

                </div>

                <div class="form-group">

                    <label for="message-text" class="col-form-label cheque_number_label" style="display: none">Cheque Number:</label>

                    <input type="text" class="form-control" id="cheque_number" name="cheque_number" style="display: none">  

                </div>

                <input type="hidden" class="form-control" id="invoice" name="invoice">

                <input type="hidden" class="form-control" id="service_status" name="service_status">

            </div>

            <div class="modal-footer">

                <button type="submit" class="btn btn-success">Submit</button>

                <button type="button" class="btn btn-light" id="payement-modal-close">Close</button>

            </div>

            @csrf

        </form>

            </div>

        </div>

    </div>



                 

                           

                       



<script>

$(document).ready(function(){

    $( document ).ajaxStart(function() {

    $('#loader-modal').removeClass( "hide");

});

$( document ).ajaxStop(function() {

    $('#loader-modal').addClass("hide");

});





});



 function runGrid(){

    $("#jsGrid").jsGrid({

    width: "100%",

    height: "160px",

    inserting: true,

    loadIndication: true,

    loadMessage: 'Loading notes...',

    sorting: true,

    editing: true,

    autoload:true,

    noDataContent: 'No Notes',

    rowClick: function(args){

        $(".popup img").attr("src", args.item.img);

    $(".popup").animate( { "opacity": "show", top:"100"} , 500 );

        return false;

    },

    fields: [

      { name: "id", title:'ID',  type: "text", css: 'hide' },

      { name: "details", title:'Details',  type: "text", width: 150, validate:"required",editing: true },

      {

        name: "img",

        itemTemplate: function(val, item) {

            console.log(val);

            return $("<img>").attr("src", val).css({ width: 100 }).addClass("image_upload");

        },

        insertTemplate: function() {

            var insertControl = this.insertControl = $("<input>").prop("type", "file");

            return insertControl;

        },

        editTemplate: function() {

             var insertControl = this.insertControl = $("<input>").prop("type", "file");

            return insertControl;

        },

        insertValue: function() {

            //URL.createObjectURL(file)

        //  alert(this.insertControl[0].files[0][name]);

            return this.insertControl[0].files[0]; 

        },

        editValue: function() {

            return this.insertControl[0].files[0]; 

        },

       

        align: "center",

        width: 120

    },

      { name: "bookingid", title:'Bookingid',  type: "text", css: 'hide' },

      { type: "control" }

    ],

    controller:{

        loadData: function(filter){

            var d = $.Deferred();

            $.ajax({

                type:"GET",

                url: 'fetch-job-notes/'+<?php echo $booking_detail['id'];?>,

                dataType: "JSON"

            }).done(function(response) {

                    d.resolve(response);

                });

 

            return d.promise();

            

        },

        insertItem: function(item){

            var d = $.Deferred();

            var formData = new FormData();

            formData.append("_token", "{{ csrf_token() }}");

            formData.append("id", "<?php echo $booking_detail['id'];?>",);

            formData.append("details", item.details);

            if(typeof(item.img) != "undefined"){

                formData.append("img[]", item.img, item.img.name);

            }

           

            $.ajax({

                method: "POST",

                type: "POST",

                url: "{{route('add-job-notes.store')}}",

                data: formData,

                processData: false,

                contentType: false,

            }).done(function(response) {

                $(runGrid);

                });

        },

        updateItem: function(item){

            var d = $.Deferred();

            var formData = new FormData();

            formData.append("_token", "{{ csrf_token() }}");

            formData.append("booking_id", "<?php echo $booking_detail['id'];?>",);

            formData.append("id", item.id);

            formData.append("details", item.details);

            if(typeof(item.img) != "undefined"){

                formData.append("img[]", item.img, item.img.name);

            }

           

            $.ajax({

                method: "POST",

                type: "POST",

                url: "/update-job-notes",

                data: formData,

                processData: false,

                contentType: false,

            }).done(function(response) {

                $(runGrid);

                //d.resolve(response);

            });

           

        },

        deleteItem: function(item){

            return $.ajax({

                type: "GET",

                url: 'delete-notes/'+item.id

            });

        },

      

    },

  });

 }

 $(runGrid);

 $(document).ready(function(){



    $("#payment-form").submit(function (event) {

            event.preventDefault();

            $.ajax({

                url: "{{url('change-booking-status')}}",

                type: "POST",

                data: $("#payment-form").serialize(),

                success: function (response) {

                    if(response.success){

                    showSuccessToast(response.success);

                    $("#payment-modal").modal('hide');

                }

                else{

                    showDangerToast(response.error);

                }

                },

            });

        });



$( "#payement-modal-close" ).click(function() {

    $("#payment-modal").modal('hide');

});

$( "#cancel-modal-close" ).click(function() {

    $("#cancel-modal").modal('hide');

});

$( "#modal-close" ).click(function() {

    $("#payment-modal").modal('hide');

});



    $("#payment_method").on('change',function(){

        var value=$(this).val();

        if(value == 'cheque'){

            $(".cheque_number_label").show();

            $("#cheque_number").show();

        }

        else {

            $(".cheque_number_label").hide();

            $("#cheque_number").hide();

        }

    });

    changeBookingStatus = function(level){

        var status_value = level.value;

        var booking_id = $(level).data('booking_id');

        if(status_value == 3)

        {

            $('#payment-modal').show();

            $("#payment-modal").modal('show');

            $("#invoice").val(booking_id);

            $("#service_status").val(status_value);

            return false;

        }  else if(status_value == 4) {
                    $("#cancel-modal").modal('show');
                    $('#cancel-booking-id').val(booking_id);
                } else {



            $.ajax({

            type: "POST",

            url:"{{url('booking-status')}}",

            data: { id: booking_id,service_status: status_value,_token: "{{ csrf_token() }}"  },

            success: function(response) {

                

                if(response.success){

                    showSuccessToast(response.success);

                }

                else{

                    showDangerToast(response.error);

                }

            }

        });



    }

 

    }

    sendReviewSms = function(mobileNumber){

        $.ajax({

            type: "GET",

            url:"send_review_sms/"+mobileNumber,

            success: function(response) {

                if(response.success){

                    showSuccessToast(response.success);

                }

                else{

                    showDangerToast(response.error);

                }

                

            }

        });

    }



    sendInvoiceEmail = function(invoice){

        $.ajax({

            type: "GET",

            url:"invoice-mail/"+invoice,

            success: function(response) {

                if(response.success){

                    showSuccessToast(response.success);

                }

                else{

                    showDangerToast(response.error);

                }

                

            }

        });

    }



    sendInvoiceSMS = function(invoice,phone){

        $.ajax({

            type: "POST",

            url:"invoice-sms/",

            data: { invoice: invoice,phone: phone,_token: "{{ csrf_token() }}"},

            success: function(response) {

                if(response.success){

                    showSuccessToast(response.success);

                }

                else{

                    showDangerToast(response.error);

                }

                

            }

        });

    }

});

function hide_image(){

    $(".popup").fadeOut(500);

}

</script>

