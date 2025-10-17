@extends('layouts.admin')

@section('title', 'Markeeting | Dirtbusters')



@section('content')

<div class="main-panel">

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

            Markeeting



            </h3>

          </div>

          <div class="alert alert-success" style="display:none;" id="status-success"></div>

          @if(Session::has('success'))



<div class="alert alert-success alert-dismissible">



    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



    {{Session::get('success')}}



</div>







@elseif(Session::has('destroy'))



<div class="alert alert-danger alert-dismissible">



    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



    {{Session::get('destroy')}}



</div>



@endif







@if(Session::has('update'))



<div class="alert alert-success alert-dismissible">



    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>


fas fa-dollar-sign
    {{Session::get('update')}}



</div>



@endif

          

  

               

                <div class="row">

                

                    <div class="col-6">

                        <div class="card">



                            <div class="card-body">

                               

                                <h4 class="card-title"></h4>

                        <form class="forms-sample" action="" method="post">

                            @csrf

                              <div class="form-group">

                                <label>Select customer</label>

                                <select id="markeeting_id" name="markeeting_id" class="js-example-basic-single w-100" required>

                            

                                    @foreach($customer_data as $customer)

                                      <option value="{{$customer->id}}">{{$customer->first_name}} {{$customer->last_name}}</option>

                                  @endforeach

                                </select>

                              </div>

                              <div class="form-group">

                                <label>Select whatspApp Template</label>

                                <select id="tmplate_id" name="mark_id" class="js-example-basic-single w-100" required>

                                

                                  @foreach($templates_data as $template)

                                    

                                      <option value="{{$template->id}}">{{$template->title}}</option>

                                  @endforeach

                                </select>

                              </div>

                              <!-- <div class="form-group">

                                <label for="exampleInputUsername1">Service Name</label>

                                <input type="text" class="form-control" name="name" id="exampleInputUsername1" placeholder="Enter Service Name" required>

                              </div>

                              <div class="form-group">

                                <div class="input-group">

                                  <div class="input-group-prepend">

                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-pound-sign"></i></span>

                                  </div>

                                  <input type="text" name="price" class="form-control" aria-label="Amount (to the nearest dollar)" required>

                                </div>

                              </div>

                              <div class="form-group">

                                <div class="col" style="margin-left: -8px;">

                                    <input type='text' class="color-picker" name="service_color" value="#ffe74c" />

                                </div>

                              </div>

                              <div class="form-group">

                                  <label for="exampleTextarea1">Customer Message (characters limit 150)</label>

                                  <textarea class="form-control" id="exampleTextarea1" name="customer_message" rows="4" maxlength="150" placeholder="e.g. Dear {customer name}, Your {services} services on {date} has been booked at {time}. For further information please contact us."></textarea>

                              </div> -->

                              <button type="submit" class="btn btn-primary mr-2">Submit</button>

                           

                            </form>



                    </div>



                </div>



            </div>



        </div>



<script>



function changeTemplateStatus(_this, id) {



 



    var status = $(_this).prop('checked') == true ? 1 : 0;



    $.ajax({



        url: "{{url('template-status')}}",



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

@endsection







