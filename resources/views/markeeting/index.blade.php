

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'send message | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Marketing

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('marketing')}}">marketing</a></li>

                    
                </ol>

            </nav>

        </div>

        @if(Session::has('error'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('error')}}

        </div>

        @endif

        <div class="row">

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Customers </h4>

                <p class="card-description">

                  In order to get customers please select filters

                </p>

                <form class="form-inline"  method="get" id="expense-filters">

                  <div class="form-group mr-5">

                      <select id="zip" name="zip" class="form-control js-example-basic-single w-100" style="width:100%">

                        <option value="no" selected>Select Zip</option>

                        @foreach($zipcode as $data)

                            <option value="{{$data->zip}}">{{$data->zip}}</option>

                        @endforeach

                      </select>

                    </div>

                    <div class="form-group mr-5">

                      <select id="referrence" name="ref" class="form-control js-example-basic-single w-100" style="width:100%">

                        <option value="no" selected>Select Reference</option>

                        @foreach($refby as $data)

                            <option value="{{$data->referred_by}}">{{$data->referred_by}}</option>

                        @endforeach

                      </select>

                    </div>

                    <button type="submit" value="submit" class="btn btn-primary mb-2">Submit</button>


                </form>

              </div>

            </div>

          </div>

        </div>
        <div class="card" id="report">
            
          </div>
    </div>

</div>

<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md" role="document">
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
                                    <option value="" >Select Template Type</option>
                                    @foreach($templates_data as $data)
                                    <option value="{{$data->type}}">
                                      @if($data->type ==1)
                                      {{"SMS"}}
                                      </option>
                                      @elseif($data->type ==2)
                                      {{"WhatsApp"}}
                                      </option>
                                      @elseif($data->type ==3)
                                      {{"E-Mail"}}
                                    </option>
                                    @endif
                                @endforeach
                                  </select>
                              </div>
                              <div class="form-group" id="template_title" >
                              </div>
                              <div class="modal-footer">
                                <button type="submit" id="sub-btn" class="btn btn-success">Submit</button>
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                              </div>
                              
                       
      </div>
      
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    
    $("#expense-filters").submit(function(event){
      event.preventDefault();
      $.ajax({
          url:"{{url('marketing-search')}}",
          type:"GET",
          data: $('form#expense-filters').serialize(),
          success:function(response){
            //console.log(response.view);
            $("#report").empty();
            $("#report").append(response);        
          }
        });  
          
    });
    $( "#template_type" ).change(function () {  
      event.preventDefault();
      var selectedType = $(this).children("option:selected").val();
      if(selectedType==""){
        alert('Please Chose an option')
      }
      else{
      $.ajax({ 
        url:"{{url('template-search')}}",
              type:"GET",
              data: $('select#template_type').serialize(),
              success:function(response){
                $("#template_title").empty();
                if(response){
                $("#template_title").append('<label>Select Template </label><select id="template-tit" name="template_title" class="form-control"></select>');
                $.each(response, function( index, value ) {
                  $("#template-tit").append('<option value="'+value['title']+'">'+value['title']+'</option>');
                });
                }
               
      }});
    
    }
      }); 
});

$(document).ready(function() {
    $('#message-modal').on('hidden.bs.modal', function(){
      $(this).find('form#message_form_id').trigger('reset');
      $("#template_title").empty();
    
      
      
     });
});
$(document).ready(function() {
               $("#sub-btn").click(function() {
                
                var selected = $('#template_type option:selected');
                
                  
                 var selected_t = $('#template_title option:selected');
                    
                $("#hid_type").val(selected.val());
                $("#hid_title").val(selected_t.val());
                if(!confirm("Are you sure about this change?")) {
                            return false;
                }
              $("#send-mesage").submit();
                      
                          
                
            });
          });

</script>

@endsection