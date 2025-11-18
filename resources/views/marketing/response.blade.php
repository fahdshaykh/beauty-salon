

        <div class="card">

            <div class="card-body">

                <div class="row">

               

                    <div class="col-12">

                        <div class="table-responsive">

                            <!-- <div   class="form-check form-check-flat form-check-primary mt-2 mr-5" style="float: left;">

                                <label class="form-check-label">

                                <input id="select-all" type="checkbox" class="form-check-input" name="customer-check">

                                    Select All

                                <i class="input-helper"></i></label>

                             </div> -->

                            <form method="POST" action="{{url('send-message')}}"  id="send-mesage">

                                @csrf

                            <table id="customer-listing" class="table table-bordered  table-hover">

                                        

                                             <button style="float: right;" type="button" class="btn btn-primary mb-5 mr-5" id="message">Send Message</button>


                                          

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th><i class="fa fa-check"></i></th>

                                        <th>Name</th>

                                        @if(!empty($all_services_by_customer))

                                        <th>Services</th>

                                        <th>Date</th>

                                        @endif

                                        <th>Phone#</th>

                                        <th>Email</th>

                                        <th>Address</th>

                                        <th>Referred By:</th>

                                       

                                    </tr>

                                </thead>

                                <tbody>

                                   

                                    <input type="hidden" name="type" value="" id="hid_type">

                                    <input type="hidden" name="title" value="" id="hid_title">

                                    <input type="hidden" name="schedule" value="0" id="schedule">

                                    <input type="hidden" name="schedule_time" value="" id="schedule_time">

                                    <input type="hidden" name="schedule_date" value="" id="schedule_date">

                                    @if($customers=="no")

                                    <div class="row">

                                        <div class="col-lg-4">

                                            <div class="alert alert-danger">

                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                                                    No data Found

                                             

                                             </tr>

                                        </div>

                                        </div>

                                  

                                    @elseif($customers=="no-date")

                                    <div class="row">

                                        <div class="col-lg-8">

                                            <div class="alert alert-danger">

                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                                                    Please Select Dates!

                                             

                                             </tr>

                                        </div>

                                        </div>

                                    @elseif($customers!="no")

                                 

                                    @foreach($customers as $data)

                                      

                                    <tr>    

                                        <td><div class="form-check form-check-flat form-check-primary">

                                               

                                            <label class="form-check-label">

                                                <input value="{{$data->id}}" id="custom-check" type="checkbox" class="form-check-input custom-check"  name="phoneumber[]">

                                                <i class="input-helper"></i></label>

                                             </div>

                                           

                                            </td>

                                              

                                        <td>{{$data->first_name}} {{$data->last_name}}</td>

                                        @if(!empty($all_services_by_customer))

                                        <td>

                                        {{$all_services_by_customer[$data->id]}}

                                        </td>

                                        <td>{{$data->created_at}}</td>

                                        @endif



                                   

                                        <td>{{$data->primary_phone}}</td>

                                        <td>{{$data->email}}</td>

                                        <td>{{$data->address}}</td>

                                        <td>{{$data->referred_by}}</td>

                                   

                                    </tr>

                                    @endforeach

                                    @endif

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

</script>