@section('pagescript')
<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">
<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>
<script src="{{asset('js/editorDemo.js')}}"></script>
<script src="{{asset('js/file-upload.js')}}"></script>
@stop
@extends('layouts.admin')
@section('title', 'Template | B Saloon')

@section('content')
<div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
             Create New Template
            </h3>
          </div>
          <div class="row">
               
            <div class="col-6">
          <div class="card">

            <div class="card-body">
               
                <h4 class="card-title"></h4>
                        <!-- Message Form -->
                        <form class="forms-sample" action="{{route('template.store')}}" method="post" id="">
                            @csrf
                            <div class="form-group">
                              <label>Select Template</label>
                              <div class="form-group">
                                <select name="template_type" class="form-control form-control-lg" id="template_id">
                                  <option value="1" selected="selected">SMS</option>
                                  <!-- <option value="2">WhatsApp</option> -->
                                  <!-- <option value="3">E-Mail</option> -->
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label >Title</label>
                              <input type="text" class="form-control" name="title" id="title_id" placeholder="Enter Title" required>
                            </div>
                            <div id="message_template">
                            
                                <div class="form-group">
                                  <label for="exampleTextarea1">Customer Message (characters limit 150)</label>
                                  <textarea class="form-control" id="message_id" name="message_des" rows="4" maxlength="150" placeholder="e.g. Dear {customer name}, Its been long since you have had any cleaning service. For further information please contact us."></textarea>
                              </div>
                                </div>

                               <!-- WhatsApp Form -->
                               <div id="whatsapp_message">
                               <!-- <div class="alert alert-warning mt-3" role="alert" style="text-align:center">
                                In order to send dynamic message please follow the pattern: <strong>{customer name}</strong> | <strong>{services name}</strong> | <strong>{date}</strong> | <strong>{time}</strong>
                              </div> -->
                              <label>WhatsApp Message <span>(characters limit 240)</span></label>
                              <textarea id="summernoteHeader" name="whatsapp_des" required="requirde"></textarea> 
                              </div>

                                <!-- email Form -->
                              <div id="email_message">
                              <!-- <div class="alert alert-warning mt-3" role="alert" style="text-align:center">
                                In order to send dynamic message please follow the pattern: <strong>{customer name}</strong> | <strong>{services name}</strong> | <strong>{date}</strong> | <strong>{time}</strong>
                              </div> -->
                              <div class="form-group">
                              <label>E-Mail </label>
                              <textarea id="summernoteFooter" name="email_des" required></textarea>                                                      
                              </div> 
                              </div>
                              <button type="submit" class="btn btn-lg btn-primary mt-3">Save Message</button>
                        </form>


                    </div>

                </div>

            </div>

        </div>
</div>
</div>
    
@endsection




