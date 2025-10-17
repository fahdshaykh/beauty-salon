<?php $__env->startSection('pagescript'); ?>
<link rel="stylesheet" href="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.css')); ?>">
<script src="<?php echo e(asset('vendors/summernote/dist/summernote-bs4.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/editorDemo.js')); ?>"></script>
<script src="<?php echo e(asset('js/file-upload.js')); ?>"></script>
<script src="//editor.unlayer.com/embed.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', 'Template | B Saloon'); ?>

<?php $__env->startSection('content'); ?>
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
          <button style="float:right" type="button" onclick="getvalue();" class="btn btn-primary mt-3">Save Template</button>
          <div class="form-group">
            <label >Title</label>
            <input type="text" class="form-control" name="title" id="title_id" placeholder="Enter Title" required>
          </div>
          <div id="editor-container" style="height: 720px;"></div>
          <!-- <button style="float:left" type="button" onclick="getvalue();" class="btn btn-primary mt-3">Save Template</button> -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$( document ).ready(function() {
  unlayer.init({
    id: 'editor-container',
    projectId: 59079,
    tools: {
    social: {
      enabled: true
    }
  }
  });

});
function getvalue(){

var title = $('#title_id').val();
if(title==""){
  showDangerToast('Please Enter Title');
  return false;
}

unlayer.exportHtml(function(data) {
   var templatejson = JSON.stringify(data.design); // design json
   var html = data.html; // design html
   console.log(data.design);
    $.ajax({
      url: "<?php echo e(url('email-template-store')); ?>",
      type: "POST",
      data: {title:title, email_des:html, design:templatejson, "_token": "<?php echo e(csrf_token()); ?>"},
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
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/email_templates/create.blade.php ENDPATH**/ ?>