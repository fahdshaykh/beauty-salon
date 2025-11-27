@section('pagescript')

<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">

<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>

<script src="{{asset('js/editorDemo.js')}}"></script>

<script src="{{asset('js/file-upload.js')}}"></script>
<script src="//editor.unlayer.com/embed.js"></script>

@stop

@extends('layouts.admin')

@section('title', 'Template | '. (auth()->user()->companySetting->site_name ?? ''))


@section('content')

<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        Create New Template
      </h3>
    </div>
    <div class="row grid-margin">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Tinymce Editor</h4>
          <button style="float:right" type="button" onclick="getvalue();" class="btn btn-primary mt-3">Save Template</button>
          <div class="form-group">
            <label >Title</label>
            <input type="text" class="form-control" value="{{$template->title}}" name="title" id="title_id" placeholder="Enter Title" required>
          </div>
          <div id="editor-container" style="height: 720px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>

<script>
$( document ).ready(function() {
  var templateid = "{{$template->id}}";
  editor = unlayer.init({
    id: 'editor-container',
    projectId: 59079,
  });
  setTimeout(function() { 
    $.ajax({
      url: "{{url('email-template-json')}}/"+templateid,
      type: "GET",
      success: function (response) {
        var design =  JSON.parse(response);
        unlayer.loadDesign(design);
      }
    }); 
  }, 8000);
});
function getvalue(){
  var templateid = "{{$template->id}}";
  var title = $('#title_id').val();
  unlayer.exportHtml(function(data) {
    var templatejson = JSON.stringify(data.design); // design json
    var html = data.html; // design html
    $.ajax({
        url: "{{url('email-template-update')}}",
        type: "POST",
        data: {id: templateid,title:title, email_des:html, design:templatejson, "_token": "{{ csrf_token() }}"},
        success: function (response) {
          if(response.success){
          showSuccessToast(response.success);
          }
          else{
            showDangerToast(response.error);
          }
        },
      });
  });
}

  

 </script> 

@endsection









