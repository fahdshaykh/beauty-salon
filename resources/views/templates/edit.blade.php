@section('pagescript')
<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">
<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>
<script src="{{asset('js/editorDemo.js')}}"></script>
<script src="{{asset('js/file-upload.js')}}"></script>
@stop
@extends('layouts.admin')

@section('title', 'Template | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')
<div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
             Edit  Template
            </h3>
          </div>
          @if ($errors->any())
          <div class="alert alert-danger alert-dismissible">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
          <div class="row">
               
            <div class="col-6">
          <div class="card">

            <div class="card-body">
               
                <h4 class="card-title"></h4>
             
           
                          <!-- Message Form -->
                        <form class="forms-sample" action="{{route('template.update',$template->id)}}" method="post">
                            @csrf
                            <div class="form-group">
                              <label>Template Type</label>
                              <div class="form-group">
                                <select name="template_type" class="form-control form-control-lg">

                                  <option value="{{$template->type}}" selected="selected">
 
                                    @switch($template->type)
                                      @case(1)
                                      SMS
                                      @break
                                      @case(2)
                                      WhatsApp
                                      @break
                                      @case(3)
                                      Email
                                      @break
                                    @endswitch
                                  </option>
                                
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label>Title</label>
                              <input type="text" class="form-control" value="{{ $template->title }}" name="title" id="title_id" placeholder="Enter Title" required>
                            </div>
                               
                                  @switch($template->type)
                                    @case(1)
                                        <div id="message_template">
                                  
                                          <div class="form-group">
                                            <label >Customer Message (characters limit 150)</label> 
          
                                            <textarea  class="form-control" id="message_id" name="description" rows="4" maxlength="150" vlaue="5465456456" placeholder="e.g. Dear {customer name}, Your {services} services on {date} has been booked at {time}. For further information please contact us.">   {{$template->description}}</textarea>
                                        </div>
                                          </div>
                                    @break
                                    @case(2)
                                        <!-- WhatsApp Form -->
                                      <div id="">
                                       
                                      <label>WhatsApp Message <span>(characters limit 240)</span></label>
                                      <textarea id="summernoteHeader" name="description" required="requirde">{{$template->description}}</textarea> 
                                      </div>
                                    @break
                                    @case(3)
                                        
                                         <!-- email Form -->
                                        <div id="">
                                          <div class="form-group">
                                          <label>E-Mail</label>
                                          <textarea id="summernoteFooter" name="description" required>{{$template->description}}</textarea>                                                      
                                          </div> 
                                          </div>
                                    @break
                                  @endswitch


                            

                         
                                  @method('PUT')
                           
                              <button type="submit" class="btn btn-lg btn-primary mt-3">Update</button>
                        </form>
                        

                    </div>

                </div>

            </div>

        </div>
</div>
</div>
    
@endsection




